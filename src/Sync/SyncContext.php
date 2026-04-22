<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Sync;

final readonly class SyncContext
{
    public function __construct(
        private string $filePath,
        private bool $dryRun = false,
        private ?int $limit = null,
    ) {
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function isDryRun(): bool
    {
        return $this->dryRun;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }
}
