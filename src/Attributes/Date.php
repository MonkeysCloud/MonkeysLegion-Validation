<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\ConstraintInterface;

/**
 * Ensures a string matches the ISO 8601 date format YYYY-MM-DD.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Date implements ConstraintInterface
{
    public function __construct(
        public string $message = 'Value must be a valid ISO date (YYYY-MM-DD).'
    ) {}
}