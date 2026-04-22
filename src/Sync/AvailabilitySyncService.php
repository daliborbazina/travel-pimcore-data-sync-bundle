<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Sync;

use DaliborBazina\TravelPimcoreDataSyncBundle\Exception\SyncException;
use DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Relation\TravelOfferAvailabilityAssigner;
use DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Repository\TravelOfferAvailabilityRepository;
use DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Repository\TravelOfferRepository;
use DaliborBazina\TravelPimcoreDataSyncBundle\Source\JsonFeedReader;
use DaliborBazina\TravelPimcoreDataSyncBundle\Validation\AvailabilityOfferValidator;
use Throwable;

use function array_slice;
use function sprintf;

final readonly class AvailabilitySyncService
{
    public function __construct(
        private JsonFeedReader $feedReader,
        private AvailabilityOfferValidator $availabilityOfferValidator,
        private TravelOfferRepository $travelOfferRepository,
        private TravelOfferAvailabilityRepository $travelOfferAvailabilityRepository,
        private TravelOfferAvailabilityAssigner $travelOfferAvailabilityAssigner,
    ) {
    }

    public function sync(SyncContext $context): SyncReport
    {
        $availabilityOfferDtos = $this->feedReader->readAvailabilityFeed($context->getFilePath());

        $report = new SyncReport();
        $report->addRecordsRead(count($availabilityOfferDtos));

        if (null !== $context->getLimit()) {
            $availabilityOfferDtos = array_slice($availabilityOfferDtos, 0, $context->getLimit());
        }

        $report->addRecordsSelected(count($availabilityOfferDtos));

        foreach ($availabilityOfferDtos as $availabilityOfferDto) {
            $validationResult = $this->availabilityOfferValidator->validate($availabilityOfferDto);

            if (!$validationResult->isValid()) {
                $report->incrementRecordsInvalid();
                continue;
            }

            try {
                $travelOffer = $this->travelOfferRepository->findOneBySupplierOfferId(
                    $availabilityOfferDto->getSupplierOfferId()->getValue(),
                );

                if (null === $travelOffer) {
                    $report->incrementRecordsSkipped();
                    continue;
                }

                $travelPeriod = $this->travelOfferAvailabilityRepository->findOneBySupplierOfferIdAndTravelPeriod(
                    $availabilityOfferDto->getSupplierOfferId()->getValue(),
                    $availabilityOfferDto->getTravelPeriod()
                );

                if (null === $travelPeriod) {
                    if ($context->isDryRun()) {
                        $travelPeriod = $this->travelOfferAvailabilityRepository->createPreviewFromDto(
                            $availabilityOfferDto
                        );
                    } else {
                        $travelPeriod = $this->travelOfferAvailabilityRepository->createFromDto(
                            $availabilityOfferDto,
                            $travelOffer
                        );
                        $report->incrementTravelOffersUpdated();
                    }
                } elseif (!$context->isDryRun()) {
                    $this->travelOfferAvailabilityRepository->updateFromDto($travelPeriod, $availabilityOfferDto);
                    $report->incrementTravelOffersUpdated();
                }

                if (!$context->isDryRun()) {
                    $this->travelOfferAvailabilityAssigner->assign($travelPeriod, $travelOffer);
                }

                $report->incrementRecordsSucceeded();
            } catch (Throwable $exception) {
                $report->incrementRecordsFailed();

                throw new SyncException(
                    sprintf(
                        'Availability synchronization failed for supplier offer "%s".',
                        $availabilityOfferDto->getSupplierOfferId()->getValue(),
                    ),
                    0,
                    $exception,
                );
            }
        }

        return $report;
    }
}
