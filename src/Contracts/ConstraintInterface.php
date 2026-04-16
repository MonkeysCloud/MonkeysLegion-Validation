<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Contracts;

use MonkeysLegion\Validation\ValidationError;

/**
 * Contract for self-validating constraint attributes.
 *
 * Each validation attribute implements this interface, encapsulating
 * its own validation logic rather than relying on a central validator.
 */
interface ConstraintInterface
{
    /**
     * Validate a value against this constraint.
     *
     * @param mixed  $value The value to validate
     * @param string $field The property/field name (for error messages)
     * @param object $dto   The parent DTO (for cross-field validation)
     *
     * @return ValidationError|null Null if valid, error object if invalid
     */
    public function validate(mixed $value, string $field, object $dto): ?ValidationError;
}
