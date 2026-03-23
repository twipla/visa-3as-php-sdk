<?php

declare(strict_types=1);

namespace Visa\Packages;

use Laminas\Hydrator\ClassMethodsHydrator;
use Visa\HydratorInterface;

class PackageRestrictionHydrator implements HydratorInterface
{
    private ClassMethodsHydrator $hydrator;

    public function __construct()
    {
        $this->hydrator = new ClassMethodsHydrator();
    }

    public function hydrateObject(array $data, bool $multiple = false): PackageRestriction
    {
        return $this->hydrator->hydrate($data, new PackageRestriction());
    }

    public function hydrateObjectArray(array $arrayDataArray): array
    {
        return array_map(function (array $data) {
            return $this->hydrator->hydrate($data, new PackageRestriction());
        }, $arrayDataArray);
    }
}
