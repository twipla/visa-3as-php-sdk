<?php

declare(strict_types=1);

namespace Visa\Packages;

use GuzzleHttp\Exception\GuzzleException;
use Visa\VisaHttpClient;

class PackagesApi
{
    private VisaHttpClient $httpClient;
    private PackageHydrator $hydrator;

    public function __construct(VisaHttpClient $visaHttpClient)
    {
        $this->httpClient = $visaHttpClient;
        $this->hydrator = new PackageHydrator(new PackageRestrictionHydrator());
    }

    /**
     * @param array $package
     * @return Package
     * @throws GuzzleException
     * @throws \Exception
     */
    public function create(array $package): Package
    {   
        $response = $this->httpClient->post('/v2/3as/packages', $package);

        return $this->hydrator->hydrateObject($response->getPayload());
    }

    /**
     * @param string $id
     * @return Package
     * @throws GuzzleException
     */
    public function getById(string $id): Package
    {
        $response = $this->httpClient->get('/v2/3as/packages/' . $id);

        return $this->hydrator->hydrateObject($response->getPayload());
    }

    /**
     * @param string|null $intpWebsiteId
     * @param string|null $intpCustomerId
     * @return Package[]
     * @throws GuzzleException
     */
    public function list(string $intpWebsiteId = null, string $intpCustomerId = null): array
    {
        $query = '';
        if (!empty($intpWebsiteId)) {
            $query .= 'intpWebsiteId=' . $intpWebsiteId;
        }
        if (!empty($intpCustomerId)) {
            if (!empty($query)) {
                $query .= '&';
            }
            $query .= 'intpCustomerId=' . $intpCustomerId;
        }
        if (!empty($query)) {
            $query = '?' . $query;

        }
        $response = $this->httpClient->get('/v2/3as/packages'. $query);

        return $this->hydrator->hydrateObjectArray($response->getPayload());
    }
}
