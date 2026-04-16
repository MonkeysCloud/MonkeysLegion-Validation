<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;

/**
 * String length must fall within the given bounds.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class Length implements ConstraintInterface
{
    public function __construct(
        public int $min = 0,
        public int $max = PHP_INT_MAX,
        public string $message = 'Length constraint violated.',
    ) {}

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if ($value === null || $value === '') {
            return null;
        }

        $len = mb_strlen((string) $value, 'UTF-8');

        if ($len < $this->min || $len > $this->max) {
            return new ValidationError($field, $this->message);
        }

        return null;
    }
}