# TWIPLA PHP SDK

A simple API wrapper for integrating the Analysis as a Service (3AS) APIs provided by TWIPLA

## Getting started

1. [Create an RSA Key Pair (PEM format)](#creating-an-rsa-key-pair)
1. Send the resulting public key (`jwtRS256.key.pub`) to the TWIPLA Dev Team
1. [Install the library](#installation)
1. [Use the SDK instance](#how-to-use-the-library) to interract with the API

## Creating an RSA Key pair

1. Create the keypair: `ssh-keygen -t rsa -b 2048 -m PEM -f jwtRS256.key`
1. Convert the public key to PEM: `openssl rsa -in jwtRS256.key -pubout -outform PEM -out jwtRS256.key.pub`

## Installation

### Composer

#### Install via Composer

```
composer require visitor-analytics/3as-sdk
```

## How to use the library

```php
$visa = new VisitorAnalytics([
     'intp' => [
         'id' => {INTP_ID},
         'privateKey' => {INTP_RS256_PRIVATE_KEY}
     ],
     'env' => 'stage'
]);
```

## Concepts

### Terms

- **INTP (Integration Partner)**\
   The company that is integrating the analytics as a service solution (3AS)
- **STPs (Server Touchpoints)**\
   Credits used to measure data usage for a given website
- **Intpc (INTPC integration partner customer)**\
   One user of the INTP, can have many websites
- **Website**\
   The website where data will be tracked. It has a subscription with a package with a certain limit of STPs.
  This subscription can be upgraded or downgraded.
  When the website is created a tracking code snippet is returned that must be embedded within the websites HTML.
- **Package**\
   A package has a price and contains a certain number of STPs. They are used when upgrading/downgrading the subscription of a website.

### General

Most endpoints that deal with customers or websites support some form of an ID which can be provided and then used for all following requests.

For example creating a new customer with a website requires an `intpCustomerId`|`intpcId` and an `intpWebsiteId`. These must be provided by the INTP and are intended to make integrations easier because there is no need to save any external IDs. Then when getting data about a customer the request is done using the same `intpCustomerId` provided on creation.

### Subscription Types

There are currently **two types of subscription** available:

#### 1. `Website` Subscription

- Applies to a **single website**.
- Created using an **`intp` package**, which defines the subscription plan.
- Can be billed **monthly** or **yearly**.
- Each `website` subscription is tied to an **`intpc`**, which is the entity responsible for creating the website.

#### 2. `Intpc` Subscription

- Covers **one or more websites** under a single subscription.
- Created using an **`intp` package**, which defines the subscription plan.
- Can be billed **monthly** or **yearly**.
- The **touchpoint limit** defined by the package is **shared across all associated websites**.
- The `intpc` can **monitor individual usage** per website, providing detailed insights into how each site consumes touchpoints.
- Ideal for managing multiple websites with a **centralized billing**.

### Example implementation flow

1. Create a new intpc with a website
1. Inject the resulting tracking code in the website's HTML
1. Use the SDK's [generate iframe url](#generate-the-visitoranalytics-dashboard-iframe-url) method to create an url
1. Show an iframe to the user with the url created previously
1. Show a modal to the user to upgrade his subscription
1. Display all the available packages using the SDK
1. After the payment is complete, use the SDK to upgrade the subscription of the website

## Available APIs

- [INTPCs](#intpcs-api)
- [INTPC](#intpc-api)
- [Package](#package-api)
- [Packages](#packages-api)
- [Website](#website-api)
- [Websites](#websites-api)
- [Utils](#utils-api)

### INTPCs API

Integration partners (INTP) are able to get data about their customers (INTPc).

#### Register and start an INTPc level subscription. This will allow subsequently added websites to consume from the same `touchpoint` pool provided by the `package` used during setup.

```php
$visa->intpcs->create([
        'intpCustomerId' => {INTP_CUSTOMER_ID},
        'email' => {INTP_CUSTOMER_EMAIL},
        'packageId' => {PACKAGE_UUID},
        'billingDate' => {ISO_DATE_STRING} (optional, defaults to current time)
        'website' => [
            'intpWebsiteId' => {INTP_WEBSITE_ID},
            'domain' => {INTP_WEBSITE_DOMAIN_URI},
        ]
]);
```

#### Register an INTPc and start a website level subscription. Each added website will have its own subscription.

```php
$visa->intpcs->create([
        'intpCustomerId' => {INTP_CUSTOMER_ID},
        'email' => {INTP_CUSTOMER_EMAIL},
        'website' => [
            'intpWebsiteId' => {INTP_WEBSITE_ID},
            'domain' => {INTP_WEBSITE_DOMAIN_URI},
            'packageId' => {PACKAGE_UUID},
            'billingDate' => {ISO_DATE_STRING} (optional, defaults to current time)
        ]
]);
```

#### Register an INTPc without register a website. Websites can be added further using the [Add Website](#create-a-website-with-its-own-subscription-and-attach-it-to-an-existing-intpc) endpoint.

```php
$visa->intpcs->create([
        'intpCustomerId' => {INTP_CUSTOMER_ID},
        'email' => {INTP_CUSTOMER_EMAIL},
]);
```

#### List all available customers

```php
$visa->intpcs->list();
```

#### Get a single intpc by its INTP given id

```php
$visa->intpcs->getByIntpCustomerId({INTP_CUSTOMER_ID});
```

### INTPC API

#### List all websites belonging to an INTP Customer

```php
$visa->intpc({INTP_CUSTOMER_ID})->listWebsites();
```

#### Delete a Customer belonging to an INTP

```php
$visa->intpc({INTP_CUSTOMER_ID})->delete();
```

#### Generate the VisitorAnalytics Dashboard IFrame Url

This is one of the essential methods to use when using the iframe appoach 3AS.
It creates an URL for a given customer and website combination that shows the TWIPLA dashboard in the theme configured by the INTP.

```php
$visa->intpc({INTP_CUSTOMER_ID})->generateIFrameDashboardUrl({INTP_WEBSITE_ID});
```

### Packages API

An Integration Partner (INTP) is able to get data about their packages

#### List all available packages
Returns all packages available within the company.
Filtering options:
INTP_WEBSITE_ID - Filters the response to return only packages visible to this website — all public packages plus any custom ones assigned to it.
INTP_CUSTOMER_ID - Filters the response to return only packages visible to this customer — all public packages plus any custom ones assigned to it.

```php
$visa->packages->list({INTP_WEBSITE_ID}, {INTP_CUSTOMER_ID});
```

#### Get a single package by ID

```php
$visa->packages->getById({PACKAGE_UUID});
```

#### Create a package

```php
$visa->packages->create([
    'name' => {PACKAGE_NAME},
    'touchpoints' => {TOUCHPOINT_LIMIT},
    'price' => {FLOAT},
    'currency' => {CURRENCY_CODE}, // ex: EUR, USD, RON
    'period' => {PERIOD}, // ex: monthly, yearly
    'visibility' => {'public'|'restricted'}, // controls who can access the package. 'public' packages are available to all clients; 'restricted' packages can be assigned to specific clients only.
]);
```

### Package API

#### An INTP can update its packages

```php
$visa->package({PACKAGE_UUID})->update([
    'name' => {UPDATED_PACKAGE_NAME}
]);
```
#### An INTP can assign a package to a target
Assign a custom package to a specific client {TARGET_ID} - this is a website ID or a customer ID depending on your subscription type.

Note: Only packages with visibility = restricted can be used.

Steps:

    1. Create the customer or website (if they don't already exist). They will initially use the free or unlimited free plan.
    2. Create a custom package with visibility = restricted.
    3. Assign the package to the specific customer or website using this endpoint.
    4. Upgrade the subscription to this custom package.


```php
$visa->package({PACKAGE_UUID})->assign({TARGET_ID});
```

#### An INTP can unassign a package to a target
Unassign a custom package from a specific client {TARGET_ID} - this is a website ID or a customer ID depending on your subscription type.

Note: If an active subscription exists for this package, unassigning it will not cancel the subscription — it will continue to run as normal until the package is explicitly changed.

```php
$visa->package({PACKAGE_UUID})->unassign({TARGET_ID});
```

### Websites API

#### List all websites

```php
$visa->websites->list();
```

#### Get a single website by its INTP given id

```php
$visa->websites->getByIntpWebsiteId({INTP_WEBSITE_ID});
```

#### Create a website with its own subscription and attach it to an existing INTPc

```php
$visa->websites->create([
    'website' => [
        'id' => {INTP_WEBSITE_ID|STRING},
        'domain' => {INTP_WEBSITE_DOMAIN},
        'package' => [
            'id' => {UUID},
            'billingDate' => {ISO_DATE_STRING} (optional, defaults to current time)
        ]
    ],
    'intpc' => [
        'id' => {INTP_CUSTOMER_ID|STRING}
    ],
]);
```

#### Create a website and attach it to an existing INTPc subscription. This website, alongside other pre-existing website will consume `touchpoints` from the same pool.

```php
$visa->websites->create([
    'website' => [
        'id' => {INTP_WEBSITE_ID|STRING},
        'domain' => {INTP_WEBSITE_DOMAIN},
    ],
    'intpc' => [
        'id' => {INTP_CUSTOMER_ID|STRING}
    ],
]);
```

#### Create a website with its own `30 day, unlimited free trial` subscription and attach it to an INTPc. After the 30 day free trial ends, the subscription will be downgraded to the `free` package.

```php
$visa->websites->create([
    'website' => [
        'id' => {INTP_WEBSITE_ID|STRING},
        'domain' => {INTP_WEBSITE_DOMAIN},
    ],
    'intpc' => [
        'id' => {INTP_CUSTOMER_ID|STRING}
    ],
    'opts' => [
        'uft' => true
    ]
]);
```

<br>

### Website API

#### Delete a website by its INTP given id

```php
$visa->website({INTP_WEBSITE_ID})->delete());
```

#### Add a whitelisted domain

```php
visa->website({INTP_WEBSITE_ID})->addWhitelistedDomain(STRING);
```

#### Delete a whitelisted domain

```php
visa->website({INTP_WEBSITE_ID})->deleteWhitelistedDomain(STRING);
```

#### List all whitelisted domains

```php
visa->website({INTP_WEBSITE_ID})->listWhitelistedDomains();
```

#### Create an api key for a website

```php
$visa->website({INTP_WEBSITE_ID})->createApiKey([
    'name' => {STRING}, // Name to identify the API key
    'comment' => {STRING|OPTIONAL}, // Optional description or notes
    'expiresAt' => {ISO_STRING|OPTIONAL} // Expiration timestamp (ISO 8601); unlimited if omitted
]);

[
    'id' => {UUID_STRING}, // Unique ID of the API key
    'name' => {STRING}, // Name of the API key
    'apiKey' => {STRING}, // The actual API key (only returned once — save it immediately!)
    'comment' => {STRING},
    'createdAt' => {ISO_STRING},
    'expiresAt' => {ISO_STRING},
    'intpWebsiteId' => {STRING},
    'intpCustomerId' => {STRING}
]

⚠️ Note: apiKey is only returned at creation time. Make sure to store it securely — it cannot be retrieved again.
```

#### List api keys for a website

```php
$visa->website({INTP_WEBSITE_ID})->listApiKeys();

[
    [
        'id' => {UUID_STRING},
        'name' => {STRING},
        'comment' => {STRING},
        'createdAt' => {ISO_STRING},
        'expiresAt' => {ISO_STRING},
        'intpWebsiteId' => {STRING},
        'intpCustomerId' => {STRING}
    ]
]
```

#### Delete an api key for a website

```php
$visa->website({INTP_WEBSITE_ID})->deleteApiKey({ID});
```

### Website Contributors API

Manage contributors for a website and control their level of access.
Any existing customer can be added as a contributor. If the customer doesn't exist yet, create them first before adding them as a contributor — no website is required at creation time.
Once added, the contributor can access the dashboard normally via the dashboard iframe URL, where the website ID is the ID of the website they contribute to.
If the website selector is enabled within the dashboard, the contributor will also see the website listed there, alongside any websites they own.

Each contributor is assigned one of the following roles:

| Role                             | Constant    | Access                                                                                                                                                                                                           |
| -------------------------------- |-------------| ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Editor**                       | `editor`    | Full edit access, including content updates and structural changes.                                                                                                                                              |
| **Watcher**                      | `watcher`   | View-only access to website data. Cannot make any edits.                                                                                                                                                         |
| **Custom Dashboard Contributor** | `dashboard` | View-only access to custom dashboards explicitly shared with them. No access to any other platform content or settings. Access to specific dashboards is granted by the website owner from within the dashboard. |

---

#### Add contributor for a website
The contributor will gain access to the dashboard with restricted permissions. The level of access and available actions depend on the assigned role.

A contributor can be assigned only one role at a time.

Roles follow a permission hierarchy, where higher-level roles automatically include the permissions granted by lower-level roles.

This endpoint can also be used to update a contributor's role. If the same contributor is added again with a different role, the previous role will be replaced with the new one.

```php
$visa->website({INTP_WEBSITE_ID})->addContributor([
    "intpCustomerId" => "{INTP_CUSTOMER_ID}",
    "role" => "editor"|"watcher"|"dashboard",
]);
```

#### List contributors for a website
List all contributors of a website

```php
$visa->website({INTP_WEBSITE_ID})->listContributors();

[
    "owner" => [
        "intpCustomerId" => "{INTP_CUSTOMER_ID}",
        "email" => "owner_email_string",
    ],
    "contributors" => [
        "editor" => [
            [
            "intpCustomerId" => "{INTP_CUSTOMER_ID}",
            "email" => "editor1_email_string",
            ],
            [
                "intpCustomerId" => "{INTP_CUSTOMER_ID}",
                "email" => "editor2_email_string",
            ],
        ],
        "watcher" => [
            [
                "intpCustomerId" => "{INTP_CUSTOMER_ID}",
                "email" => "watcher_email_string",
            ],
        ],
        "dashboard" => [
            [
                "intpCustomerId" => "{INTP_CUSTOMER_ID}",
                "email" => "dashboard_email_string",
            ],
        ],
    ],
]
```

#### Delete contributor for a website
Remove a contributor from a website.

The contributor will immediately lose access to the dashboard and all associated resources.

```php
$visa->website({INTP_WEBSITE_ID})->deleteContributor({INTP_CUSTOMER_ID});
```

### API for managing a subscription of type `website`

#### Upgrade - immediately applies a higher stp count package to the subscription

```php
$visa->websiteSubscription->upgrade([
    "intpWebsiteId" => {INTP_WEBSITE_ID},
    "packageId" => {PACKAGE_UUID},
    "trial" => {true|false},
    "proRate" => {true|false}
])
```

#### Downgrade - auto-renew the subscription at the end of the current billing interval to a new lower stp count package

```php
$visa->websiteSubscription->downgrade([
    "intpWebsiteId" => {INTP_WEBSITE_ID},
    "packageId" => {PACKAGE_UUID}
])
```

#### Cancel - disable the subscription auto-renewal at the end of the current billing interval

```php
$visa->websiteSubscription->cancel([
    "intpWebsiteId" => {INTP_WEBSITE_ID},
])
```

#### Resume - re-enable the subscription auto-renewal at the end of the current billing interval

```php
$visa->websiteSubscription->resume([
    "intpWebsiteId" => {INTP_WEBSITE_ID},
])
```

#### Deactivate - immediately disables the subscription

```php
$visa->websiteSubscription->deactivate([
    "intpWebsiteId" => {INTP_WEBSITE_ID},
])
```

### API for managing a subscription of type `intpc`

#### Upgrade - immediately applies a higher stp count package to the subscription

```php
$visa->intpcSubscription->upgrade([
    "intpcId" => {INTP_CUSTOMER_ID},
    "packageId" => {PACKAGE_UUID},
    "trial" => {true|false},
    "proRate" => {true|false}
])
```

#### Downgrade - auto-renew the subscription at the end of the current billing interval to a new lower stp count package

```php
$visa->intpcSubscription->downgrade([
    "intpcId" => {INTP_CUSTOMER_ID},
    "packageId" => {PACKAGE_UUID}
])
```

#### Cancel - disable the subscription auto-renewal at the end of the current billing interval

```php
$visa->intpcSubscription->cancel([
    "intpcId" => {INTP_CUSTOMER_ID},
])
```

#### Resume - re-enable the subscription auto-renewal at the end of the current billing interval

```php
$visa->intpcSubscription->resume([
    "intpcId" => {INTP_CUSTOMER_ID},
])
```

#### Deactivate - immediately disables the subscription

```php
$visa->intpcSubscription->deactivate([
    "intpcId" => {INTP_CUSTOMER_ID},
])
```

### Utils API

#### Generate a valid access token for the current INTP configuration.

```php
$visa->auth->generateINTPAccessToken();
```

#### Generate a valid access token for the current INTPc configuration.

```php
$visa->auth->generateINTPcAccessToken({INTP_CUSTOMER_ID});
```

## Dashboard IFrame

The IFrame is one of the main ways a user can interract with the data gathered for his website. The URL of the IFrame is [generated using the SDK](#generate-the-visitoranalytics-dashboard-iframe-url)

The resulting URL can be further enhanced with query parameters:

1. `allowUpgrade=true` - Show upgrade CTAs

Upgrade buttons will be added to the Dashboard for all features that require a certain minimum package.
Once the upgrade button is clicked, the iframe posts a message to the parent frame, containing the following payload:

```javascript
{
  "type": "UPGRADE_BUTTON_CLICKED",
  "data": {
    "intpWebsiteId": "", // string; external website id
    "intpCustomerId": "", // string; customer id
    "packageName": "", // string; current package name
    "packageId": "", // string; current package id
    "inTrial": true|false, // boolean;
    "expiresAt": "", // string; expiry date in ISO 8601 format
    "billingInterval": "monthly"|"yearly" // string;
  }
}
```

## Pagination

`list` methods support pagination options as follows:

```php
$visa->customers->list(['page' => 0, 'pageSize' => 5])
```

If no pagination options are provided, the `pageSize` defaults to 10 items.

The `page` count starts from 0.
