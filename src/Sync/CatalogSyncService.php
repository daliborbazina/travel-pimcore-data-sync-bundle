<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Sync;

use DaliborBazina\TravelPimcoreDataSyncBundle\Exception\SyncException;
use DaliborBazina\TravelPimcoreDataSyncBundle\Mapper\DestinationOfferDataMapper;
use DaliborBazina\TravelPimcoreDataSyncBundle\Mapper\HotelOfferDataMapper;
use DaliborBazina\TravelPimcoreDataSyncBundle\Mapper\TravelOfferDataMapper;
use DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Relation\TravelOfferAssigner;
use DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Repository\DestinationRepository;
use DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Repository\HotelRepository;
use DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Repository\TravelOfferRepository;
use DaliborBazina\TravelPimcoreDataSyncBundle\Source\JsonFeedReader;
use DaliborBazina\TravelPimcoreDataSyncBundle\Validation\CatalogOfferValidator;
use Throwable;

use function array_slice;
use function sprintf;

final readonly class CatalogSyncService
{
    public function __construct(
        private JsonFeedReader $feedReader,
        private DestinationOfferDataMapper $destinationDataMapper,
        private HotelOfferDataMapper $hotelDataMapper,
        private TravelOfferDataMapper $travelOfferDataMapper,
        private CatalogOfferValidator $catalogOfferValidator,
        private DestinationRepository $destinationRepository,
        private HotelRepository $hotelRepository,
        private TravelOfferRepository $travelOfferRepository,
        private TravelOfferAssigner $travelOfferRelationAssigner,
    ) {
    }

    public function sync(SyncContext $context): SyncReport
    {
        $catalogOfferDtos = $this->feedReader->readCatalogFeed($context->getFilePath());

        $report = new SyncReport();
        $report->addRecordsRead(count($catalogOfferDtos));

        if (null !== $context->getLimit()) {
            $catalogOfferDtos = array_slice($catalogOfferDtos, 0, $context->getLimit());
        }

        $report->addRecordsSelected(count($catalogOfferDtos));

        foreach ($catalogOfferDtos as $catalogOfferDto) {
            $validationResult = $this->catalogOfferValidator->validate($catalogOfferDto);

            if (!$validationResult->isValid()) {
                $report->incrementRecordsInvalid();
                continue;
            }

            try {
                // Destination
                $destinationDto = $this->destinationDataMapper->map($catalogOfferDto);

                $destination = $this->destinationRepository->findOneByCode(
                    $destinationDto->getDestinationCode()->getValue(),
                );

                if (null === $destination) {
                    if ($context->isDryRun()) {
                        $destination = $this->destinationRepository->createPreviewFromDto($destinationDto);
                    } else {
                        $destination = $this->destinationRepository->createFromDto($destinationDto);
                    }

                    $report->incrementDestinationsCreated();
                } elseif (!$context->isDryRun()) {
                    $this->destinationRepository->updateFromDto($destination, $destinationDto);
                    $report->incrementDestinationsUpdated();
                }

                // Hotel
                $hotelDto = $this->hotelDataMapper->map($catalogOfferDto);

                $hotel = $this->hotelRepository->findOneByNameAndDestination(
                    $hotelDto->getName(),
                    $destination,
                );

                if (null === $hotel) {
                    if ($context->isDryRun()) {
                        $hotel = $this->hotelRepository->createPreviewFromDto($hotelDto, $destination);
                    } else {
                        $hotel = $this->hotelRepository->createFromDto($hotelDto, $destination);
                    }

                    $report->incrementHotelsCreated();
                } elseif (!$context->isDryRun()) {
                    $this->hotelRepository->updateFromDto($hotel, $hotelDto);
                    $report->incrementHotelsUpdated();
                }

                // TravelOffer
                $travelOfferDto = $this->travelOfferDataMapper->map($catalogOfferDto);

                $travelOffer = $this->travelOfferRepository->findOneBySupplierOfferId(
                    $travelOfferDto->getSupplierOfferId()->getValue(),
                );

                if (null === $travelOffer) {
                    if ($context->isDryRun()) {
                        $travelOffer = $this->travelOfferRepository->createPreviewFromDto($travelOfferDto);
                    } else {
                        $travelOffer = $this->travelOfferRepository->createFromDto($travelOfferDto, $destination);
                    }
                    $report->incrementTravelOffersCreated();
                } elseif (!$context->isDryRun()) {
                    $this->travelOfferRepository->updateFromDto($travelOffer, $travelOfferDto);
                    $report->incrementTravelOffersUpdated();
                }

                if (!$context->isDryRun()) {
                    $this->travelOfferRelationAssigner->assign($travelOffer, $destination, $hotel);
                }

                $report->incrementRecordsSucceeded();
            } catch (Throwable $exception) {
                $report->incrementRecordsFailed();

                throw new SyncException(
                    sprintf(
                        'Catalog synchronization failed for supplier offer "%s".',
                        $catalogOfferDto->getSupplierOfferId()->getValue(),
                    ),
                    0,
                    $exception,
                );
            }
        }

        return $report;
    }
}
