<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Relation;

use Pimcore\Model\DataObject\TravelOffer;
use Pimcore\Model\DataObject\TravelOfferAvailability;

final readonly class TravelOfferAvailabilityAssigner
{
    public function assign(TravelOfferAvailability $travelOfferAvailability, TravelOffer $travelOffer): void
    {
        $travelOfferAvailability->setTravelOffer($travelOffer);
        $travelOfferAvailability->save();
    }
}
