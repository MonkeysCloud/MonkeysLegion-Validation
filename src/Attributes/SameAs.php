<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;
use ReflectionProperty;

/**
 * Value must match another field on the same DTO.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class SameAs implements ConstraintInterface
{
    public function __construct(
        public string $otherField,
        public string $message = 'Value must match :other.',
    ) {}

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if (!property_exists($dto, $this->otherField)) {
            return new ValidationError($field, "Referenced field '{$this->otherField}' does not exist.");
        }

        $ref = new ReflectionProperty($dto, $this->otherField);
        $other = $ref->getValue($dto);

        if ($value !== $other) {
            $msg = str_replace(':other', $this->otherField, $this->message);

            return new ValidationError($field, $msg);
        }

        return null;
    }
}