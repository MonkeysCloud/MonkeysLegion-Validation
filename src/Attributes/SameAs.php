<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\ConstraintInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class SameAs implements ConstraintInterface
{
    public function __construct(
        public string $otherField,
        public string $message = 'Value does not match %other%.'
    ) {}
}