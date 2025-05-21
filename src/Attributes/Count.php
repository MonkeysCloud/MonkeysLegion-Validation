<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\ConstraintInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Count implements ConstraintInterface
{
    public function __construct(
        public int    $min = 0,
        public int    $max = PHP_INT_MAX,
        public string $message = 'Collection size is out of bounds.'
    ) {}
}