<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Mapper;

use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\CatalogOfferDto;
use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\HotelDto;

final readonly class HotelOfferDataMapper
{
    public function map(CatalogOfferDto $catalogOfferDto): HotelDto
    {
        return new HotelDto(
            hotelName: $catalogOfferDto->getHotelName(),
            sourceUrl: $catalogOfferDto->getHotelSourceUrl(),
            teaser: $catalogOfferDto->getTeaser(),
            description: $catalogOfferDto->getTeaser(),
            mediaUrls: $catalogOfferDto->getMediaUrls()
        );
    }
}
