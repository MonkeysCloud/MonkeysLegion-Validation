<?php

declare(strict_types=1);

use MonkeysLegion\Validation\ValidationResult;
use MonkeysLegion\Validation\Validator;

if (!function_exists('validate')) {
    /**
     * Validate a DTO with constraint attributes.
     *
     * @param object $dto A data-transfer object with validation attributes
     *
     * @return ValidationResult
     */
    function validate(object $dto): ValidationResult
    {
        static $validator = null;
        $validator ??= new Validator();

        return $validator->validate($dto);
    }
}
