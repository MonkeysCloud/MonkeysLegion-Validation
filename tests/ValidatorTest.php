<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Tests;

use MonkeysLegion\Validation\Attributes as Assert;
use MonkeysLegion\Validation\Validator;

use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    // ── NotBlank ─────────────────────────────────────────────────

    public function test_not_blank_fails_on_empty_string(): void
    {
        $dto = new class ('') {
            public function __construct(
                #[Assert\NotBlank]
                public string $name,
            ) {}
        };

        $result = $this->validator->validate($dto);
        $this->assertFalse($result->isValid);
        $this->assertSame(1, $result->errorCount);
        $this->assertTrue($result->hasErrorsFor('name'));
    }

    public function test_not_blank_passes_on_value(): void
    {
        $dto = new class ('hello') {
            public function __construct(
                #[Assert\NotBlank]
                public string $name,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── Email ────────────────────────────────────────────────────

    public function test_email_fails_on_invalid(): void
    {
        $dto = new class ('not-an-email') {
            public function __construct(
                #[Assert\Email]
                public string $email,
            ) {}
        };

        $result = $this->validator->validate($dto);
        $this->assertFalse($result->isValid);
    }

    public function test_email_passes_on_valid(): void
    {
        $dto = new class ('user@example.com') {
            public function __construct(
                #[Assert\Email]
                public string $email,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    public function test_email_skips_empty(): void
    {
        $dto = new class ('') {
            public function __construct(
                #[Assert\Email]
                public string $email,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── Length ────────────────────────────────────────────────────

    public function test_length_fails_too_short(): void
    {
        $dto = new class ('ab') {
            public function __construct(
                #[Assert\Length(min: 3, max: 10)]
                public string $code,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_length_fails_too_long(): void
    {
        $dto = new class ('this-is-way-too-long') {
            public function __construct(
                #[Assert\Length(min: 3, max: 10)]
                public string $code,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_length_passes(): void
    {
        $dto = new class ('hello') {
            public function __construct(
                #[Assert\Length(min: 3, max: 10)]
                public string $code,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── Pattern ──────────────────────────────────────────────────

    public function test_pattern_fails(): void
    {
        $dto = new class ('abc123') {
            public function __construct(
                #[Assert\Pattern(regex: '/^\d{3}$/')]
                public string $pin,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_pattern_passes(): void
    {
        $dto = new class ('123') {
            public function __construct(
                #[Assert\Pattern(regex: '/^\d{3}$/')]
                public string $pin,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── Alpha ────────────────────────────────────────────────────

    public function test_alpha_fails(): void
    {
        $dto = new class ('abc123') {
            public function __construct(
                #[Assert\Alpha]
                public string $name,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_alpha_passes(): void
    {
        $dto = new class ('Hello') {
            public function __construct(
                #[Assert\Alpha]
                public string $name,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── Alnum ────────────────────────────────────────────────────

    public function test_alnum_fails(): void
    {
        $dto = new class ('abc-123') {
            public function __construct(
                #[Assert\Alnum]
                public string $code,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_alnum_passes(): void
    {
        $dto = new class ('abc123') {
            public function __construct(
                #[Assert\Alnum]
                public string $code,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── Numeric ──────────────────────────────────────────────────

    public function test_numeric_fails(): void
    {
        $dto = new class ('hello') {
            public function __construct(
                #[Assert\Numeric]
                public string $amount,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_numeric_passes(): void
    {
        $dto = new class ('42.5') {
            public function __construct(
                #[Assert\Numeric]
                public string $amount,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── Min / Max / Range ────────────────────────────────────────

    public function test_min_fails(): void
    {
        $dto = new class (5) {
            public function __construct(
                #[Assert\Min(10)]
                public int $age,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_min_passes(): void
    {
        $dto = new class (18) {
            public function __construct(
                #[Assert\Min(10)]
                public int $age,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    public function test_max_fails(): void
    {
        $dto = new class (200) {
            public function __construct(
                #[Assert\Max(100)]
                public int $score,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_max_passes(): void
    {
        $dto = new class (50) {
            public function __construct(
                #[Assert\Max(100)]
                public int $score,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    public function test_range_fails(): void
    {
        $dto = new class (150) {
            public function __construct(
                #[Assert\Range(min: 1, max: 100)]
                public int $percent,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_range_passes(): void
    {
        $dto = new class (50) {
            public function __construct(
                #[Assert\Range(min: 1, max: 100)]
                public int $percent,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── Decimal ──────────────────────────────────────────────────

    public function test_decimal_fails(): void
    {
        $dto = new class ('12.345') {
            public function __construct(
                #[Assert\Decimal(scale: 2)]
                public string $price,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_decimal_passes(): void
    {
        $dto = new class ('12.34') {
            public function __construct(
                #[Assert\Decimal(scale: 2)]
                public string $price,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── Choice ───────────────────────────────────────────────────

    public function test_choice_fails(): void
    {
        $dto = new class ('yellow') {
            public function __construct(
                #[Assert\Choice(['red', 'green', 'blue'])]
                public string $color,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_choice_passes(): void
    {
        $dto = new class ('red') {
            public function __construct(
                #[Assert\Choice(['red', 'green', 'blue'])]
                public string $color,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── Count ────────────────────────────────────────────────────

    public function test_count_fails(): void
    {
        $dto = new class ([1]) {
            public function __construct(
                #[Assert\Count(min: 2, max: 5)]
                public array $items,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_count_passes(): void
    {
        $dto = new class ([1, 2, 3]) {
            public function __construct(
                #[Assert\Count(min: 2, max: 5)]
                public array $items,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── Date ─────────────────────────────────────────────────────

    public function test_date_fails(): void
    {
        $dto = new class ('not-a-date') {
            public function __construct(
                #[Assert\Date]
                public string $birthday,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_date_passes(): void
    {
        $dto = new class ('2024-03-15') {
            public function __construct(
                #[Assert\Date]
                public string $birthday,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── After / Before ───────────────────────────────────────────

    public function test_after_passes_when_date_is_later(): void
    {
        $dto = new class ('2024-01-01', '2024-06-01') {
            public function __construct(
                public string $startDate,
                #[Assert\After(otherField: 'startDate')]
                public string $endDate,
            ) {}
        };

        // endDate (2024-06-01) IS after startDate (2024-01-01) → should pass
        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    public function test_after_fails_when_same(): void
    {
        $dto = new class ('2024-06-01', '2024-06-01') {
            public function __construct(
                public string $startDate,
                #[Assert\After(otherField: 'startDate')]
                public string $endDate,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_before_fails(): void
    {
        $dto = new class ('2024-12-01', '2024-01-01') {
            public function __construct(
                #[Assert\Before(otherField: 'endDate')]
                public string $startDate,
                public string $endDate,
            ) {}
        };

        // startDate (2024-12-01) is NOT before endDate (2024-01-01) → fail
        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    // ── Url ──────────────────────────────────────────────────────

    public function test_url_fails(): void
    {
        $dto = new class ('not-a-url') {
            public function __construct(
                #[Assert\Url]
                public string $website,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_url_passes(): void
    {
        $dto = new class ('https://example.com') {
            public function __construct(
                #[Assert\Url]
                public string $website,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── Ip ───────────────────────────────────────────────────────

    public function test_ip_fails(): void
    {
        $dto = new class ('999.999.999.999') {
            public function __construct(
                #[Assert\Ip]
                public string $address,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_ip_passes(): void
    {
        $dto = new class ('192.168.1.1') {
            public function __construct(
                #[Assert\Ip]
                public string $address,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── UuidV4 ───────────────────────────────────────────────────

    public function test_uuid_fails(): void
    {
        $dto = new class ('not-a-uuid') {
            public function __construct(
                #[Assert\UuidV4]
                public string $id,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_uuid_passes(): void
    {
        $dto = new class ('550e8400-e29b-41d4-a716-446655440000') {
            public function __construct(
                #[Assert\UuidV4]
                public string $id,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── SameAs ───────────────────────────────────────────────────

    public function test_same_as_fails(): void
    {
        $dto = new class ('secret', 'different') {
            public function __construct(
                public string $password,
                #[Assert\SameAs(otherField: 'password')]
                public string $confirmPassword,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_same_as_passes(): void
    {
        $dto = new class ('secret', 'secret') {
            public function __construct(
                public string $password,
                #[Assert\SameAs(otherField: 'password')]
                public string $confirmPassword,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── Type ─────────────────────────────────────────────────────

    public function test_type_fails(): void
    {
        $dto = new class ('not-an-int') {
            public function __construct(
                #[Assert\Type(type: 'int')]
                public mixed $value,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_type_passes(): void
    {
        $dto = new class (42) {
            public function __construct(
                #[Assert\Type(type: 'int')]
                public mixed $value,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── Json ─────────────────────────────────────────────────────

    public function test_json_fails(): void
    {
        $dto = new class ('{invalid}') {
            public function __construct(
                #[Assert\Json]
                public string $payload,
            ) {}
        };

        $this->assertFalse($this->validator->validate($dto)->isValid);
    }

    public function test_json_passes(): void
    {
        $dto = new class ('{"key":"value"}') {
            public function __construct(
                #[Assert\Json]
                public string $payload,
            ) {}
        };

        $this->assertTrue($this->validator->validate($dto)->isValid);
    }

    // ── Multiple constraints ─────────────────────────────────────

    public function test_multiple_constraints_collect_all_errors(): void
    {
        $dto = new class ('', 'bad-email') {
            public function __construct(
                #[Assert\NotBlank]
                public string $name,
                #[Assert\Email]
                public string $email,
            ) {}
        };

        $result = $this->validator->validate($dto);
        $this->assertFalse($result->isValid);
        $this->assertSame(2, $result->errorCount);
    }

    public function test_valid_dto_returns_empty_result(): void
    {
        $dto = new class ('John', 'john@example.com') {
            public function __construct(
                #[Assert\NotBlank]
                public string $name,
                #[Assert\Email]
                public string $email,
            ) {}
        };

        $result = $this->validator->validate($dto);
        $this->assertTrue($result->isValid);
        $this->assertSame(0, $result->errorCount);
    }
}
