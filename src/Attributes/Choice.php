<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Attributes;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;

use Attribute;

/**
 * Value must be one of the allowed choices.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class Choice implements ConstraintInterface
{
    /** @var list<mixed> */
    public array $choices;

    /**
     * @param list<mixed> $choices
     */
    public function __construct(
        array $choices,
        public string $message = 'Value is not a valid choice.',
    ) {
        $this->choices = $choices;
    }

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!in_array($value, $this->choices, true)) {
            return new ValidationError($field, $this->message);
        }

        return null;
    }
}