<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\ConstraintInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class UuidV4 implements ConstraintInterface
{
    public function __construct(
        public string $message = 'Value must be a valid UUIDv4.'
    ) {}
}