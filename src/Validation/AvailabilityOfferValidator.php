<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Validation;

use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\TravelOfferAvailabilityDto;

final readonly class AvailabilityOfferValidator
{
    public function validate(TravelOfferAvailabilityDto $availabilityOfferDto): ValidationResult
    {
        $errors = [];

        if ('' === trim($availabilityOfferDto->getSupplierOfferId()->getValue())) {
            $errors[] = 'Missing supplier offer ID.';
        }

        if ('' === trim($availabilityOfferDto->getTravelPeriod()->getValue())) {
            $errors[] = 'Missing travel period.';
        }

        if (null !== $availabilityOfferDto->getPriceHint() && null === $availabilityOfferDto->getCurrency()) {
            $errors[] = 'Currency is required when price hint is provided.';
        }

        return new ValidationResult($errors);
    }
}
