<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation;

use MonkeysLegion\Validation\Contracts\ValidatorInterface;

use Psr\Http\Message\ServerRequestInterface;

use ReflectionClass;
use RuntimeException;

/**
 * Binds PSR-7 request data to a typed DTO and validates it.
 */
final class DtoBinder
{
    // ── Constructor ───────────────────────────────────────────────

    public function __construct(
        private readonly ValidatorInterface $validator,
    ) {}

    // ── Public API ────────────────────────────────────────────────

    /**
     * Bind request payload to a DTO and validate.
     *
     * @template T of object
     *
     * @param class-string<T>        $dtoClass
     * @param ServerRequestInterface $request
     *
     * @return array{dto: T, result: ValidationResult}
     *
     * @throws RuntimeException      If DTO has no constructor
     * @throws \JsonException        On invalid JSON body
     * @throws \ReflectionException  On reflection failure
     */
    public function bind(string $dtoClass, ServerRequestInterface $request): array
    {
        $data = $this->extractData($request);
        $dto = $this->hydrateDto($dtoClass, $data);
        $result = $this->validator->validate($dto);

        return ['dto' => $dto, 'result' => $result];
    }

    // ── Private methods ──────────────────────────────────────────

    /**
     * Extract data from the request (JSON body + query params).
     *
     * @return array<string, mixed>
     */
    private function extractData(ServerRequestInterface $request): array
    {
        $data = [];

        // Try parsed body first (form data, pre-parsed JSON)
        $parsed = $request->getParsedBody();

        if (is_array($parsed)) {
            $data = $parsed;
        } else {
            // Fall back to raw JSON body
            $raw = (string) $request->getBody();

            if ($raw !== '') {
                $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

                if (is_array($decoded)) {
                    $data = $decoded;
                }
            }
        }

        // Query params merge (query wins for GET-style overrides)
        return array_merge($data, $request->getQueryParams());
    }

    /**
     * Hydrate a DTO from data array.
     *
     * @template T of object
     *
     * @param class-string<T>       $dtoClass
     * @param array<string, mixed>  $data
     *
     * @return T
     */
    private function hydrateDto(string $dtoClass, array $data): object
    {
        $ref = new ReflectionClass($dtoClass);
        $ctor = $ref->getConstructor();

        if ($ctor === null) {
            throw new RuntimeException("{$dtoClass} has no constructor.");
        }

        $args = [];

        foreach ($ctor->getParameters() as $param) {
            $name = $param->getName();

            if (array_key_exists($name, $data)) {
                $args[] = $data[$name];
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                $args[] = null;
            }
        }

        /** @var T */
        return $ref->newInstanceArgs($args);
    }
}