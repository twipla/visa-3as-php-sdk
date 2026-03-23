<?php

declare(strict_types=1);

namespace Packages;

use PHPUnit\Framework\TestCase;
use Visa\Packages\PackageApi;
use Visa\Response;
use Visa\VisaHttpClient;

class PackageApiTest  extends TestCase
{
    public function testAssignAction() {
        $response = $this->createMock(Response::class);
        $response->method('getStatusCode')->willReturn(204);

        $client = $this->createMock(VisaHttpClient::class);
        $client->method('put')
            ->with('/v3/3as/packages/5e392d13-5db2-40c0-a3db-03c37ad365b4/assignments/9e595bdc-b79c-4c32-9d2f-80be6a67785c')
            ->willReturn($response);

        $packageApi = new PackageApi($client);
        $packageApi->setPackageId('5e392d13-5db2-40c0-a3db-03c37ad365b4');

        $packageApi->assign('9e595bdc-b79c-4c32-9d2f-80be6a67785c');
    }

    public function testAssignAction_exceptionPackageId() {
        $client = $this->createMock(VisaHttpClient::class);
        $client->expects($this->never())->method('put');

        $packageApi = new PackageApi($client);
        //no intpPackageId is set

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Typed property Visa\Packages\PackageApi::$packageId must not be accessed before initialization');

        $packageApi->assign('9e595bdc-b79c-4c32-9d2f-80be6a67785c');
    }

    public function testAssignAction_exceptionTargetId() {
        $client = $this->createMock(VisaHttpClient::class);
        $client->expects($this->never())->method('put');

        $packageApi = new PackageApi($client);
        $packageApi->setPackageId('5e392d13-5db2-40c0-a3db-03c37ad365b4');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Target id not set.');

        $packageApi->assign('');
    }

    public function testUnassignAction() {
        $response = $this->createMock(Response::class);
        $response->method('getStatusCode')->willReturn(204);

        $client = $this->createMock(VisaHttpClient::class);
        $client->method('delete')
            ->with('/v3/3as/packages/5e392d13-5db2-40c0-a3db-03c37ad365b4/assignments/9e595bdc-b79c-4c32-9d2f-80be6a67785c')
            ->willReturn($response);

        $packageApi = new PackageApi($client);
        $packageApi->setPackageId('5e392d13-5db2-40c0-a3db-03c37ad365b4');

        $packageApi->unassign('9e595bdc-b79c-4c32-9d2f-80be6a67785c');
    }

    public function testUnassignAction_exceptionPackageId() {
        $client = $this->createMock(VisaHttpClient::class);
        $client->expects($this->never())->method('delete');

        $packageApi = new PackageApi($client);
        //no intpPackageId is set

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Typed property Visa\Packages\PackageApi::$packageId must not be accessed before initialization');

        $packageApi->unassign('9e595bdc-b79c-4c32-9d2f-80be6a67785c');
    }

    public function testUnassignAction_exceptionTargetId() {
        $client = $this->createMock(VisaHttpClient::class);
        $client->expects($this->never())->method('delete');

        $packageApi = new PackageApi($client);
        $packageApi->setPackageId('5e392d13-5db2-40c0-a3db-03c37ad365b4');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Target id not set.');

        $packageApi->unassign('');
    }
}