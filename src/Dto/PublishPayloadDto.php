<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Dto;

final readonly class PublishPayloadDto
{
    /**
     * @param list<PublishTravelPeriodPayloadDto> $travelPeriods
     */
    public function __construct(
        private string $supplierOfferId,
        private array $travelPeriods,
        private ?string $teaser = null,
        private ?string $description = null,
        private ?string $status = null
    ) {
    }

    /**
     * @return array{
     *     supplier_offer_id: string,
     *     travel_periods: list<array{
     *         available_from: ?string,
     *         available_to: ?string,
     *         price_hint: ?string,
     *         currency: ?string,
     *         available: bool
     *     }>,
     *     teaser: ?string,
     *     description: ?string,
     *     status: ?string
     * }
     */
    public function toArray(): array
    {
        $travelPeriods = [];

        foreach ($this->travelPeriods as $travelPeriod) {
            $travelPeriods[] = $travelPeriod->toArray();
        }

        return [
            'supplier_offer_id' => $this->supplierOfferId,
            'travel_periods' => $travelPeriods,
            'teaser' => $this->teaser,
            'description' => $this->description,
            'status' => $this->status,
        ];
    }
}
