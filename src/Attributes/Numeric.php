<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\ConstraintInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Numeric implements ConstraintInterface
{
    public function __construct(
        public string $message = 'Value must contain only digits.'
    ) {}
}