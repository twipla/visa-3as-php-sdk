<?php

declare(strict_types=1);

namespace Visa\Websites;

use Visa\HydratorInterface;
use Visa\VisaHttpClient;

class WebsiteApi
{
    private string $intpWebsiteId;

    private VisaHttpClient $visaHttpClient;
    private HydratorInterface $apiKeyHydrator;
    private HydratorInterface $contributorHydrator;

    public function __construct(VisaHttpClient $visaHttpClient)
    {
        $this->visaHttpClient = $visaHttpClient;
        $this->apiKeyHydrator = new ApiKeyHydrator();
        $this->contributorHydrator = new ContributorHydrator();
    }

    public function setIntpWebsiteId(string $intpWebsiteId): WebsiteApi
    {
        $this->intpWebsiteId = $intpWebsiteId;

        return $this;
    }

    public function delete(): void
    {
        if (!$this->intpWebsiteId) {
            throw new \Exception('Website external id not set.');
        }

        $this->visaHttpClient->delete('/v2/3as/websites/' . $this->intpWebsiteId);
    }

    public function addWhitelistedDomain(string $domain): void
    {
        if (!$this->intpWebsiteId) {
            throw new \Exception('Website external id not set.');
        }

        $this->visaHttpClient->post('/v2/3as/websites/' . $this->intpWebsiteId . '/whitelisted-domains', ['domain' => $domain]);
    }

    public function deleteWhitelistedDomain(string $domain): void
    {
        if (!$this->intpWebsiteId) {
            throw new \Exception('Website external id not set.');
        }

        $this->visaHttpClient->patch('/v2/3as/websites/' . $this->intpWebsiteId . '/whitelisted-domains', ['domain' => $domain]);
    }

    /**
     * @return string[] An array of domains
     * @throws \Exception
     */
    public function listWhitelistedDomains(): array
    {
        if (!$this->intpWebsiteId) {
            throw new \Exception('Website external id not set.');
        }

        $response = $this->visaHttpClient->get('/v2/3as/websites/' . $this->intpWebsiteId . '/whitelisted-domains');

        return $response->getPayload();
    }

    public function createApiKey(array $input): ApiKey
    {
        if (!$this->intpWebsiteId) {
            throw new \Exception('Website external id not set.');
        }

        $response = $this->visaHttpClient->post('/v2/3as/websites/' . $this->intpWebsiteId . '/api-keys', $input);

        return $this->apiKeyHydrator->hydrateObject($response->getPayload());
    }

    public function listApiKeys(): array
    {
        if (!$this->intpWebsiteId) {
            throw new \Exception('Website external id not set.');
        }

        $response = $this->visaHttpClient->get('/v2/3as/websites/' . $this->intpWebsiteId . '/api-keys');

        return $this->apiKeyHydrator->hydrateObjectArray($response->getPayload());
    }

    public function deleteApiKey(string $id)
    {
        if (!$this->intpWebsiteId) {
            throw new \Exception('Website external id not set.');
        }

        $this->visaHttpClient->delete('/v2/3as/websites/' . $this->intpWebsiteId . '/api-keys/' . $id);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function listContributors(): array {
        if (!$this->intpWebsiteId) {
            throw new \Exception('Website external id not set.');
        }

         $response = $this->visaHttpClient->get('/v3/3as/websites/' . $this->intpWebsiteId . '/contributors');

        return $this->contributorHydrator->hydrateObjectArray($response->getPayload());
    }

    /**
     * @throws \Exception
     */
    public function addContributor(array $input): void
    {
        if (!$this->intpWebsiteId) {
            throw new \Exception('Website external id not set.');
        }

        if (empty($input)) {
            throw new \Exception('Cannot add contributor with empty input.');
        }
        $this->visaHttpClient->post('/v3/3as/websites/' . $this->intpWebsiteId . '/contributors', $input);
    }

    /**
     * @throws \Exception
     */
    public function deleteContributor(string $intpCustomerId): void
    {
        if (!$this->intpWebsiteId) {
            throw new \Exception('Website external id not set.');
        }

        if (empty($intpCustomerId)) {
            throw new \Exception('Cannot delete contributor for empty intpCustomerId.');
        }
        $this->visaHttpClient->delete('/v3/3as/websites/' . $this->intpWebsiteId . '/contributors/' . $intpCustomerId);
    }
}
