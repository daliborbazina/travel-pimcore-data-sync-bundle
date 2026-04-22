<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Repository;

use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\DestinationDto;
use DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Path\PimcoreFolderLocator;
use InvalidArgumentException;
use Pimcore\Model\DataObject\Destination;
use Pimcore\Model\DataObject\Destination\Listing as DestinationListing;
use Pimcore\Model\DataObject\Service;

final readonly class DestinationRepository
{
    public function __construct(private PimcoreFolderLocator $folderLocator)
    {
    }

    public function findOneByCode(string $destinationCode): ?Destination
    {
        $list = new DestinationListing();

        $list->setCondition('destinationCode = ?', [$destinationCode]);
        $list->setLimit(1);

        /** @var array<int, Destination> $items */
        $items = $list->load();

        return $items[0] ?? null;
    }

    public function createFromDto(DestinationDto $destinationDto): Destination
    {
        $destination = $this->createInstance($destinationDto);
        $destination->setParent(
            Service::createFolderByPath(
                $this->folderLocator->getDestinationFolderPath()
            )
        );
        $destination->save();

        return $destination;
    }

    public function createPreviewFromDto(DestinationDto $destinationDto): Destination
    {
        return $this->createInstance($destinationDto);
    }

    public function updateFromDto(Destination $destination, DestinationDto $destinationDto): void
    {
        $this->hydrate($destination, $destinationDto);
        $destination->save();
    }

    private function hydrate(Destination $destination, DestinationDto $destinationDto): void
    {
        $destination->setDestinationCode($this->requireKey($destinationDto));
        $destination->setName($destinationDto->getName());
    }

    private function createInstance(DestinationDto $destinationDto): Destination
    {
        $destination = new Destination();
        $destination->setKey($this->buildKey($destinationDto));
        $destination->setPublished(true);
        $this->hydrate($destination, $destinationDto);

        return $destination;
    }

    private function buildKey(DestinationDto $destinationDto): string
    {
        return $this->slugify($this->requireKey($destinationDto));
    }

    private function requireKey(DestinationDto $destinationDto): string
    {
        $destinationCode = trim($destinationDto->getDestinationCode()->getValue());

        if ('' === $destinationCode) {
            throw new InvalidArgumentException('Destination code must not be empty.');
        }

        return $destinationCode;
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9\-]+/', '-', $value) ?? '';
        $value = trim($value, '-');

        if ('' === $value) {
            throw new InvalidArgumentException('Destination key could not be generated from destination code.');
        }

        return $value;
    }
}
