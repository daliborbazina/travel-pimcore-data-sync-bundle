<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Repository;

use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\TravelOfferAvailabilityDto;
use DaliborBazina\TravelPimcoreDataSyncBundle\ValueObject\TravelPeriod;
use Pimcore\Model\DataObject\TravelOffer;
use Pimcore\Model\DataObject\TravelOfferAvailability;
use Pimcore\Model\DataObject\TravelOfferAvailability\Listing as TravelOfferAvailabilityListing;

final readonly class TravelOfferAvailabilityRepository
{
    /**
     * @param string $supplierOfferId
     * @param TravelPeriod $travelPeriod
     * @return TravelOfferAvailability|null
     */
    public function findOneBySupplierOfferIdAndTravelPeriod(
        string $supplierOfferId,
        TravelPeriod $travelPeriod
    ): ?TravelOfferAvailability {
        $list = new TravelOfferAvailabilityListing();
        $list->setCondition(
            'supplierOfferId = ? AND availableFrom = ? AND availableTo = ?',
            [$supplierOfferId, $travelPeriod->getFromDateString(), $travelPeriod->getToDateString()]
        );
        $list->setLimit(1);

        /** @var array<int, TravelOfferAvailability> $items */
        $items = $list->load();

        return $items[0] ?? null;
    }

    public function createFromDto(
        TravelOfferAvailabilityDto $dto,
        TravelOffer $travelOffer
    ): TravelOfferAvailability {
        $travelOfferAvailability = $this->createInstance($dto);
        $travelOfferAvailability->setParent($travelOffer);

        $travelOfferAvailability->save();

        return $travelOfferAvailability;
    }

    public function createPreviewFromDto(
        TravelOfferAvailabilityDto $dto
    ): TravelOfferAvailability {
        return $this->createInstance($dto);
    }

    public function updateFromDto(
        TravelOfferAvailability $travelOfferAvailability,
        TravelOfferAvailabilityDto $dto
    ): void {
        $this->hydrate($travelOfferAvailability, $dto);
        $travelOfferAvailability->save();
    }

    private function hydrate(
        TravelOfferAvailability $travelOfferAvailability,
        TravelOfferAvailabilityDto $dto
    ): void {
        $travelOfferAvailability->setSupplierOfferId($dto->getSupplierOfferId()->getValue());
        $travelOfferAvailability->setAvailableFrom($dto->getTravelPeriod()->getFromCarbon());
        $travelOfferAvailability->setAvailableTo($dto->getTravelPeriod()->getToCarbon());
        $travelOfferAvailability->setNights($dto->getTravelPeriod()->getNumberOfNights());
        $priceHint = $dto->getPriceHint();

        $travelOfferAvailability->setPriceHint(null !== $priceHint ? number_format($priceHint, 2, '.', '') : null);
        $travelOfferAvailability->setCurrency($dto->getCurrency());
        $travelOfferAvailability->setAvailable(true);
        $travelOfferAvailability->setStatus($dto->getStatus() == 'active' ? 'available' : 'sold_out');
    }

    private function createInstance(TravelOfferAvailabilityDto $dto): TravelOfferAvailability
    {
        $travelOfferAvailability = new TravelOfferAvailability();
        $travelOfferAvailability->setKey($this->buildKey($dto));
        $travelOfferAvailability->setPublished(true);

        $this->hydrate($travelOfferAvailability, $dto);

        return $travelOfferAvailability;
    }

    /**
     * @return array<int, TravelOfferAvailability>
     */
    public function findByTravelOffer(TravelOffer $travelOffer): array
    {
        $list = new TravelOfferAvailabilityListing();
        $list->setCondition(
            'travelOffer__id = ? ',
            [$travelOffer->getId()]
        );

        /** @var array<int, TravelOfferAvailability> $items */
        $items = $list->load();

        return $items;
    }

    /**
     * @param list<int> $travelOfferIds
     * @return array<int, TravelOfferAvailability>
     */
    public function findByTravelOfferIds(array $travelOfferIds): array
    {
        $list = new TravelOfferAvailabilityListing();
        $list->setCondition(
            'travelOffer__id IN (?) ',
            [$travelOfferIds]
        );

        /** @var array<int, TravelOfferAvailability> $items */
        $items = $list->load();

        return $items;
    }

    private function buildKey(TravelOfferAvailabilityDto $dto): string
    {
        return $this->requireKey($dto);
    }

    private function requireKey(TravelOfferAvailabilityDto $dto): string
    {
        $from = $dto->getTravelPeriod()->getFromCarbon()->format('d.m.Y');
        $to = $dto->getTravelPeriod()->getToCarbon()->format('d.m.Y');
        $nights = $dto->getTravelPeriod()->getNumberOfNights();

        return sprintf('%d nights: %s-%s', $nights, $from, $to);
    }
}
