<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\ConstraintInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Max implements ConstraintInterface
{
    public function __construct(
        public int|float $value,
        public string    $message = 'Value is above the maximum.'
    ) {}
}