<?php

namespace Visa\Packages;

use Visa\VisaHttpClient;

class PackageApi
{
    private string $packageId;

    private VisaHttpClient $httpClient;
    private PackageHydrator $hydrator;

    public function __construct(VisaHttpClient $visaHttpClient)
    {
        $this->httpClient = $visaHttpClient;
        $this->hydrator = new PackageHydrator(new PackageRestrictionHydrator());
    }

    public function setPackageId(string $packageId): PackageApi
    {
        $this->packageId = $packageId;

        return $this;
    }

    public function update(array $package): Package
    {
        $response = $this->httpClient->patch('/v2/3as/packages/' . $this->packageId, $package);

        return $this->hydrator->hydrateObject($response->getPayload());
    }

    /**
     * @param string $targetId
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function assign(string $targetId): void
    {
        if (!$this->packageId) {
            throw new \Exception('Package id not set.');
        }

        if (!$targetId) {
            throw new \Exception('Target id not set.');
        }

        $this->httpClient->put('/v3/3as/packages/'. $this->packageId . '/assignments/' . $targetId, []);
    }

    /**
     * @param string $targetId
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unassign(string $targetId): void
    {
        if (!$this->packageId) {
            throw new \Exception('Package id not set.');
        }

        if (!$targetId) {
            throw new \Exception('Target id not set.');
        }

        $this->httpClient->delete('/v3/3as/packages/'. $this->packageId . '/assignments/' . $targetId);
    }
}
