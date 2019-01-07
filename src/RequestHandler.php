<?php

declare(strict_types=1);

namespace Bmatovu\Http\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandler implements RequestHandlerInterface
{
    /**
     * @var MiddlewareInterface[] unresolved middleware stack
     */
    private $middlewares = [];

    /**
     * Default request handler.
     *
     * @var RequestHandlerInterface
     */
    private $defaultHandler;

    /**s
     * Create RequestHandler.
     * @param RequestHandlerInterface $defaultHandler
     */
    public function __construct(RequestHandlerInterface $defaultHandler)
    {
        $this->defaultHandler = $defaultHandler;
    }

    /**
     * Pop middleware from middlewares stack.
     *
     * @return MiddlewareInterface|null
     */
    public function popMiddleware()
    {
        return array_shift($this->middlewares);
    }

    /**
     * Add/Push middleware to stack.
     *
     * @param callable|MiddlewareInterface $middleware
     */
    public function push($middleware)
    {
        array_push($this->middlewares, $middleware);
    }

    /**
     * Handles a request and produces a response.
     *
     * Dispatches middleware from stack, LIFO
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (0 === \count($this->middlewares)) {
            return $this->defaultHandler->handle($request);
        }

        $middleware = $this->popMiddleware();

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }

        if (\is_callable($middleware)) {
            return $middleware($request, $this->defaultHandler);
        }
    }
}
