<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\ConstraintInterface;

/**
 * Ensures this date is BEFORE the date in another property of the same DTO.
 *
 * Example:
 *   #[Before('endDate')]
 *   public string $startDate;
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Before implements ConstraintInterface
{
    public function __construct(
        /** Property name to compare against */
        public string $otherField,
        public string $message = 'Date must be before %other%.'
    ) {}
}