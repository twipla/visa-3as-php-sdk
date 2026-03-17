<?php

declare(strict_types=1);

namespace Visa\Websites;

class Contributor
{
    private string $intpCustomerId;
    private string $email;

    /**
     * @return string
     */
    public function getIntpCustomerId(): string
    {
        return $this->intpCustomerId;
    }

    /**
     * @param string $intpCustomerId
     * @return void
     */
    public function setIntpCustomerId(string $intpCustomerId): void
    {
        $this->intpCustomerId = $intpCustomerId;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}