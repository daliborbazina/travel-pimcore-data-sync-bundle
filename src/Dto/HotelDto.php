<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Dto;

final readonly class HotelDto
{
    /**
     * @param array<int, string> $mediaUrls
     */
    public function __construct(
        private string $hotelName,
        private ?string $sourceUrl,
        private ?string $teaser = null,
        private ?string $description = null,
        private array $mediaUrls = [],
    ) {
    }

    public function getName(): string
    {
        return $this->hotelName;
    }

    public function getTeaser(): ?string
    {
        return $this->teaser;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return array<int, string>
     */
    public function getMediaUrls(): array
    {
        return $this->mediaUrls;
    }

    public function getSourceUrl(): ?string
    {
        return $this->sourceUrl;
    }
}
