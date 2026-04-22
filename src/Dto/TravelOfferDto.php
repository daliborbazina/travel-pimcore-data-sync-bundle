<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Dto;

use DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject\SupplierOfferId;

final readonly class TravelOfferDto
{
    /**
     * @param array<int, string> $mediaUrls
     */
    public function __construct(
        private SupplierOfferId $supplierOfferId,
        private ?string $teaser = null,
        private ?string $description = null,
        private array $mediaUrls = [],
    ) {
    }

    public function getSupplierOfferId(): SupplierOfferId
    {
        return $this->supplierOfferId;
    }

    public function getTeaser(): ?string
    {
        return $this->teaser;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return array<int, string>
     */
    public function getMediaUrls(): array
    {
        return $this->mediaUrls;
    }
}
