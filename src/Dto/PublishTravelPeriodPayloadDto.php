<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Dto;

final readonly class PublishTravelPeriodPayloadDto
{
    public function __construct(
        private ?string $availableFrom,
        private ?string $availableTo,
        private ?string $priceHint = null,
        private ?string $currency = null,
        private bool $available = false,
    ) {
    }

    /**
     * @return array{
     *     available_from: ?string,
     *     available_to: ?string,
     *     price_hint: ?string,
     *     currency: ?string,
     *     available: bool
     * }
     */
    public function toArray(): array
    {
        return [
            'available_from' => $this->availableFrom,
            'available_to' => $this->availableTo,
            'price_hint' => $this->priceHint,
            'currency' => $this->currency,
            'available' => $this->available,
        ];
    }
}
