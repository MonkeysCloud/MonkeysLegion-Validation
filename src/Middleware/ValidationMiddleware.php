<?php
namespace MonkeysLegion\Validation\Middleware;

use JsonException;
use MonkeysLegion\Validation\DtoBinder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;

final class ValidationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private DtoBinder $binder,
        /** Map `[routeName => Fully\Qualified\DtoClass]` */
        private array $dtoMap
    ) {}

    /**
     * @throws JsonException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeName = $request->getAttribute('route');  // Adapt if you store route differently
        $dtoClass  = $this->dtoMap[$routeName] ?? null;

        if ($dtoClass) {
            ['dto' => $dto, 'errors' => $errors] = $this->binder->bind($dtoClass, $request);

            if ($errors) {
                return new JsonResponse(
                    ['errors' => array_map(fn($e) => ['field' => $e->property, 'message' => $e->message], $errors)],
                    400
                );
            }
            // Pass validated DTO to the handler
            $request = $request->withAttribute('dto', $dto);
        }

        return $handler->handle($request);
    }
}