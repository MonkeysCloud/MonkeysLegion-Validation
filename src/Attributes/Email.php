<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Email
{
    public function __construct(public string $message = 'Value must be a valid e-mail.') {}
}