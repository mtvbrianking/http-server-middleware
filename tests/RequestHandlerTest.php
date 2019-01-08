<?php

namespace Bmatovu\Http\Server\Test;

use Bmatovu\Http\Server\RequestHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandlerTest extends TestCase
{
    private function makeHttpRequest(string $method = 'GET')
    {
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $request->method('getMethod')->willReturn($method);

        return $request;
    }

    private function makeHttpResponse()
    {
        return $this->getMockBuilder(ResponseInterface::class)->getMock();
    }

    private function makeDefaultRequestHandler()
    {
        return $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
    }

    public function testRequestHandlerResponse()
    {
        $handler = new RequestHandler($this->makeDefaultRequestHandler());
        $response = $handler->handle($this->makeHttpRequest());
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
