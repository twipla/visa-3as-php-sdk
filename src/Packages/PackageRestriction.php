<?php

namespace Visa\Packages;

class PackageRestriction
{
    private string $intpWebsiteId;
    private string $intpCustomerId;

    public function getIntpWebsiteId(): string
    {
        return $this->intpWebsiteId;
    }

    public function setIntpWebsiteId(string $intpWebsiteId): void
    {
        $this->intpWebsiteId = $intpWebsiteId;
    }

    public function getIntpCustomerId(): string
    {
        return $this->intpCustomerId;
    }

    public function setIntpCustomerId(string $intpCustomerId): void
    {
        $this->intpCustomerId = $intpCustomerId;
    }
}