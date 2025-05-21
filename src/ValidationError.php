<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation;

/**
 * Immutable value object representing a single validation failure.
 */
final readonly class ValidationError
{
    public function __construct(
        public string $property,
        public string $message
    ) {}
}