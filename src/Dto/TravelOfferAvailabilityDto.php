<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Dto;

use DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject\SupplierOfferId;
use DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject\TravelPeriod;

final readonly class TravelOfferAvailabilityDto
{
    public function __construct(
        private SupplierOfferId $supplierOfferId,
        private TravelPeriod $travelPeriod,
        private ?string $status = null,
        private ?float $priceHint = null,
        private ?string $currency = null,
        private bool $available = false,
        private ?string $updatedAt = null,
    ) {
    }

    public function getSupplierOfferId(): SupplierOfferId
    {
        return $this->supplierOfferId;
    }

    public function getTravelPeriod(): TravelPeriod
    {
        return $this->travelPeriod;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getPriceHint(): ?float
    {
        return $this->priceHint;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }
}
