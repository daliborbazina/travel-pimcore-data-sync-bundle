<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Tests\Validation;

use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\CatalogOfferDto;
use DaliborBazina\TravelPimcoreDataSyncBundle\Validation\CatalogOfferValidator;
use DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject\DestinationCode;
use DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject\SupplierOfferId;
use PHPUnit\Framework\TestCase;

final class CatalogOfferValidatorTest extends TestCase
{
    public function testItReturnsValidResultForMinimalValidCatalogOffer(): void
    {
        $validator = new CatalogOfferValidator();

        $dto = new CatalogOfferDto(
            supplierOfferId: new SupplierOfferId('AHR-MED-001'),
            destinationCode: new DestinationCode('hr-ist-med'),
            destinationName: 'Medulin',
            hotelName: 'Park Plaza Belvedere',
            teaser: 'Summer stay',
            description: 'Seven-night summer stay',
            mediaUrls: [],
        );

        $result = $validator->validate($dto);

        self::assertTrue($result->isValid());
        self::assertSame([], $result->getErrors());
    }

    public function testItReturnsErrorsForMissingRequiredCatalogFields(): void
    {
        $validator = new CatalogOfferValidator();

        $dto = new CatalogOfferDto(
            supplierOfferId: new SupplierOfferId(' '),
            destinationCode: new DestinationCode(' '),
            destinationName: ' ',
            hotelName: ' ',
            teaser: null,
            description: null,
            mediaUrls: [],
        );

        $result = $validator->validate($dto);

        self::assertFalse($result->isValid());
        self::assertSame(
            [
                'Missing supplier offer ID.',
                'Missing destination code.',
                'Missing destination name.',
                'Missing hotel name.',
            ],
            $result->getErrors(),
        );
    }
}
