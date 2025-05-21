<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\ConstraintInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Choice implements ConstraintInterface
{
    /**
     * @param array<scalar> $choices
     */
    public function __construct(
        public array  $choices,
        public string $message = 'Value is not an allowed choice.'
    ) {}
}