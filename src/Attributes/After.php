<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\ConstraintInterface;

/**
 * Ensures this date is AFTER the date in another property of the same DTO.
 *
 * Example:
 *   #[After('startDate')]
 *   public string $endDate;
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class After implements ConstraintInterface
{
    public function __construct(
        /** Property name to compare against */
        public string $otherField,
        public string $message = 'Date must be after %other%.'
    ) {}
}