<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Sync;

final class SyncReport
{
    private int $recordsRead = 0;
    private int $recordsSelected = 0;

    private int $recordsSucceeded = 0;
    private int $recordsInvalid = 0;
    private int $recordsFailed = 0;
    private int $recordsSkipped = 0;

    private int $destinationsCreated = 0;
    private int $destinationsUpdated = 0;

    private int $hotelsCreated = 0;
    private int $hotelsUpdated = 0;

    private int $travelOffersCreated = 0;
    private int $travelOffersUpdated = 0;

    public function addRecordsRead(int $count): void
    {
        $this->recordsRead += $count;
    }

    public function addRecordsSelected(int $count): void
    {
        $this->recordsSelected += $count;
    }

    public function incrementRecordsRead(): void
    {
        ++$this->recordsRead;
    }

    public function incrementRecordsSelected(): void
    {
        ++$this->recordsSelected;
    }

    public function incrementRecordsSucceeded(): void
    {
        ++$this->recordsSucceeded;
    }

    public function incrementRecordsInvalid(): void
    {
        ++$this->recordsInvalid;
    }

    public function incrementRecordsFailed(): void
    {
        ++$this->recordsFailed;
    }

    public function incrementRecordsSkipped(): void
    {
        ++$this->recordsSkipped;
    }

    public function incrementDestinationsCreated(): void
    {
        ++$this->destinationsCreated;
    }

    public function incrementDestinationsUpdated(): void
    {
        ++$this->destinationsUpdated;
    }

    public function incrementHotelsCreated(): void
    {
        ++$this->hotelsCreated;
    }

    public function incrementHotelsUpdated(): void
    {
        ++$this->hotelsUpdated;
    }

    public function incrementTravelOffersCreated(): void
    {
        ++$this->travelOffersCreated;
    }

    public function incrementTravelOffersUpdated(): void
    {
        ++$this->travelOffersUpdated;
    }

    public function getRecordsRead(): int
    {
        return $this->recordsRead;
    }

    public function getRecordsSelected(): int
    {
        return $this->recordsSelected;
    }

    public function getRecordsSucceeded(): int
    {
        return $this->recordsSucceeded;
    }

    public function getRecordsInvalid(): int
    {
        return $this->recordsInvalid;
    }

    public function getRecordsFailed(): int
    {
        return $this->recordsFailed;
    }

    public function getRecordsSkipped(): int
    {
        return $this->recordsSkipped;
    }

    public function getDestinationsCreated(): int
    {
        return $this->destinationsCreated;
    }

    public function getDestinationsUpdated(): int
    {
        return $this->destinationsUpdated;
    }

    public function getHotelsCreated(): int
    {
        return $this->hotelsCreated;
    }

    public function getHotelsUpdated(): int
    {
        return $this->hotelsUpdated;
    }

    public function getTravelOffersCreated(): int
    {
        return $this->travelOffersCreated;
    }

    public function getTravelOffersUpdated(): int
    {
        return $this->travelOffersUpdated;
    }

    public function getProcessed(): int
    {
        return $this->recordsSucceeded
            + $this->recordsInvalid
            + $this->recordsFailed
            + $this->recordsSkipped;
    }

    public function getUnprocessed(): int
    {
        return $this->recordsSelected - $this->getProcessed();
    }
}
