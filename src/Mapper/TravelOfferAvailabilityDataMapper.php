<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Mapper;

use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\TravelOfferAvailabilityDto;
use DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject\SupplierOfferId;
use DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject\TravelPeriod;
use InvalidArgumentException;

use function is_numeric;
use function is_string;
use function sprintf;
use function trim;

final readonly class TravelOfferAvailabilityDataMapper
{
    /**
     * @param array<string, mixed> $data
     */
    public function map(array $data): TravelOfferAvailabilityDto
    {
        return new TravelOfferAvailabilityDto(
            supplierOfferId: new SupplierOfferId($this->getRequiredString($data, 'supplier_offer_id')),
            travelPeriod: new TravelPeriod($this->getRequiredString($data, 'travel_period')),
            status: $this->getNullableString($data, 'status'),
            priceHint: $this->getNullableFloat($data, 'price_hint'),
            currency: $this->getNullableString($data, 'currency'),
            available: $this->getBool($data, 'available'),
            updatedAt: $this->getNullableString($data, 'updated_at'),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getRequiredString(array $data, string $key): string
    {
        $value = $this->getNullableString($data, $key);

        if (null === $value) {
            throw new InvalidArgumentException(sprintf('Missing required availability field "%s".', $key));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getNullableString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);

        return '' !== $value ? $value : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getNullableFloat(array $data, string $key): ?float
    {
        $value = $data[$key] ?? null;

        if (null === $value || '' === $value) {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getBool(array $data, string $key): bool
    {
        return true === ($data[$key] ?? false);
    }
}
