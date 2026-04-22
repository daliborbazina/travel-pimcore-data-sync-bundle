<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Tests\Validation;

use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\TravelOfferAvailabilityDto;
use DaliborBazina\TravelPimcoreDataSyncBundle\Validation\AvailabilityOfferValidator;
use DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject\SupplierOfferId;
use DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject\TravelPeriod;
use PHPUnit\Framework\TestCase;

final class AvailabilityOfferValidatorTest extends TestCase
{
    public function testItReturnsValidResultForMinimalValidAvailabilityOffer(): void
    {
        $validator = new AvailabilityOfferValidator();

        $dto = new TravelOfferAvailabilityDto(
            supplierOfferId: new SupplierOfferId('AHR-MED-001'),
            travelPeriod: new TravelPeriod('2026-08-01/2026-08-08'),
            status: 'available',
            priceHint: 799.99,
            currency: 'EUR',
            available: true,
            updatedAt: '2026-04-16T10:00:00+00:00',
        );

        $result = $validator->validate($dto);

        self::assertTrue($result->isValid());
        self::assertSame([], $result->getErrors());
    }

    public function testItRequiresCurrencyWhenPriceHintIsProvided(): void
    {
        $validator = new AvailabilityOfferValidator();

        $dto = new TravelOfferAvailabilityDto(
            supplierOfferId: new SupplierOfferId('AHR-MED-001'),
            travelPeriod: new TravelPeriod('2026-08-01/2026-08-08'),
            status: 'available',
            priceHint: 799.99,
            currency: null,
            available: true,
            updatedAt: '2026-04-16T10:00:00+00:00',
        );

        $result = $validator->validate($dto);

        self::assertFalse($result->isValid());
        self::assertSame(
            ['Currency is required when price hint is provided.'],
            $result->getErrors(),
        );
    }
}
