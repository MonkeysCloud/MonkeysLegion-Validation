<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Exceptions;

use MonkeysLegion\Validation\ValidationResult;

use RuntimeException;

/**
 * Throwable validation failure carrying the full result.
 */
final class ValidationException extends RuntimeException
{
    // ── Properties ────────────────────────────────────────────────

    public readonly ValidationResult $result;

    // ── Constructor ───────────────────────────────────────────────

    private function __construct(string $message, ValidationResult $result)
    {
        parent::__construct($message, 422);
        $this->result = $result;
    }

    // ── Factory ───────────────────────────────────────────────────

    /**
     * Create from a validation result.
     */
    public static function fromResult(ValidationResult $result): self
    {
        $count = $result->errorCount;
        $message = "Validation failed with {$count} error" . ($count !== 1 ? 's' : '') . '.';

        return new self($message, $result);
    }
}
