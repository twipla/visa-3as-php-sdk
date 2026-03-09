<?php

namespace Websites;

use PHPUnit\Framework\TestCase;
use Visa\Response;
use Visa\VisaHttpClient;
use Visa\Websites\Contributor;
use Visa\Websites\WebsiteApi;

class WebsiteApiTest extends TestCase
{
    public function testListContributors()
    {
        $response = $this->createMock(Response::class);
        $response->method('getPayload')->willReturn([
            "payload" => [
                "owner" => [
                    "intpCustomerId" => "intpCustomerId_string",
                    "email" => "owner_email_string",
                ],
                "contributors" => [
                    "editor" => [
                        [
                        "intpCustomerId" => "intpCustomerId_string",
                        "email" => "editor1_email_string",
                        ],
                        [
                            "intpCustomerId" => "intpCustomerId_string",
                            "email" => "editor2_email_string",
                        ],
                    ],
                    "watcher" => [
                        [
                            "intpCustomerId" => "intpCustomerId_string",
                            "email" => "watcher_email_string",
                        ],
                    ],
                    "dashboard" => [
                        [
                            "intpCustomerId" => "intpCustomerId_string",
                            "email" => "dashboard_email_string",
                        ],
                    ],
                ],
            ]
        ]);

        $httpClient = $this->createMock(VisaHttpClient::class);
        $httpClient->method('get')
            ->willReturn($response);

        $websiteApi = new WebsiteApi($httpClient);
        $websiteApi->setIntpWebsiteId('123');

        $contributorsList = $websiteApi->listContributors();
        $this->assertIsArray($contributorsList);
        $this->assertInstanceOf(Contributor::class, $contributorsList['owner']);
        $this->assertInstanceOf(Contributor::class, $contributorsList['contributors']['editor'][0]);
        $this->assertInstanceOf(Contributor::class, $contributorsList['contributors']['watcher'][0]);
        $this->assertInstanceOf(Contributor::class, $contributorsList['contributors']['dashboard'][0]);
    }

    public function testListContributors_exception()
    {
        $httpClient = $this->createMock(VisaHttpClient::class);
        $httpClient->expects($this->never())->method('get');

        $websiteApi = new WebsiteApi($httpClient);
        //no intpWebsiteId is set

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Typed property Visa\Websites\WebsiteApi::$intpWebsiteId must not be accessed before initialization');

        $websiteApi->listContributors();
    }

    public function testAddContributor()
    {
        $payload = [
            "intpCustomerId" => "intpCustomerId_string",
            "role" => "editor"
        ];
        $httpResponse = $this->createMock(Response::class);
        $httpClient = $this->createMock(VisaHttpClient::class);
        $httpClient->expects($this->once())
            ->method('post')
            ->with('/v3/3as/websites/123/contributors', $payload)
            ->willReturn($httpResponse);

        $websiteApi = new WebsiteApi($httpClient);
        $websiteApi->setIntpWebsiteId('123');
        $websiteApi->addContributor($payload);
    }

    public function testAddContributor_exceptionWebsiteId()
    {
        $payload = [
            "intpCustomerId" => "intpCustomerId_string",
            "role" => "editor"
        ];
        $httpClient = $this->createMock(VisaHttpClient::class);
        $httpClient->expects($this->never())->method('post');

        $websiteApi = new WebsiteApi($httpClient);
        //no intpWebsiteId is set

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Typed property Visa\Websites\WebsiteApi::$intpWebsiteId must not be accessed before initialization');

        $websiteApi->addContributor($payload);
    }

    public function testAddContributor_exception()
    {
        $httpClient = $this->createMock(VisaHttpClient::class);
        $httpClient->expects($this->never())->method('post');
        $websiteApi = new WebsiteApi($httpClient);
        $websiteApi->setIntpWebsiteId('123');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot add contributor with empty input.');

        $websiteApi->addContributor([]);
    }

    public function testDeleteContributor()
    {
        $httpResponse = $this->createMock(Response::class);
        $httpClient = $this->createMock(VisaHttpClient::class);
        $httpClient->expects($this->once())
            ->method('delete')
            ->with('/v3/3as/websites/123/contributors/456')
        ->willReturn($httpResponse);

        $websiteApi = new WebsiteApi($httpClient);
        $websiteApi->setIntpWebsiteId('123');
        $websiteApi->deleteContributor('456');
    }

    public function testDeleteContributor_exception()
    {
        $httpClient = $this->createMock(VisaHttpClient::class);
        $httpClient->expects($this->never())->method('delete');
        $websiteApi = new WebsiteApi($httpClient);
        $websiteApi->setIntpWebsiteId('123');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot delete contributor for empty intpCustomerId.');

        $websiteApi->deleteContributor('');
    }

    public function testDeleteContributor_exceptionWebsiteId()
    {
        $httpClient = $this->createMock(VisaHttpClient::class);
        $httpClient->expects($this->never())->method('post');

        $websiteApi = new WebsiteApi($httpClient);
        //no intpWebsiteId is set

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Typed property Visa\Websites\WebsiteApi::$intpWebsiteId must not be accessed before initialization');

        $websiteApi->deleteContributor('456');
    }
}
