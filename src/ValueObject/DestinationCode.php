<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject;

final readonly class DestinationCode
{
    public function __construct(
        private string $value,
    ) {
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
