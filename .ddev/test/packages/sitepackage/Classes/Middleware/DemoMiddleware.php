<?php

declare(strict_types=1);

namespace Test\Sitepackage\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DemoMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routingParameter = $request->getAttribute('routing');
        dump($routingParameter);
        return $handler->handle($request);
    }
}
