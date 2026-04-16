<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation;

/**
 * Structured validation result with PHP 8.4 property hooks.
 *
 * Usage:
 * ```php
 * $result = $validator->validate($dto);
 * if (!$result->isValid) {
 *     foreach ($result->errors as $error) { ... }
 * }
 * ```
 */
final class ValidationResult
{
    // ── Properties with hooks ────────────────────────────────────

    /** @var list<ValidationError> */
    private array $validationErrors;

    public bool $isValid {
        get => $this->validationErrors === [];
    }

    /** @var list<ValidationError> */
    public array $errors {
        get => $this->validationErrors;
    }

    public int $errorCount {
        get => count($this->validationErrors);
    }

    // ── Constructor ──────────────────────────────────────────────

    /**
     * @param list<ValidationError> $errors
     */
    public function __construct(array $errors = [])
    {
        $this->validationErrors = array_values($errors);
    }

    // ── Query methods ────────────────────────────────────────────

    /**
     * Get errors for a specific field.
     *
     * @return list<ValidationError>
     */
    public function errorsFor(string $field): array
    {
        return array_values(
            array_filter(
                $this->validationErrors,
                static fn(ValidationError $e): bool => $e->field === $field,
            ),
        );
    }

    /**
     * Check if a specific field has errors.
     */
    public function hasErrorsFor(string $field): bool
    {
        foreach ($this->validationErrors as $error) {
            if ($error->field === $field) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get first error message for a field, or null.
     */
    public function firstError(string $field): ?string
    {
        foreach ($this->validationErrors as $error) {
            if ($error->field === $field) {
                return $error->message;
            }
        }

        return null;
    }

    /**
     * Serialize all errors to array format.
     *
     * @return list<array{field: string, message: string}>
     */
    public function toArray(): array
    {
        return array_map(
            static fn(ValidationError $e): array => $e->toArray(),
            $this->validationErrors,
        );
    }

    /**
     * Throw a ValidationException if there are errors.
     *
     * @throws Exceptions\ValidationException
     */
    public function throwIfInvalid(): void
    {
        if (!$this->isValid) {
            throw Exceptions\ValidationException::fromResult($this);
        }
    }
}
