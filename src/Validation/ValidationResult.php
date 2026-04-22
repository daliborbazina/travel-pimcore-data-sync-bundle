<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Validation;

final readonly class ValidationResult
{
    /**
     * @param array<int, string> $errors
     */
    public function __construct(
        private array $errors = [],
    ) {
    }

    public function isValid(): bool
    {
        return [] === $this->errors;
    }

    /**
     * @return array<int, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
