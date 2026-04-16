<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\Contracts\ValidatorInterface;

use ReflectionClass;
use ReflectionProperty;

/**
 * Attribute-based validator using self-validating constraints.
 *
 * Inspects public properties for ConstraintInterface attributes
 * and delegates validation to each constraint.
 */
final class Validator implements ValidatorInterface
{
    /**
     * Validate a DTO against its attribute constraints.
     */
    public function validate(object $dto): ValidationResult
    {
        $errors = [];
        $ref = new ReflectionClass($dto);

        foreach ($ref->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
            $value = $prop->getValue($dto);

            foreach ($prop->getAttributes() as $attr) {
                $instance = $attr->newInstance();

                if ($instance instanceof ConstraintInterface) {
                    $error = $instance->validate($value, $prop->getName(), $dto);

                    if ($error !== null) {
                        $errors[] = $error;
                    }
                }
            }
        }

        return new ValidationResult($errors);
    }
}
