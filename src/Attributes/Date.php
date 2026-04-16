<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;
use DateTimeImmutable;

/**
 * Value must be a valid date (Y-m-d format or custom).
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class Date implements ConstraintInterface
{
    public function __construct(
        public string $format = 'Y-m-d',
        public string $message = 'Value must be a valid date.',
    ) {}

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if ($value === null || $value === '') {
            return null;
        }

        $parsed = DateTimeImmutable::createFromFormat($this->format, (string) $value);

        if ($parsed === false || $parsed->format($this->format) !== (string) $value) {
            return new ValidationError($field, $this->message);
        }

        return null;
    }
}