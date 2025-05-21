<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\ConstraintInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Range implements ConstraintInterface
{
    public function __construct(
        public int|float $min,
        public int|float $max,
        public string    $message = 'Value is outside the allowed range.'
    ) {}
}