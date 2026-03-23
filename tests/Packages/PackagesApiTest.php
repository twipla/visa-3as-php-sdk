<?php

declare(strict_types=1);

namespace Packages;

use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Visa\Packages\PackageRestriction;
use Visa\Packages\PackagesApi;
use Visa\Response;
use Visa\VisaHttpClient;
use Visa\Packages\Package;

class PackagesApiTest extends TestCase
{
    /**
     * @throws GuzzleException
     */
    public function testListPackagesAction()
    {
        $response = $this->createMock(Response::class);
        $response->method('getPayload')->willReturn([
            [
                'id' => '5e392d13-5db2-40c0-a3db-03c37ad365b4',
                'name' => 'Basic',
                'touchpoints' => 1500,
                'createdAt' => '2022-06-20T07:48:07+00:00',
                'price' => 100,
                'currency' => 'USD',
                'period' => 'month',
                'intpId' => '9e595bdc-b79c-4c32-9d2f-80be6a67785c',
                'recommended' => true,
                'visibility' => 'public',
                'restrictedTo' => [
                    [
                        'intpCustomerId' => '9e595bdc-b79c-4c32-9d2f-80be6a67785c',
                        'intpWebsiteId' => '9e595bdc-b79c-4c32-9d2f-80be6a67785c'
                    ],
                ],
            ]
        ]);

        $client = $this->createMock(VisaHttpClient::class);
        $client->method('get')
            ->with('/v2/3as/packages')
            ->willReturn($response);

        $packagesApi = new PackagesApi($client);

        $packages = $packagesApi->list();

        $this->assertIsArray($packages);
        $this->assertInstanceOf(Package::class, $packages[0]);
        $this->assertInstanceOf(PackageRestriction::class, $packages[0]->getRestrictedTo()[0]);
    }

    public function testListPackagesWithFilterAction()
    {
        $response = $this->createMock(Response::class);
        $response->method('getPayload')->willReturn([
            [
                'id' => '5e392d13-5db2-40c0-a3db-03c37ad365b4',
                'name' => 'Basic',
                'touchpoints' => 1500,
                'createdAt' => '2022-06-20T07:48:07+00:00'
            ]
        ]);

        $client = $this->createMock(VisaHttpClient::class);
        $client->method('get')
            ->with('/v2/3as/packages?intpWebsiteId=9e595bdc-b79c-4c32-9d2f-80be6a67785c&intpCustomerId=9e595bdc-b79c-4c32-9d2f-80be6a67785c')
            ->willReturn($response);

        $packagesApi = new PackagesApi($client);

        $packages = $packagesApi->list('9e595bdc-b79c-4c32-9d2f-80be6a67785c', '9e595bdc-b79c-4c32-9d2f-80be6a67785c');

        $this->assertIsArray($packages);
        $this->assertInstanceOf(Package::class, $packages[0]);
    }

    public function testListPackagesWithIntpWebsiteIdFilterAction()
    {
        $response = $this->createMock(Response::class);
        $response->method('getPayload')->willReturn([
            [
                'id' => '5e392d13-5db2-40c0-a3db-03c37ad365b4',
                'name' => 'Basic',
                'touchpoints' => 1500,
                'createdAt' => '2022-06-20T07:48:07+00:00'
            ]
        ]);

        $client = $this->createMock(VisaHttpClient::class);
        $client->method('get')
            ->with('/v2/3as/packages?intpWebsiteId=9e595bdc-b79c-4c32-9d2f-80be6a67785c')
            ->willReturn($response);

        $packagesApi = new PackagesApi($client);

        $packages = $packagesApi->list('9e595bdc-b79c-4c32-9d2f-80be6a67785c');

        $this->assertIsArray($packages);
        $this->assertInstanceOf(Package::class, $packages[0]);
    }

    public function testListPackagesWithIntpCustomerIdFilterAction()
    {
        $response = $this->createMock(Response::class);
        $response->method('getPayload')->willReturn([
            [
                'id' => '5e392d13-5db2-40c0-a3db-03c37ad365b4',
                'name' => 'Basic',
                'touchpoints' => 1500,
                'createdAt' => '2022-06-20T07:48:07+00:00'
            ]
        ]);

        $client = $this->createMock(VisaHttpClient::class);
        $client->method('get')
            ->with('/v2/3as/packages?intpCustomerId=9e595bdc-b79c-4c32-9d2f-80be6a67785c')
            ->willReturn($response);

        $packagesApi = new PackagesApi($client);

        $packages = $packagesApi->list(null, '9e595bdc-b79c-4c32-9d2f-80be6a67785c');

        $this->assertIsArray($packages);
        $this->assertInstanceOf(Package::class, $packages[0]);
    }

    public function testGetByIdAction()
    {
        $response = $this->createMock(Response::class);
        $response->method('getPayload')->willReturn(
            [
                'id' => '5e392d13-5db2-40c0-a3db-03c37ad365b4',
                'name' => 'Basic',
                'touchpoints' => 1500,
                'createdAt' => '2022-06-20T07:48:07+00:00'
            ]
        );

        $httpClient = $this->createMock(VisaHttpClient::class);
        $httpClient->method('get')
            ->willReturn($response);

        $packagesApi = new PackagesApi($httpClient);

        $package = $packagesApi->getById('5e392d13-5db2-40c0-a3db-03c37ad365b4');

        $this->assertInstanceOf(Package::class, $package);
    }

    public function testCreateAction()
    {
        $packageData = [
            'name' => 'string',
            'price' => 12.99,
            'currency' => 'EUR',
            'touchpoints' => 0,
            'period' => 'monthly',
            'visibility' => 'public',
        ];

        $response = $this->createMock(Response::class);
        $response->method('getPayload')->willReturn(
            array_merge([
                'id' => '5e392d13-5db2-40c0-a3db-03c37ad365b4',
                'intpId' => '7128840a-5347-49a4-8dfa-9022d8dad21a',
                'createdAt' => '2022-06-20T07:48:07+00:00',
                'recommended' => false,
            ], $packageData)
        );

        $httpClient = $this->createMock(VisaHttpClient::class);
        $httpClient->method('post')
            ->with('/v2/3as/packages', $packageData)
            ->willReturn($response);

        $packagesApi = new PackagesApi($httpClient);

        $package = $packagesApi->create($packageData);

        $this->assertInstanceOf(Package::class, $package);
        $this->assertIsArray($package->getRestrictedTo());
    }
}
