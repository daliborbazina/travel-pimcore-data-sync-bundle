<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Path;

use function sprintf;

final readonly class PimcoreFolderLocator
{
    public function getDestinationFolderPath(): string
    {
        return '/Travel/Destinations';
    }

    public function getHotelFolderPath(string $destinationCode): string
    {
        return sprintf('/Travel/Hotels/%s', $destinationCode);
    }

    public function getTravelOfferFolderPath(string $destinationCode): string
    {
        return sprintf('/Travel/Offers/%s', $destinationCode);
    }
}
