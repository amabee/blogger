<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;


class AddJsonResponseHeader{
    public function __invoke(Request $request, Handler $handler) : Response{
        $response = $handler->handle($request);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>