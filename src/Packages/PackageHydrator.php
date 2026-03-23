<?php

declare(strict_types=1);

namespace Visa\Packages;

use Laminas\Hydrator\ClassMethodsHydrator;
use Visa\HydratorInterface;

class PackageHydrator implements HydratorInterface
{
    private ClassMethodsHydrator $hydrator;
    private PackageRestrictionHydrator $packageRestrictionHydrator;

    public function __construct(PackageRestrictionHydrator $packageRestrictionHydrator)
    {
        $this->hydrator = new ClassMethodsHydrator();
        $this->packageRestrictionHydrator = $packageRestrictionHydrator;
    }

    public function hydrateObject(array $data, bool $multiple = false): Package
    {
        $package = $this->hydrator->hydrate($data, new Package());

        if (!empty($data['restrictedTo'])) {
            $restrictedTo = $this->packageRestrictionHydrator->hydrateObjectArray($data['restrictedTo']);
            $package->setRestrictedTo($restrictedTo);
        }

        return $package;
    }

    public function hydrateObjectArray(array $arrayDataArray): array
    {
        return array_map(function (array $data) {
            return $this->hydrateObject($data);
        }, $arrayDataArray);
    }
}
