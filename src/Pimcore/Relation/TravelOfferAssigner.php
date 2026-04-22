<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Relation;

use Pimcore\Model\DataObject\Destination;
use Pimcore\Model\DataObject\Hotel;
use Pimcore\Model\DataObject\TravelOffer;

final readonly class TravelOfferAssigner
{
    public function assign(TravelOffer $travelOffer, Destination $destination, Hotel $hotel): void
    {
        $travelOffer->setDestination($destination);
        $travelOffer->setHotel($hotel);
        $travelOffer->save();
    }
}
