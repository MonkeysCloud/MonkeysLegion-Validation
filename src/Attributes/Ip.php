<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\ConstraintInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Ip implements ConstraintInterface
{
    public function __construct(
        public bool   $allowV6 = true,
        public string $message = 'Value must be a valid IP address.'
    ) {}
}