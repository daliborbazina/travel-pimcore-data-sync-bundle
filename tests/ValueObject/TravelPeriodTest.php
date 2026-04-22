<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Tests\ValueObject;

use DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject\TravelPeriod;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TravelPeriodTest extends TestCase
{
    public function testItParsesValidTravelPeriod(): void
    {
        $travelPeriod = new TravelPeriod('2026-08-01/2026-08-08');

        self::assertSame('2026-08-01/2026-08-08', $travelPeriod->getValue());
        self::assertSame('2026-08-01', $travelPeriod->getFromDateString());
        self::assertSame('2026-08-08', $travelPeriod->getToDateString());
    }

    public function testItCalculatesNumberOfNights(): void
    {
        $travelPeriod = new TravelPeriod('2026-08-01/2026-08-08');

        self::assertSame(7, $travelPeriod->getNumberOfNights());
    }

    public function testItRejectsEmptyTravelPeriod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Travel period must not be empty.');

        new TravelPeriod('');
    }

    public function testItRejectsInvalidFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Travel period must use the format YYYY-MM-DD/YYYY-MM-DD.');

        new TravelPeriod('2026-08-01');
    }

    public function testItRejectsInvalidDates(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Travel period contains an invalid date.');

        new TravelPeriod('2026-08-32/2026-08-08');
    }

    public function testItRejectsStartDateAfterEndDate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Travel period start date must not be after end date.');

        new TravelPeriod('2026-08-08/2026-08-01');
    }
}
