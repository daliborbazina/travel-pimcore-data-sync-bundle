<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Dto;

use DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject\DestinationCode;

final readonly class DestinationDto
{
    public function __construct(
        private DestinationCode $destinationCode,
        private string $name,
    ) {
    }

    public function getDestinationCode(): DestinationCode
    {
        return $this->destinationCode;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
