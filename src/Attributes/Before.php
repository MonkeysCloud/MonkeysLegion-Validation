<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;
use DateTimeImmutable;
use ReflectionProperty;

/**
 * Date value must be before another field's date.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class Before implements ConstraintInterface
{
    public function __construct(
        public string $otherField,
        public string $message = 'Value must be before :other.',
    ) {}

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!property_exists($dto, $this->otherField)) {
            return new ValidationError($field, "Referenced field '{$this->otherField}' does not exist.");
        }

        $ref = new ReflectionProperty($dto, $this->otherField);
        $other = $ref->getValue($dto);

        if ($other === null || $other === '') {
            return null;
        }

        try {
            $current = new DateTimeImmutable((string) $value);
            $compare = new DateTimeImmutable((string) $other);
        } catch (\Exception) {
            return new ValidationError($field, 'Value is not a valid date.');
        }

        if ($current >= $compare) {
            $msg = str_replace(':other', $this->otherField, $this->message);

            return new ValidationError($field, $msg);
        }

        return null;
    }
}