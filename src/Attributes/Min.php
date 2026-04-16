<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;

/**
 * Numeric value must be at least this minimum.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class Min implements ConstraintInterface
{
    public function __construct(
        public int|float $value,
        public string $message = 'Value is below the minimum.',
    ) {}

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ((float) $value < (float) $this->value) {
            return new ValidationError($field, $this->message);
        }

        return null;
    }
}