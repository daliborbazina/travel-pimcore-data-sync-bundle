<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Dto;

use DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject\DestinationCode;
use DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject\SupplierOfferId;

final readonly class CatalogOfferDto
{
    /**
     * @param array<int, string> $mediaUrls
     */
    public function __construct(
        private SupplierOfferId $supplierOfferId,
        private DestinationCode $destinationCode,
        private string $destinationName,
        private string $hotelName,
        private ?string $teaser = null,
        private ?string $description = null,
        private array $mediaUrls = [],
        private ?string $hotelSourceUrl = null,
    ) {
    }

    public function getSupplierOfferId(): SupplierOfferId
    {
        return $this->supplierOfferId;
    }

    public function getDestinationCode(): DestinationCode
    {
        return $this->destinationCode;
    }

    public function getDestinationName(): string
    {
        return $this->destinationName;
    }

    public function getHotelName(): string
    {
        return $this->hotelName;
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

    public function getHotelSourceUrl(): ?string
    {
        return $this->hotelSourceUrl;
    }
}
