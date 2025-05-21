<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\ConstraintInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Pattern implements ConstraintInterface
{
    public function __construct(
        public string $regex,
        public string $message = 'Value does not match required pattern.'
    ) {}
}