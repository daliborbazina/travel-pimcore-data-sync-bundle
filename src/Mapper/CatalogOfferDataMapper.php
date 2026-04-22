<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Mapper;

use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\CatalogOfferDto;
use DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject\DestinationCode;
use DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject\SupplierOfferId;
use InvalidArgumentException;

use function is_array;
use function is_string;
use function sprintf;
use function trim;

final readonly class CatalogOfferDataMapper
{
    /**
     * @param array<string, mixed> $data
     */
    public function map(array $data): CatalogOfferDto
    {
        return new CatalogOfferDto(
            supplierOfferId: new SupplierOfferId($this->getRequiredString($data, 'supplier_offer_id')),
            destinationCode: new DestinationCode($this->getRequiredString($data, 'destination_code')),
            destinationName: $this->getRequiredString($data, 'destination_name'),
            hotelName: $this->getRequiredString($data, 'hotel_name'),
            teaser: $this->getNullableString($data, 'teaser'),
            description: $this->getNullableString($data, 'description'),
            mediaUrls: $this->getStringList($data, 'media_urls'),
            hotelSourceUrl: $this->getNullableString($data, 'hotel_source_url'),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getRequiredString(array $data, string $key): string
    {
        $value = $this->getNullableString($data, $key);

        if (null === $value) {
            throw new InvalidArgumentException(sprintf('Missing required catalog field "%s".', $key));
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
     *
     * @return array<int, string>
     */
    private function getStringList(array $data, string $key): array
    {
        $value = $data[$key] ?? [];

        if (!is_array($value)) {
            return [];
        }

        $items = [];

        foreach ($value as $item) {
            if (!is_string($item)) {
                continue;
            }

            $item = trim($item);

            if ('' === $item) {
                continue;
            }

            $items[] = $item;
        }

        return $items;
    }
}
