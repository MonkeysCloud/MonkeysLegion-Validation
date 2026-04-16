<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;

/**
 * Collection size must fall within the given bounds.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class Count implements ConstraintInterface
{
    public function __construct(
        public int $min = 0,
        public int $max = PHP_INT_MAX,
        public string $message = 'Collection size constraint violated.',
    ) {}

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if ($value === null) {
            return null;
        }

        if (!is_countable($value) && !is_iterable($value)) {
            return new ValidationError($field, 'Value must be countable.');
        }

        $size = is_countable($value) ? count($value) : iterator_count($value);

        if ($size < $this->min || $size > $this->max) {
            return new ValidationError($field, $this->message);
        }

        return null;
    }
}