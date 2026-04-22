<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Publish;

use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\PublishPayloadDto;
use DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Repository\TravelOfferAvailabilityRepository;
use DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Repository\TravelOfferRepository;

use function json_encode;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;

final readonly class PublishPreviewService
{
    public function __construct(
        private TravelOfferRepository $travelOfferRepository,
        private TravelOfferAvailabilityRepository $travelOfferAvailabilityRepository,
        private PublishPayloadBuilder $publishPayloadBuilder,
    ) {
    }

    /**
     * @return list<PublishPayloadDto>
     */
    public function buildPreview(?int $limit = null): array
    {
        $travelOffers = $this->travelOfferRepository->findPublishableOffers($limit);

        if ([] === $travelOffers) {
            return [];
        }

        $ids = [];

        foreach ($travelOffers as $travelOffer) {
            $ids[] = $travelOffer->getId();
        }

        $availabilities = $this->travelOfferAvailabilityRepository->findByTravelOfferIds($ids);

        $availabilitiesByOfferId = [];

        foreach ($availabilities as $availability) {
            $parentOffer = $availability->getTravelOffer();

            if (null === $parentOffer) {
                continue;
            }

            $availabilitiesByOfferId[$parentOffer->getId()][] = $availability;
        }

        $payloads = [];

        foreach ($travelOffers as $travelOffer) {
            $payloads[] = $this->publishPayloadBuilder->buildFromTravelOffer(
                $travelOffer,
                $availabilitiesByOfferId[$travelOffer->getId()] ?? [],
            );
        }

        return $payloads;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function buildPreviewAsArray(?int $limit = null): array
    {
        $result = [];

        foreach ($this->buildPreview($limit) as $payload) {
            $result[] = $payload->toArray();
        }

        return $result;
    }

    public function buildPreviewAsJson(?int $limit = null, bool $pretty = true): string
    {
        $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES;

        if ($pretty) {
            $flags |= JSON_PRETTY_PRINT;
        }

        return json_encode($this->buildPreviewAsArray($limit), $flags);
    }
}
