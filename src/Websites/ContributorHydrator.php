<?php

declare(strict_types=1);

namespace Visa\Websites;

use Visa\HydratorInterface;

class ContributorHydrator implements HydratorInterface
{
    public function hydrateObjectArray(array $arrayData): array
    {
        $response = [];
        array_map(function (array $data) use (&$response) {
            if (array_key_exists('owner', $data)) {
                $response['owner'] = $this->hydrateObject($data['owner']);
            }
            if (array_key_exists('contributors', $data)) {
                array_map(function (string $type, array $contributorsByType) use (&$response) {
                    $byType = [];
                    foreach ($contributorsByType as $contributorData) {
                        $byType[] = $this->hydrateObject($contributorData);
                    }
                    $response['contributors'][$type] = $byType;
                }, array_keys($data['contributors']), $data['contributors']);
            }
        }, $arrayData);

        return $response;
    }

    public function hydrateObject(array $data): Contributor
    {
        $contributor = new Contributor();

        $contributor->setIntpCustomerId($data['intpCustomerId']);
        $contributor->setEmail($data['email']);

        return $contributor;
    }
}