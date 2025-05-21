<?php
declare(strict_types=1);

namespace MonkeysLegion\Validation;

use DateTimeImmutable;
use MonkeysLegion\Validation\Attributes as Assert;
use ReflectionClass;
use ReflectionProperty;

/**
 * Default validator that inspects attribute-based constraints
 * on public DTO properties and produces ValidationError objects.
 */
final class AttributeValidator implements ValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate(object $dto): array
    {
        $errors = [];
        $ref    = new ReflectionClass($dto);

        foreach ($ref->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
            $value = $prop->getValue($dto);

            foreach ($prop->getAttributes() as $attr) {
                $instance = $attr->newInstance();

                /* ─────────────────────────────── BASIC STRINGS ────────────────────────────── */

                if ($instance instanceof Assert\NotBlank &&
                    ($value === null || $value === '')) {
                    $errors[] = new ValidationError($prop->getName(), $instance->message);
                }

                if ($instance instanceof Assert\Email &&
                    !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = new ValidationError($prop->getName(), $instance->message);
                }

                if ($instance instanceof Assert\Length) {
                    $len = mb_strlen((string) $value);
                    if ($len < $instance->min || $len > $instance->max) {
                        $errors[] = new ValidationError($prop->getName(), $instance->message);
                    }
                }

                if ($instance instanceof Assert\Pattern &&
                    !preg_match($instance->regex, (string) $value)) {
                    $errors[] = new ValidationError($prop->getName(), $instance->message);
                }

                if ($instance instanceof Assert\Alpha &&
                    !preg_match('/^[a-z]+$/i', (string) $value)) {
                    $errors[] = new ValidationError($prop->getName(), $instance->message);
                }

                if ($instance instanceof Assert\Alnum &&
                    !preg_match('/^[a-z0-9]+$/i', (string) $value)) {
                    $errors[] = new ValidationError($prop->getName(), $instance->message);
                }

                if ($instance instanceof Assert\Numeric &&
                    !ctype_digit((string) $value)) {
                    $errors[] = new ValidationError($prop->getName(), $instance->message);
                }

                if ($instance instanceof Assert\Choice &&
                    !in_array($value, $instance->choices, true)) {
                    $errors[] = new ValidationError($prop->getName(), $instance->message);
                }

                /* ─────────────────────────────── NUMBERS ──────────────────────────────────── */

                if ($instance instanceof Assert\Min &&
                    (float) $value < (float) $instance->value) {
                    $errors[] = new ValidationError($prop->getName(), $instance->message);
                }

                if ($instance instanceof Assert\Max &&
                    (float) $value > (float) $instance->value) {
                    $errors[] = new ValidationError($prop->getName(), $instance->message);
                }

                if ($instance instanceof Assert\Range) {
                    $n = (float) $value;
                    if ($n < (float) $instance->min || $n > (float) $instance->max) {
                        $errors[] = new ValidationError($prop->getName(), $instance->message);
                    }
                }

                if ($instance instanceof Assert\Decimal &&
                    !preg_match('/^-?\d+(?:\.\d{0,' . $instance->scale . '})?$/', (string) $value)) {
                    $errors[] = new ValidationError($prop->getName(), $instance->message);
                }

                /* ─────────────────────────────── COLLECTIONS ──────────────────────────────── */

                if ($instance instanceof Assert\Count && is_iterable($value)) {
                    $size = is_countable($value) ? count($value) : iterator_count($value);
                    if ($size < $instance->min || $size > $instance->max) {
                        $errors[] = new ValidationError($prop->getName(), $instance->message);
                    }
                }

                /* ─────────────────────────────── DATES ───────────────────────────────────── */

                if ($instance instanceof Assert\Date &&
                    !DateTimeImmutable::createFromFormat('Y-m-d', (string) $value)) {
                    $errors[] = new ValidationError($prop->getName(), $instance->message);
                }

                if ($instance instanceof Assert\After) {
                    $other = $ref->getProperty($instance->otherField)->getValue($dto);
                    if (new DateTimeImmutable((string) $value) <= new DateTimeImmutable((string) $other)) {
                        $errors[] = new ValidationError(
                            $prop->getName(),
                            str_replace('%other%', $instance->otherField, $instance->message)
                        );
                    }
                }

                if ($instance instanceof Assert\Before) {
                    $other = $ref->getProperty($instance->otherField)->getValue($dto);
                    if (new DateTimeImmutable((string) $value) >= new DateTimeImmutable((string) $other)) {
                        $errors[] = new ValidationError(
                            $prop->getName(),
                            str_replace('%other%', $instance->otherField, $instance->message)
                        );
                    }
                }

                /* ─────────────────────────────── NETWORKING ──────────────────────────────── */

                if ($instance instanceof Assert\Url &&
                    !filter_var($value, FILTER_VALIDATE_URL)) {
                    $errors[] = new ValidationError($prop->getName(), $instance->message);
                }

                if ($instance instanceof Assert\Ip) {
                    $flags = $instance->allowV6
                        ? FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6
                        : FILTER_FLAG_IPV4;
                    if (!filter_var($value, FILTER_VALIDATE_IP, $flags)) {
                        $errors[] = new ValidationError($prop->getName(), $instance->message);
                    }
                }

                /* ─────────────────────────────── IDENTIFIERS ─────────────────────────────── */

                if ($instance instanceof Assert\UuidV4 &&
                    !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', (string) $value)) {
                    $errors[] = new ValidationError($prop->getName(), $instance->message);
                }

                /* ─────────────────────────────── CROSS-FIELD ─────────────────────────────── */

                if ($instance instanceof Assert\SameAs) {
                    $other = $ref->getProperty($instance->otherField)->getValue($dto);
                    if ($value !== $other) {
                        $errors[] = new ValidationError(
                            $prop->getName(),
                            str_replace('%other%', $instance->otherField, $instance->message)
                        );
                    }
                }
            }
        }

        return $errors;
    }
}