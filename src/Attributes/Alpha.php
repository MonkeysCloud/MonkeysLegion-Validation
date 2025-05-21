<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\ConstraintInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Alpha implements ConstraintInterface
{
    public function __construct(
        public string $message = 'Value must contain only letters [a-zA-Z].'
    ) {}
}