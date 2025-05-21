<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\ConstraintInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Decimal implements ConstraintInterface
{
    public function __construct(
        public int    $scale,                    // digits after the decimal point
        public string $message = 'Number has too many decimal places.'
    ) {}
}