<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;

/**
 * Value must be a valid e-mail address.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class Email implements ConstraintInterface
{
    public function __construct(
        public string $message = 'Value must be a valid e-mail.',
    ) {}

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if ($value === null || $value === '') {
            return null; // Use NotBlank for required checks
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return new ValidationError($field, $this->message);
        }

        return null;
    }
}