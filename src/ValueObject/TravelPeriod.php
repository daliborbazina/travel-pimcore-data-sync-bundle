<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject;

use Carbon\Carbon;
use DateTimeImmutable;
use InvalidArgumentException;

final readonly class TravelPeriod
{
    private DateTimeImmutable $from;
    private DateTimeImmutable $to;
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ('' === $value) {
            throw new InvalidArgumentException('Travel period must not be empty.');
        }

        $parts = explode('/', $value);

        if (2 !== count($parts)) {
            throw new InvalidArgumentException('Travel period must use the format YYYY-MM-DD/YYYY-MM-DD.');
        }

        $fromRaw = trim($parts[0]);
        $toRaw = trim($parts[1]);

        $from = DateTimeImmutable::createFromFormat('Y-m-d', $fromRaw);
        $to = DateTimeImmutable::createFromFormat('Y-m-d', $toRaw);

        if (false === $from || false === $to) {
            throw new InvalidArgumentException('Travel period contains an invalid date.');
        }

        if ($from->format('Y-m-d') !== $fromRaw || $to->format('Y-m-d') !== $toRaw) {
            throw new InvalidArgumentException('Travel period must use valid ISO dates.');
        }

        if ($from > $to) {
            throw new InvalidArgumentException('Travel period start date must not be after end date.');
        }

        $this->from = $from;
        $this->to = $to;
        $this->value = $from->format('Y-m-d').'/'.$to->format('Y-m-d');
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getFrom(): DateTimeImmutable
    {
        return $this->from;
    }

    public function getTo(): DateTimeImmutable
    {
        return $this->to;
    }

    public function getFromDateString(): string
    {
        return $this->from->format('Y-m-d');
    }

    public function getToDateString(): string
    {
        return $this->to->format('Y-m-d');
    }

    public function getFromCarbon(): Carbon
    {
        return Carbon::instance($this->from);
    }

    public function getToCarbon(): Carbon
    {
        return Carbon::instance($this->to);
    }

    public function getNumberOfNights(): int
    {
        return (int) $this->from->diff($this->to)->days;
    }
}
