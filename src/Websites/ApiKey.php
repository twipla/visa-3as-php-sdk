<?php

declare(strict_types=1);

namespace Visa\Websites;

class ApiKey
{
    private string $id;
    private string $name;
    private ?string $apiKey;
    private ?string $comment;
    private string $createdAt;
    private string $expiresAt;
    private string $intpWebsiteId;
    private string $intpCustomerId;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apikey)
    {
        $this->apiKey = $apikey;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getIntpWebsiteId(): string
    {
        return $this->intpWebsiteId;
    }

    /**
     * @param string $intpWebsiteId
     */
    public function setIntpWebsiteId($intpWebsiteId)
    {
        $this->intpWebsiteId = $intpWebsiteId;
    }

    /**
     * @return string
     */
    public function getIntpCustomerId(): string
    {
        return $this->intpCustomerId;
    }

    /**
     * @param string $intpCustomerId
     */
    public function setIntpCustomerId($intpCustomerId)
    {
        $this->intpCustomerId = $intpCustomerId;
    }

    /**
     * @param string $createdAt
     */
    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @param string $expiresAt
     */
    public function setExpiresAt(string $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return string
     */
    public function getExpiresAt(): string
    {
        return $this->expiresAt;
    }
}
