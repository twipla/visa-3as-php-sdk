<?php

namespace Websites;

use PHPUnit\Framework\TestCase;
use Visa\Response;
use Visa\VisaHttpClient;
use Visa\Websites\Website;
use Visa\Websites\WebsitesApi;

class WebsitesApiTest extends TestCase
{
    public function testListAction()
    {
        $response = $this->createMock(Response::class);
        $response->method('getPayload')->willReturn([
            [
                'id' => '9e595bdc-b79c-4c32-9d2f-80be6a67785c',
                'domain' => 'https://example.io',
                'createdAt' => '2022-06-21T12:05:04+00:00'
            ]
        ]);

        $httpClient = $this->createMock(VisaHttpClient::class);
        $httpClient->method('get')
            ->willReturn($response);

        $websiteApi = new WebsitesApi($httpClient);

        $this->assertIsArray($websiteApi->list());
        $this->assertInstanceOf(Website::class, $websiteApi->list()['items'][0]);
    }

    public function testGetByIdAction()
    {
        $response = $this->createMock(Response::class);
        $response->method('getPayload')->willReturn([
            'id' => '9e595bdc-b79c-4c32-9d2f-80be6a67785c',
            'domain' => 'https://example.io',
            'createdAt' => '2022-06-21T12:05:04+00:00'
        ]);

        $httpClient = $this->createMock(VisaHttpClient::class);
        $httpClient->method('get')
            ->willReturn($response);

        $websiteApi = new WebsitesApi($httpClient);

        $this->assertInstanceOf(
            Website::class,
            $websiteApi->getByIntpWebsiteId('9e595bdc-b79c-4c32-9d2f-80be6a67785c')
        );
    }

    public function testCreateAction()
    {
        $response = $this->createMock(Response::class);
        $response->method('getPayload')->willReturn([
            'id' => '9e595bdc-b79c-4c32-9d2f-80be6a67785c',
            'status' => 'active',
            'intpId' => '9e595bdc-b79c-4c32-9d2f-80be6a67785c',
            'intpWebsiteId' => '9e595bdc-b79c-4c32-9d2f-80be6a67785c',
            'intpCustomerId' => '9e595bdc-b79c-4c32-9d2f-80be6a67785c',
            'domain' => 'https://example.io',
            'packageId' => '497f6eca-6276-4993-bfeb-53cbbbba6f08',
            'packageName' => 'basic',
            'billingInterval' => 'monthly',
            'createdAt' => '2026-06-21T12:05:04+00:00',
            'expiresAt' => '2027-06-21T12:05:04+00:00',
            'stpResetAt' => '2027-06-21T12:05:04+00:00',
        ]);
        $payload = [
            "website" => [
                "id" => "string",
                "domain" => "string",
                "package" => [
                    "id" => "497f6eca-6276-4993-bfeb-53cbbbba6f08",
                    "billingDate" => "2026-06-21T12:05:04+00:00"
                ],
                "privacyLevel" => 0
            ],
            "intpc" => [
                "id" => "9e595bdc-b79c-4c32-9d2f-80be6a67785c"
            ],
            "opts" => [
                "uft" => true
            ]
        ];

        $httpClient = $this->createMock(VisaHttpClient::class);
        $httpClient->method('post')
            ->with('/v3/3as/websites', $payload)
            ->willReturn($response);

        $websiteApi = new WebsitesApi($httpClient);

        $createdWebsite = $websiteApi->create($payload);
        $this->assertInstanceOf(Website::class, $createdWebsite);
        $this->assertEquals('9e595bdc-b79c-4c32-9d2f-80be6a67785c', $createdWebsite->getId());
    }
}
