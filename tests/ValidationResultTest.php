<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Tests;

use MonkeysLegion\Validation\Exceptions\ValidationException;
use MonkeysLegion\Validation\ValidationError;
use MonkeysLegion\Validation\ValidationResult;

use PHPUnit\Framework\TestCase;

final class ValidationResultTest extends TestCase
{
    public function test_empty_result_is_valid(): void
    {
        $result = new ValidationResult();
        $this->assertTrue($result->isValid);
        $this->assertSame(0, $result->errorCount);
        $this->assertSame([], $result->errors);
    }

    public function test_result_with_errors_is_invalid(): void
    {
        $result = new ValidationResult([
            new ValidationError('email', 'Invalid email'),
            new ValidationError('name', 'Required'),
        ]);

        $this->assertFalse($result->isValid);
        $this->assertSame(2, $result->errorCount);
    }

    public function test_errors_for_field(): void
    {
        $result = new ValidationResult([
            new ValidationError('email', 'Invalid email'),
            new ValidationError('email', 'Too long'),
            new ValidationError('name', 'Required'),
        ]);

        $emailErrors = $result->errorsFor('email');
        $this->assertCount(2, $emailErrors);
        $this->assertSame('Invalid email', $emailErrors[0]->message);
    }

    public function test_has_errors_for(): void
    {
        $result = new ValidationResult([
            new ValidationError('email', 'Invalid'),
        ]);

        $this->assertTrue($result->hasErrorsFor('email'));
        $this->assertFalse($result->hasErrorsFor('name'));
    }

    public function test_first_error(): void
    {
        $result = new ValidationResult([
            new ValidationError('email', 'First error'),
            new ValidationError('email', 'Second error'),
        ]);

        $this->assertSame('First error', $result->firstError('email'));
        $this->assertNull($result->firstError('name'));
    }

    public function test_to_array(): void
    {
        $result = new ValidationResult([
            new ValidationError('email', 'Invalid'),
        ]);

        $array = $result->toArray();
        $this->assertSame([['field' => 'email', 'message' => 'Invalid']], $array);
    }

    public function test_throw_if_invalid(): void
    {
        $result = new ValidationResult([
            new ValidationError('email', 'Invalid'),
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(422);
        $result->throwIfInvalid();
    }

    public function test_throw_if_invalid_does_nothing_when_valid(): void
    {
        $result = new ValidationResult();

        // Should not throw
        $result->throwIfInvalid();
        $this->assertTrue($result->isValid);
    }
}
