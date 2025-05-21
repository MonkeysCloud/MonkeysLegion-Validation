<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\ConstraintInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Url implements ConstraintInterface
{
    public function __construct(
        public string $message = 'Value must be a valid URL.'
    ) {}
}