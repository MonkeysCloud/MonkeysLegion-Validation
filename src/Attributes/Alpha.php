<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;

/**
 * Value must contain only alphabetic characters.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class Alpha implements ConstraintInterface
{
    public function __construct(
        public string $message = 'Value must contain only letters.',
    ) {}

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!preg_match('/^[a-zA-Z]+$/', (string) $value)) {
            return new ValidationError($field, $this->message);
        }

        return null;
    }
}