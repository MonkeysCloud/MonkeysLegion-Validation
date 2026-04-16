<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;

/**
 * Value must not be null, empty string, or empty array.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class NotBlank implements ConstraintInterface
{
    public function __construct(
        public string $message = 'Value must not be blank.',
    ) {}

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if ($value === null || $value === '' || $value === []) {
            return new ValidationError($field, $this->message);
        }

        return null;
    }
}