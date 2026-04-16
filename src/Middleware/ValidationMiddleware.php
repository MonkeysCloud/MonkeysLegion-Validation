<?php

declare(strict_types=1);

namespace MonkeysLegion\Validation\Middleware;

use MonkeysLegion\Validation\DtoBinder;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * PSR-15 middleware: binds DTO from request and validates.
 *
 * Returns 422 Unprocessable Entity with JSON error body on failure.
 * On success, attaches the validated DTO as a request attribute.
 */
final class ValidationMiddleware implements MiddlewareInterface
{
    // ── Constructor ───────────────────────────────────────────────

    /**
     * @param array<string, class-string> $dtoMap Route name → DTO class mapping
     */
    public function __construct(
        private readonly DtoBinder $binder,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly array $dtoMap = [],
    ) {}

    // ── MiddlewareInterface ──────────────────────────────────────

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeName = $request->getAttribute('route');

        if (!is_string($routeName)) {
            return $handler->handle($request);
        }

        $dtoClass = $this->dtoMap[$routeName] ?? null;

        if ($dtoClass === null) {
            return $handler->handle($request);
        }

        ['dto' => $dto, 'result' => $result] = $this->binder->bind($dtoClass, $request);

        if (!$result->isValid) {
            $body = json_encode(
                ['errors' => $result->toArray()],
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE,
            );

            $stream = $this->streamFactory->createStream($body);

            return $this->responseFactory
                ->createResponse(422, 'Unprocessable Entity')
                ->withHeader('Content-Type', 'application/json')
                ->withBody($stream);
        }

        // Attach validated DTO to the request
        $request = $request->withAttribute('dto', $dto);

        return $handler->handle($request);
    }
}
