<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Auth\Security\PwnedPasswordChecker;
use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;

/**
 * Validates that a password has NOT been found in known data breaches
 * using the Have I Been Pwned API (k-anonymity model).
 *
 * PHP attributes cannot receive constructor injection. Set the
 * PwnedPasswordChecker instance via NotPwned::setChecker() at boot.
 * If no checker is set, validation is skipped (fail-open).
 *
 * @copyright 2026 MonkeysCloud Team
 * @license   MIT
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class NotPwned implements ConstraintInterface
{
    public function __construct(
        public readonly string $message = 'This password has been found in a data breach. Please choose a different password.',
    ) {}

    /**
     * Set the PwnedPasswordChecker instance (call at application boot).
     */
    public static function setChecker(PwnedPasswordChecker $checker): void
    {
        $GLOBALS['__ml_not_pwned_checker'] = $checker;
    }

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        // Skip if value is null or empty (use NotBlank for required checks).
        if ($value === null || $value === '') {
            return null;
        }

        $checker = $GLOBALS['__ml_not_pwned_checker'] ?? null;

        // If no checker is configured, skip validation (fail-open).
        if (!$checker instanceof PwnedPasswordChecker) {
            return null;
        }

        // Skip if checker is disabled.
        if (!$checker->isEnabled()) {
            return null;
        }

        if ($checker->isPwned((string) $value)) {
            return new ValidationError($field, $this->message);
        }

        return null;
    }
}
