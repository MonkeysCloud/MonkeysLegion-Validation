<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Length
{
    public function __construct(
        public int    $min = 0,
        public int    $max = PHP_INT_MAX,
        public string $message = 'Length constraint violated.'
    ) {}
}