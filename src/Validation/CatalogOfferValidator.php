<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Validation;

use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\CatalogOfferDto;

final readonly class CatalogOfferValidator
{
    public function validate(CatalogOfferDto $catalogOfferDto): ValidationResult
    {
        $errors = [];

        if ('' === trim($catalogOfferDto->getSupplierOfferId()->getValue())) {
            $errors[] = 'Missing supplier offer ID.';
        }

        if ('' === trim($catalogOfferDto->getDestinationCode()->getValue())) {
            $errors[] = 'Missing destination code.';
        }

        if ('' === trim($catalogOfferDto->getDestinationName())) {
            $errors[] = 'Missing destination name.';
        }

        if ('' === trim($catalogOfferDto->getHotelName())) {
            $errors[] = 'Missing hotel name.';
        }

        return new ValidationResult($errors);
    }
}
