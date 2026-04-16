<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;
use Closure;

/**
 * Custom validation via callable.
 *
 * The callback receives the value and must return true if valid.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Callback implements ConstraintInterface
{
    /** @var Closure(mixed): bool */
    private Closure $callback;

    /**
     * @param callable(mixed): bool $callback
     */
    public function __construct(
        callable $callback,
        public readonly string $message = 'Callback validation failed.',
    ) {
        $this->callback = $callback(...);
    }

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if (!($this->callback)($value)) {
            return new ValidationError($field, $this->message);
        }

        return null;
    }
}
