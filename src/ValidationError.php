<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation;

/**
 * Immutable value object representing a single validation failure.
 */
final readonly class ValidationError
{
    public function __construct(
        public string $field,
        public string $message,
    ) {}

    /**
     * Serialize to array for JSON responses.
     *
     * @return array{field: string, message: string}
     */
    public function toArray(): array
    {
        return [
            'field'   => $this->field,
            'message' => $this->message,
        ];
    }
}