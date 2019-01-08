<?php

require __DIR__ . '/../vendor/autoload.php';

use function Http\Response\send;

use Bmatovu\Http\Server\RequestHandler;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

// Middlewares

class Router implements RequestHandlerInterface
{
    /**
     * Handle the request and return a response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $response = new Response();

        $uri_path = $request->getUri()->getPath();

        switch ($uri_path) {
            case "/":
                echo "@index<br/>";
                $response->getBody()->write("<br/>Index<br/>");
                break;
            case "/about":
                $response->getBody()->write("<br/>About<br/>");
                break;
            default:
                $response->getBody()->write("<br/>Not Found<br/>");
                $response = $response->withStatus(404);
                break;
        }

        return $response;
    }
}

class AppMiddleware1 implements MiddlewareInterface
{
    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        echo "@app-mw-1 :: before<br/>";
        $response = $handler->handle($request);
        echo "@app-mw-1 :: after<br/>";
        $response->getBody()->write("<br/>App MW #1<br/>");
        return $response;
    }
}

class AppMiddleware2 implements MiddlewareInterface
{
    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        echo "@app-mw-2 :: before<br/>";
        $response = $handler->handle($request);
        echo "@app-mw-2 :: after<br/>";
        $response->getBody()->write("<br/>App MW #2<br/>");
        return $response;
    }
}

// closure middleware must implements Psr\Http\Server\MiddlewareInterface
$app_mw3 = function(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface {
    echo "@app-mw-3 :: before<br/>";
    $response = $handler->handle($request);
    echo "@app-mw-3 :: after<br/>";
    $response->getBody()->write("<br/>App MW #3<br/>");
    return $response;
};

$handler = new RequestHandler(new Router());
$handler->push(new AppMiddleware1());
$handler->push(new AppMiddleware2());
$handler->push($app_mw3);

// Http Messages

$request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

// Run request handler

$response = $handler->handle($request);

echo $response->getBody();
