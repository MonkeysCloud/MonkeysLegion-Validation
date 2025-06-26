<?php
namespace MonkeysLegion\Validation;

use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use RuntimeException;
use MonkeysLegion\Validation\ValidatorInterface;
use MonkeysLegion\Validation\ValidationError;

final class DtoBinder
{
    public function __construct(private ValidatorInterface $validator) {}

    /**
     * Bind request payload & query params to a typed DTO, then validate.
     *
     * @template T of object
     * @param class-string<T> $dtoClass
     * @return array{dto:T, errors:ValidationError[]}
     * @throws \ReflectionException|\JsonException
     */
    public function bind(string $dtoClass, ServerRequestInterface $request): array
    {
        // Decode JSON body (assumes content-type negotation already happened)
        $data = [];
        $raw  = (string) $request->getBody();
        if ($raw !== '') {
            $data = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);
        }

        // Merge query string (query wins)
        $data = array_merge($data, $request->getQueryParams());

        $ref  = new ReflectionClass($dtoClass);
        $ctor = $ref->getConstructor();

        if (!$ctor) {
            throw new RuntimeException("$dtoClass has no constructor");
        }

        // Build args in declared order (promoted props)
        $args = [];
        foreach ($ctor->getParameters() as $param) {
            $name = $param->getName();
            $args[] = $data[$name] ?? $param->getDefaultValue();
        }

        /** @var T $dto */
        $dto    = $ref->newInstanceArgs($args);
        $errors = $this->validator->validate($dto);

        return compact('dto', 'errors');
    }
}