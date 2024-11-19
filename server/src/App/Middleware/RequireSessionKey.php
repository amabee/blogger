<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Psr7\Factory\ResponseFactory;

use App\Repository\AuthRepository\AuthRepo;


class RequireSessionKey
{

    public function __construct(
        private ResponseFactory $responseFactory,
        private AuthRepo $authRepo
    ) {
    }

    public function __invoke(Request $request, RequestHandler $requestHandler): Response
    {
        $data = $request->getParsedBody();

        if (!$request->hasHeader('X-Session-Key')) {
            $response = $this->responseFactory->createResponse();
            $response->getBody()->write(json_encode(["success" => false, "message" => "Session key missing from request"]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $session_key = $request->getHeaderLine('X-Session-Key');

        $data = $request->getParsedBody();

        $user_key = $this->authRepo->checkIfValidSession($session_key, (int)$data['user_id']);

        if ($user_key === false) {
            $response = $this->responseFactory->createResponse();
            $response->getBody()->write(json_encode(["success" => false, "message" => "Invalid session key"]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $response = $requestHandler->handle($request);

        return $response;
    }

}
?>