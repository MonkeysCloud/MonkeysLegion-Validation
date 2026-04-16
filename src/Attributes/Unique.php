<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;

/**
 * Marker: value must be unique (consumer provides checker callback).
 *
 * This attribute flags a field as requiring uniqueness.
 * A checker callable must be provided which returns true if the value exists.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class Unique implements ConstraintInterface
{
    public function __construct(
        public string $message = 'Value must be unique.',
    ) {}

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        // Uniqueness requires external state (DB) — this is a marker-only
        // constraint. The Validator resolves uniqueness via registered checkers.
        return null;
    }
}
