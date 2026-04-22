<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Publish;

use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\PublishPayloadDto;
use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\PublishTravelPeriodPayloadDto;
use Pimcore\Model\DataObject\TravelOffer;

final readonly class PublishPayloadBuilder
{
    /**
     * @param array<int, \Pimcore\Model\DataObject\TravelOfferAvailability> $availabilities
     */
    public function buildFromTravelOffer(TravelOffer $travelOffer, array $availabilities): PublishPayloadDto
    {
        $travelPeriods = [];

        foreach ($availabilities as $availability) {
            $travelPeriods[] = new PublishTravelPeriodPayloadDto(
                availableFrom: $availability->getAvailableFrom()?->format('Y-m-d'),
                availableTo: $availability->getAvailableTo()?->format('Y-m-d'),
                priceHint: $availability->getPriceHint(),
                currency: $availability->getCurrency(),
                available: $availability->getAvailable(),
            );
        }

        return new PublishPayloadDto(
            supplierOfferId: $travelOffer->getSupplierOfferId(),
            travelPeriods: $travelPeriods,
            teaser: $travelOffer->getTeaser(),
            description: $travelOffer->getDescription(),
            status: $travelOffer->getStatus(),
        );
    }
}
