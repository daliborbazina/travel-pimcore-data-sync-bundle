<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Source;

use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\CatalogOfferDto;
use DaliborBazina\TravelPimcoreDataSyncBundle\Dto\TravelOfferAvailabilityDto;
use DaliborBazina\TravelPimcoreDataSyncBundle\Exception\SyncException;
use DaliborBazina\TravelPimcoreDataSyncBundle\Mapper\CatalogOfferDataMapper;
use DaliborBazina\TravelPimcoreDataSyncBundle\Mapper\TravelOfferAvailabilityDataMapper;
use JsonException;

use function file_get_contents;
use function is_array;
use function json_decode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

final readonly class JsonFeedReader
{
    public function __construct(
        private CatalogOfferDataMapper $catalogOfferMapper,
        private TravelOfferAvailabilityDataMapper $travelOfferAvailabilityMapper,
    ) {
    }

    /**
     * @return array<int, CatalogOfferDto>
     */
    public function readCatalogFeed(string $filePath): array
    {
        $rows = $this->readRows($filePath);
        $items = [];

        foreach ($rows as $row) {
            $items[] = $this->catalogOfferMapper->map($row);
        }

        return $items;
    }

    /**
     * @return array<int, TravelOfferAvailabilityDto>
     */
    public function readAvailabilityFeed(string $filePath): array
    {
        $rows = $this->readRows($filePath);
        $items = [];

        foreach ($rows as $row) {
            $items[] = $this->travelOfferAvailabilityMapper->map($row);
        }

        return $items;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function readRows(string $filePath): array
    {
        $contents = file_get_contents($filePath);

        if (false === $contents) {
            throw new SyncException(sprintf('Unable to read JSON feed file "%s".', $filePath));
        }

        try {
            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new SyncException(
                sprintf('Unable to decode JSON feed file "%s".', $filePath),
                0,
                $exception,
            );
        }

        if (!is_array($data)) {
            throw new SyncException(
                sprintf('JSON feed file "%s" must contain an array as the root element.', $filePath),
            );
        }


        $rows = [];

        foreach ($data as $row) {
            if (!is_array($row)) {
                continue;
            }

            $rows[] = $row;
        }

        return $rows;
    }
}
