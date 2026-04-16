<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Contracts;

use MonkeysLegion\Validation\ValidationResult;

/**
 * Validates a DTO and returns a structured result.
 */
interface ValidatorInterface
{
    /**
     * Validate a data-transfer object decorated with constraint attributes.
     *
     * @param object $dto A DTO with #[NotBlank], #[Email], etc. attributes
     */
    public function validate(object $dto): ValidationResult;
}
