<?php

declare(strict_types=1);

namespace Visa\Websites;

use Visa\HydratorInterface;

class ApiKeyHydrator implements HydratorInterface
{
    public function hydrateObject(array $data): ApiKey
    {
        $apiKey = new ApiKey();

        $apiKey->setId($data['id']);
        $apiKey->setName($data['name']);
        $apiKey->setApiKey($data['apiKey'] ?? null);
        $apiKey->setComment($data['comment'] ?? null);
        $apiKey->setCreatedAt($data['createdAt']);
        $apiKey->setExpiresAt($data['expiresAt']);
        $apiKey->setIntpWebsiteId($data["intpWebsiteId"]);
        $apiKey->setIntpCustomerId($data["intpCustomerId"]);

        return $apiKey;
    }

    public function hydrateObjectArray(array $arrayData): array
    {
        return array_map(function (array $data) {
            return $this->hydrateObject($data);
        }, $arrayData);
    }
}
