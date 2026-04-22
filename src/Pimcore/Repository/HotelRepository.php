<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Repository;

use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\HotelDto;
use DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Path\PimcoreFolderLocator;
use InvalidArgumentException;
use Pimcore\Model\DataObject\Destination;
use Pimcore\Model\DataObject\Hotel;
use Pimcore\Model\DataObject\Service;

final readonly class HotelRepository
{
    public function __construct(private PimcoreFolderLocator $folderLocator)
    {
    }

    public function findOneByNameAndDestination(string $hotelName, Destination $destination): ?Hotel
    {
        $list = new Hotel\Listing();
        $list->setCondition('hotelName = ? AND destination__id = ?', [$hotelName, $destination->getId()]);
        $list->setLimit(1);

        /** @var array<int, Hotel> $items */
        $items = $list->load();

        return $items[0] ?? null;
    }

    public function createFromDto(HotelDto $hotelDto, Destination $destination): Hotel
    {
        $hotel = $this->createInstance($hotelDto);
        $hotel->setDestination($destination);
        $hotel->setParent(
            Service::createFolderByPath(
                $this->folderLocator->getHotelFolderPath(
                    $destination->getDestinationCode()
                )
            )
        );
        $hotel->save();

        return $hotel;
    }

    public function createPreviewFromDto(HotelDto $hotelDto, Destination $destination): Hotel
    {
        $hotel = $this->createInstance($hotelDto);
        $hotel->setDestination($destination);

        return $hotel;
    }

    public function updateFromDto(Hotel $hotel, HotelDto $hotelDto): void
    {
        $this->hydrate($hotel, $hotelDto);
        $hotel->save();
    }

    private function hydrate(Hotel $hotel, HotelDto $hotelDto): void
    {
        $hotel->setHotelName($hotelDto->getName());
        $hotel->setTeaser($hotelDto->getTeaser());
        $hotel->setDescription($hotelDto->getDescription());
    }

    private function createInstance(HotelDto $hotelDto): Hotel
    {
        $hotel = new Hotel();
        $hotel->setKey($this->buildKey($hotelDto));
        $hotel->setPublished(true);
        $this->hydrate($hotel, $hotelDto);

        return $hotel;
    }

    private function buildKey(HotelDto $hotelDto): string
    {
        return $this->slugify($hotelDto->getName());
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9\-]+/', '-', $value) ?? '';
        $value = trim($value, '-');

        if ('' === $value) {
            throw new InvalidArgumentException('Hotel key could not be generated from hotel name.');
        }

        return $value;
    }
}
