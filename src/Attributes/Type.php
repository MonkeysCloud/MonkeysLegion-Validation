<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;

/**
 * Value must be of the specified PHP type.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class Type implements ConstraintInterface
{
    public function __construct(
        public string $type,
        public string $message = 'Value must be of type :type.',
    ) {}

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if ($value === null) {
            return null;
        }

        $valid = match ($this->type) {
            'string'  => is_string($value),
            'int', 'integer' => is_int($value),
            'float', 'double' => is_float($value),
            'bool', 'boolean' => is_bool($value),
            'array'   => is_array($value),
            'object'  => is_object($value),
            'numeric' => is_numeric($value),
            'scalar'  => is_scalar($value),
            default   => $value instanceof $this->type,
        };

        if (!$valid) {
            $msg = str_replace(':type', $this->type, $this->message);

            return new ValidationError($field, $msg);
        }

        return null;
    }
}
