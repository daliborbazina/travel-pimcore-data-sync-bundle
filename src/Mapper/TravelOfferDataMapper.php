<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Mapper;

use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\CatalogOfferDto;
use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\TravelOfferDto;

final readonly class TravelOfferDataMapper
{
    public function map(CatalogOfferDto $catalogOfferDto): TravelOfferDto
    {
        return new TravelOfferDto(
            supplierOfferId: $catalogOfferDto->getSupplierOfferId(),
            teaser: $catalogOfferDto->getTeaser(),
            description: $catalogOfferDto->getDescription(),
            mediaUrls: $catalogOfferDto->getMediaUrls()
        );
    }
}
