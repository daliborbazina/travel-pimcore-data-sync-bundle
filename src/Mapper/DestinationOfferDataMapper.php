<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Mapper;

use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\CatalogOfferDto;
use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\DestinationDto;

final readonly class DestinationOfferDataMapper
{
    public function map(CatalogOfferDto $catalogOfferDto): DestinationDto
    {
        return new DestinationDto(
            destinationCode: $catalogOfferDto->getDestinationCode(),
            name: $catalogOfferDto->getDestinationName(),
        );
    }
}
