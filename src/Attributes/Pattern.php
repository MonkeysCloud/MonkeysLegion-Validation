<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;

/**
 * Value must match a regular expression.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class Pattern implements ConstraintInterface
{
    public function __construct(
        public string $regex,
        public string $message = 'Value does not match the required pattern.',
    ) {}

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!preg_match($this->regex, (string) $value)) {
            return new ValidationError($field, $this->message);
        }

        return null;
    }
}