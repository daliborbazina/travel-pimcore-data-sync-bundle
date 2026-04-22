<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Tests\Publish;

use DaliborBazina\TravelPimcoreDataSyncBundle\Publish\PublishPayloadBuilder;
use PHPUnit\Framework\TestCase;
use Pimcore\Model\DataObject\TravelOffer;
use Pimcore\Model\DataObject\TravelOfferAvailability;

final class PublishPayloadBuilderTest extends TestCase
{
    public function testItBuildsPayloadFromTravelOfferAndAvailabilities(): void
    {
        $builder = new PublishPayloadBuilder();

        $travelOffer = $this->createMock(TravelOffer::class);
        $travelOffer->method('getSupplierOfferId')->willReturn('AHR-MED-001');
        $travelOffer->method('getTeaser')->willReturn('Summer stay');
        $travelOffer->method('getDescription')->willReturn('Seven-night summer stay');
        $travelOffer->method('getStatus')->willReturn('draft');

        $availabilityOne = $this->createMock(TravelOfferAvailability::class);
        $availabilityOne->method('getAvailableFrom')->willReturn(new \DateTimeImmutable('2026-08-01'));
        $availabilityOne->method('getAvailableTo')->willReturn(new \DateTimeImmutable('2026-08-08'));
        $availabilityOne->method('getPriceHint')->willReturn('799.99');
        $availabilityOne->method('getCurrency')->willReturn('EUR');
        $availabilityOne->method('getAvailable')->willReturn(true);

        $availabilityTwo = $this->createMock(TravelOfferAvailability::class);
        $availabilityTwo->method('getAvailableFrom')->willReturn(new \DateTimeImmutable('2026-09-01'));
        $availabilityTwo->method('getAvailableTo')->willReturn(new \DateTimeImmutable('2026-09-08'));
        $availabilityTwo->method('getPriceHint')->willReturn('849.99');
        $availabilityTwo->method('getCurrency')->willReturn('EUR');
        $availabilityTwo->method('getAvailable')->willReturn(false);

        $payload = $builder->buildFromTravelOffer(
            $travelOffer,
            [$availabilityOne, $availabilityTwo],
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
                    [
                        'available_from' => '2026-09-01',
                        'available_to' => '2026-09-08',
                        'price_hint' => '849.99',
                        'currency' => 'EUR',
                        'available' => false,
                    ],
                ],
                'teaser' => 'Summer stay',
                'description' => 'Seven-night summer stay',
                'status' => 'draft',
            ],
            $payload->toArray(),
        );
    }

    public function testItBuildsPayloadWithEmptyTravelPeriods(): void
    {
        $builder = new PublishPayloadBuilder();

        $travelOffer = $this->createMock(TravelOffer::class);
        $travelOffer->method('getSupplierOfferId')->willReturn('AHR-MED-001');
        $travelOffer->method('getTeaser')->willReturn('Summer stay');
        $travelOffer->method('getDescription')->willReturn('Seven-night summer stay');
        $travelOffer->method('getStatus')->willReturn('draft');

        $payload = $builder->buildFromTravelOffer($travelOffer, []);

        self::assertSame(
            [
                'supplier_offer_id' => 'AHR-MED-001',
                'travel_periods' => [],
                'teaser' => 'Summer stay',
                'description' => 'Seven-night summer stay',
                'status' => 'draft',
            ],
            $payload->toArray(),
        );
    }
}
