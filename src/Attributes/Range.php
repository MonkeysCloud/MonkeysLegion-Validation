<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;

/**
 * Numeric value must fall within the given range.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class Range implements ConstraintInterface
{
    public function __construct(
        public int|float $min,
        public int|float $max,
        public string $message = 'Value is out of range.',
    ) {}

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if ($value === null || $value === '') {
            return null;
        }

        $n = (float) $value;

        if ($n < (float) $this->min || $n > (float) $this->max) {
            return new ValidationError($field, $this->message);
        }

        return null;
    }
}