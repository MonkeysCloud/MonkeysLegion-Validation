<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;

/**
 * Value must be a valid URL.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class Url implements ConstraintInterface
{
    public function __construct(
        public string $message = 'Value must be a valid URL.',
    ) {}

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            return new ValidationError($field, $this->message);
        }

        return null;
    }
}