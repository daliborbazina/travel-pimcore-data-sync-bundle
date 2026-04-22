<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Tests\Dto;

use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\PublishPayloadDto;
use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\PublishTravelPeriodPayloadDto;
use PHPUnit\Framework\TestCase;

final class PublishPayloadDtoTest extends TestCase
{
    public function testItSerializesNestedTravelPeriodsToArray(): void
    {
        $dto = new PublishPayloadDto(
            supplierOfferId: 'AHR-MED-001',
            travelPeriods: [
                new PublishTravelPeriodPayloadDto(
                    availableFrom: '2026-08-01',
                    availableTo: '2026-08-08',
                    priceHint: '799.99',
                    currency: 'EUR',
                    available: true,
                ),
            ],
            teaser: 'Summer stay',
            description: 'Seven-night summer stay',
            status: 'draft',
        );

        self::assertSame(
            [
                'supplier_offer_id' => 'AHR-MED-001',
                'travel_periods' => [
                    [
                        'available_from' => '2026-08-01',
                        'available_to' => '2026-08-08',
                        'price_hint' => '799.99',
                        'currency' => 'EUR',
                        'available' => true,
                    ],
                ],
                'teaser' => 'Summer stay',
                'description' => 'Seven-night summer stay',
                'status' => 'draft',
            ],
            $dto->toArray(),
        );
    }
}
