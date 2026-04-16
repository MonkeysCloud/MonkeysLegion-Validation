<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Tests;

use MonkeysLegion\Validation\Exceptions\ValidationException;
use MonkeysLegion\Validation\ValidationError;
use MonkeysLegion\Validation\ValidationResult;

use PHPUnit\Framework\TestCase;

final class ValidationExceptionTest extends TestCase
{
    public function test_from_result(): void
    {
        $result = new ValidationResult([
            new ValidationError('email', 'Invalid'),
            new ValidationError('name', 'Required'),
        ]);

        $exception = ValidationException::fromResult($result);

        $this->assertSame(422, $exception->getCode());
        $this->assertStringContainsString('2 errors', $exception->getMessage());
        $this->assertSame($result, $exception->result);
    }

    public function test_singular_message(): void
    {
        $result = new ValidationResult([
            new ValidationError('email', 'Invalid'),
        ]);

        $exception = ValidationException::fromResult($result);

        $this->assertStringContainsString('1 error', $exception->getMessage());
        $this->assertStringNotContainsString('errors', $exception->getMessage());
    }
}
