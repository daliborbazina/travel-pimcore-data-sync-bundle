<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Setup;

use Carbon\Carbon;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Destination;
use Pimcore\Model\DataObject\Hotel;
use Pimcore\Model\DataObject\Service as DataObjectService;
use Pimcore\Model\DataObject\TravelOffer;
use Pimcore\Model\DataObject\TravelOfferAvailability;
use Pimcore\Model\Element\Service as ElementService;
use RuntimeException;

final class SandboxDataSeeder
{
    public function seed(): void
    {
        $this->assertGeneratedClassesExist();

        $folders = $this->ensureFolders();

        $destination = $this->upsertDestination(
            $folders['destinations'],
            'hr-istria',
            'HR-ISTRIA',
            'Istria',
            'HR',
            'Coastal destination for package travel content.',
            'Structured travel destination used for sandbox import and preview flows.',
            'active',
        );

        $hotel = $this->upsertHotel(
            $folders['hotels'],
            $destination,
            'adriatic-grand-hotel',
            'HOTEL-1001',
            'Adriatic Grand Hotel',
            '4-star hotel close to the sea.',
            'Demo hotel used for Pimcore travel data synchronization.',
            'active',
        );

        $offer = $this->upsertTravelOffer(
            $folders['offers'],
            $destination,
            $hotel,
            'offer-summer-family-001',
            'OFFER-2001',
            '7 nights with breakfast included.',
            'Structured travel offer prepared for downstream payload preview.',
            'active',
            '2026-06-10',
            '2026-06-17',
            1199.00,
            'EUR',
            true,
            true,
        );

        $this->upsertAvailability(
            $offer,
            'availability-001',
            'AV-3001',
            '2026-06-10',
            '2026-06-17',
            7,
            1199.00,
            'EUR',
            true,
            'available',
        );

        $this->upsertAvailability(
            $offer,
            'availability-002',
            'AV-3002',
            '2026-06-17',
            '2026-06-24',
            7,
            1299.00,
            'EUR',
            true,
            'limited',
        );
    }

    private function assertGeneratedClassesExist(): void
    {
        $classes = [
            Destination::class,
            Hotel::class,
            TravelOffer::class,
            TravelOfferAvailability::class,
        ];

        foreach ($classes as $class) {
            if (class_exists($class)) {
                continue;
            }

            throw new RuntimeException(
                sprintf(
                    'Generated Pimcore DataObject class is missing: %s. Run class rebuild first.',
                    $class
                )
            );
        }
    }

    /**
     * @return array{
     *     travel: DataObject\Folder,
     *     destinations: DataObject\Folder,
     *     hotels: DataObject\Folder,
     *     offers: DataObject\Folder
     * }
     */
    private function ensureFolders(): array
    {
        $travelFolder = DataObjectService::createFolderByPath('/Travel');
        $destinationFolder = DataObjectService::createFolderByPath('/Travel/Destinations');
        $hotelFolder = DataObjectService::createFolderByPath('/Travel/Hotels');
        $offerFolder = DataObjectService::createFolderByPath('/Travel/Offers');

        if (
            !$travelFolder instanceof DataObject\Folder
            || !$destinationFolder instanceof DataObject\Folder
            || !$hotelFolder instanceof DataObject\Folder
            || !$offerFolder instanceof DataObject\Folder
        ) {
            throw new RuntimeException('Failed to create sandbox folders.');
        }

        return [
            'travel' => $travelFolder,
            'destinations' => $destinationFolder,
            'hotels' => $hotelFolder,
            'offers' => $offerFolder,
        ];
    }

    private function upsertDestination(
        DataObject\Folder $parent,
        string $key,
        string $destinationCode,
        string $name,
        string $countryCode,
        string $teaser,
        string $description,
        string $status,
    ): Destination {
        $path = $parent->getFullPath().'/'.$key;
        $object = DataObject::getByPath($path);

        if (!$object instanceof Destination) {
            $object = new Destination();
            $object->setKey(ElementService::getValidKey($key, 'object'));
            $object->setParent($parent);
            $object->setPublished(true);
        }

        $object->setDestinationCode($destinationCode);
        $object->setName($name);
        $object->setCountryCode($countryCode);
        $object->setTeaser($teaser);
        $object->setDescription($description);
        $object->setStatus($status);
        $object->save();

        return $object;
    }

    private function upsertHotel(
        DataObject\Folder $parent,
        Destination $destination,
        string $key,
        string $supplierHotelId,
        string $hotelName,
        string $teaser,
        string $description,
        string $status,
    ): Hotel {
        $path = $parent->getFullPath().'/'.$key;
        $object = DataObject::getByPath($path);

        if (!$object instanceof Hotel) {
            $object = new Hotel();
            $object->setKey(ElementService::getValidKey($key, 'object'));
            $object->setParent($parent);
            $object->setPublished(true);
        }

        $object->setSupplierHotelId($supplierHotelId);
        $object->setHotelName($hotelName);
        $object->setTeaser($teaser);
        $object->setDescription($description);
        $object->setStatus($status);
        $object->setDestination($destination);
        $object->save();

        return $object;
    }

    private function upsertTravelOffer(
        DataObject\Folder $parent,
        Destination $destination,
        Hotel $hotel,
        string $key,
        string $supplierOfferId,
        string $teaser,
        string $description,
        string $status,
        string $travelStartDate,
        string $travelEndDate,
        float $priceHint,
        string $currency,
        bool $available,
        bool $publishable,
    ): TravelOffer {
        $path = $parent->getFullPath().'/'.$key;
        $object = DataObject::getByPath($path);

        if (!$object instanceof TravelOffer) {
            $object = new TravelOffer();
            $object->setKey(ElementService::getValidKey($key, 'object'));
            $object->setParent($parent);
            $object->setPublished(true);
        }

        $object->setSupplierOfferId($supplierOfferId);
        $object->setTeaser($teaser);
        $object->setDescription($description);
        $object->setStatus($status);
        $object->setTravelStartDate($this->createDate($travelStartDate));
        $object->setTravelEndDate($this->createDate($travelEndDate));
        $object->setPriceHint($this->formatDecimal($priceHint));
        $object->setCurrency($currency);
        $object->setAvailable($available);
        $object->setPublishable($publishable);
        $object->setDestination($destination);
        $object->setHotel($hotel);
        $object->save();

        return $object;
    }

    private function upsertAvailability(
        TravelOffer $travelOffer,
        string $key,
        string $supplierOfferId,
        string $availableFrom,
        string $availableTo,
        int $nights,
        float $priceHint,
        string $currency,
        bool $available,
        string $status,
    ): TravelOfferAvailability {
        $parent = $this->ensureAvailabilityFolder($travelOffer);

        $path = $parent->getFullPath().'/'.$key;
        $object = DataObject::getByPath($path);

        if (!$object instanceof TravelOfferAvailability) {
            $object = new TravelOfferAvailability();
            $object->setKey(ElementService::getValidKey($key, 'object'));
            $object->setParent($parent);
            $object->setPublished(true);
        }

        $object->setSupplierOfferId($supplierOfferId);
        $object->setAvailableFrom($this->createDate($availableFrom));
        $object->setAvailableTo($this->createDate($availableTo));
        $object->setNights($nights);
        $object->setPriceHint($this->formatDecimal($priceHint));
        $object->setCurrency($currency);
        $object->setAvailable($available);
        $object->setStatus($status);
        $object->setTravelOffer($travelOffer);
        $object->save();

        return $object;
    }

    private function ensureAvailabilityFolder(TravelOffer $travelOffer): DataObject\Folder
    {
        $folder = DataObjectService::createFolderByPath($travelOffer->getFullPath().'/availabilities');

        if ($folder instanceof DataObject\Folder) {
            return $folder;
        }

        throw new RuntimeException('Failed to create availability folder.');
    }

    private function createDate(string $value): Carbon
    {
        $date = Carbon::createFromFormat('Y-m-d', $value);

        if (!$date instanceof Carbon) {
            throw new RuntimeException(
                sprintf(
                    'Invalid date value "%s". Expected format: Y-m-d.',
                    $value
                )
            );
        }

        return $date->startOfDay();
    }

    private function formatDecimal(float $value): string
    {
        return number_format($value, 2, '.', '');
    }
}
