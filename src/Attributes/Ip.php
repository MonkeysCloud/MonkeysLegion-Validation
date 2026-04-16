<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;

/**
 * Value must be a valid IP address.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class Ip implements ConstraintInterface
{
    public function __construct(
        public bool $allowV6 = true,
        public string $message = 'Value must be a valid IP address.',
    ) {}

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if ($value === null || $value === '') {
            return null;
        }

        $flags = $this->allowV6
            ? FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6
            : FILTER_FLAG_IPV4;

        if (!filter_var($value, FILTER_VALIDATE_IP, $flags)) {
            return new ValidationError($field, $this->message);
        }

        return null;
    }
}