<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;
use Closure;

/**
 * Value must be unique — resolved via a registered checker callable.
 *
 * The checker receives the value and must return true if a duplicate exists.
 * If no checker is set, the constraint is a no-op marker.
 *
 * Usage with checker:
 * ```php
 * $unique = new Unique();
 * $unique->setChecker(fn(mixed $v) => $userRepo->existsByEmail($v));
 * ```
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Unique implements ConstraintInterface
{
    /** @var (Closure(mixed): bool)|null */
    private ?Closure $checker = null;

    public function __construct(
        public readonly string $message = 'Value must be unique.',
    ) {}

    /**
     * Register a callable that returns true when a duplicate exists.
     *
     * @param callable(mixed): bool $checker
     */
    public function setChecker(callable $checker): void
    {
        $this->checker = $checker(...);
    }

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if ($value === null || $value === '') {
            return null;
        }

        // No checker registered — skip (marker-only mode)
        if ($this->checker === null) {
            return null;
        }

        if (($this->checker)($value)) {
            return new ValidationError($field, $this->message);
        }

        return null;
    }
}
