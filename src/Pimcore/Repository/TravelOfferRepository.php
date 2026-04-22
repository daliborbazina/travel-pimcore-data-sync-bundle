<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Repository;

use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\TravelOfferDto;
use DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Path\PimcoreFolderLocator;
use InvalidArgumentException;
use Pimcore\Model\DataObject\Destination;
use Pimcore\Model\DataObject\Service;
use Pimcore\Model\DataObject\TravelOffer;
use Pimcore\Model\DataObject\TravelOffer\Listing as TravelOffersListing;

final readonly class TravelOfferRepository
{
    public function __construct(private PimcoreFolderLocator $folderLocator)
    {
    }

    public function findOneBySupplierOfferId(string $supplierOfferId): ?TravelOffer
    {
        $list = new TravelOffersListing();
        $list->setCondition('supplierOfferId = ?', [$supplierOfferId]);
        $list->setLimit(1);

        /** @var array<int, TravelOffer> $items */
        $items = $list->load();

        return $items[0] ?? null;
    }

    public function createFromDto(TravelOfferDto $travelOfferDto, Destination $destination): TravelOffer
    {
        $travelOffer = $this->createInstance($travelOfferDto);
        $travelOffer->setParent(
            Service::createFolderByPath(
                $this->folderLocator->getTravelOfferFolderPath($destination->getDestinationCode())
            )
        );

        $travelOffer->save();

        return $travelOffer;
    }

    public function createPreviewFromDto(TravelOfferDto $travelOfferDto): TravelOffer
    {
        return $this->createInstance($travelOfferDto);
    }

    public function updateFromDto(TravelOffer $travelOffer, TravelOfferDto $travelOfferDto): void
    {
        $this->hydrate($travelOffer, $travelOfferDto);
        $travelOffer->save();
    }

    private function hydrate(TravelOffer $travelOffer, TravelOfferDto $travelOfferDto): void
    {
        $travelOffer->setSupplierOfferId($travelOfferDto->getSupplierOfferId()->getValue());
        $travelOffer->setTeaser($travelOfferDto->getTeaser());
        $travelOffer->setDescription($travelOfferDto->getDescription());
    }


    private function createInstance(TravelOfferDto $travelOfferDto): TravelOffer
    {
        $travelOffer = new TravelOffer();
        $travelOffer->setKey($this->buildKey($travelOfferDto));
        $travelOffer->setPublished(true);
        $this->hydrate($travelOffer, $travelOfferDto);

        return $travelOffer;
    }

    /**
     * @return array<int, TravelOffer>
     */
    public function findPublishableOffers(?int $limit): array
    {
        $list = new TravelOffersListing();

        $list->setCondition('published = 1');
        if (null !== $limit) {
            $list->setLimit($limit);
        }

        /** @var array<int, TravelOffer> $items */
        $items = $list->load();

        return $items;
    }

    private function buildKey(TravelOfferDto $travelOfferDto): string
    {
        return $this->slugify($this->requireKey($travelOfferDto));
    }


    private function requireKey(TravelOfferDto $travelOfferDto): string
    {
        $offerId = trim($travelOfferDto->getSupplierOfferId()->getValue());

        if ('' === $offerId) {
            throw new InvalidArgumentException('Offer ID must not be empty.');
        }

        return $offerId;
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9\-]+/', '-', $value) ?? '';
        $value = trim($value, '-');

        if ('' === $value) {
            throw new InvalidArgumentException('Travel offer key could not be generated from supplier offer ID.');
        }

        return $value;
    }
}
