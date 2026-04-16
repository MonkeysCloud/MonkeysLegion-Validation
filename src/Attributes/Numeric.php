<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;

/**
 * Value must be numeric (integers, floats, and numeric strings).
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class Numeric implements ConstraintInterface
{
    public function __construct(
        public string $message = 'Value must be numeric.',
    ) {}

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return new ValidationError($field, $this->message);
        }

        return null;
    }
}