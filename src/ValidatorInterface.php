<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation;

/**
 * Validates a DTO and returns a list of {@see ValidationError}s.
 */
interface ValidatorInterface
{
    /**
     * @param  object $dto  A data-transfer object with constraint attributes.
     * @return ValidationError[]  An array of validation errors (empty if valid).
     */
    public function validate(object $dto): array;
}