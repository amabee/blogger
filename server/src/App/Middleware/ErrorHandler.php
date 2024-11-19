<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\ResponseFactory as ResponseFactory;
use Slim\Exception\HttpException;

use Monolog\Logger as MonoLogger;
use Monolog\Level as MonoLevels;
use Monolog\Handler\StreamHandler as MonoStreamHandler;

use Throwable;

class ErrorHandler
{

    public function __invoke(
        Request $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): Response {
        $responseFactory = new ResponseFactory();
        $response = $responseFactory->createResponse();
        $statusCode = $exception instanceof HttpException ? $exception->getCode() : 500;

        $error = [
            'status' => "error",
            'message' => $exception->getMessage(),
            'code' => $statusCode,
        ];

        $error['request'] = [
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri()
        ];

        if ($displayErrorDetails) {
            $error['details'] = [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ];
        }

        if ($logErrors) {
            $logger = new MonoLogger('error_log');

            $logger->pushHandler(new MonoStreamHandler(__DIR__ . '../../logs/error_log.log', MonoLogger::ERROR));

            $logger->error($exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ]);
        }

        if ($logErrorDetails) {
            $logger = new MonoLogger('error_detail');
            $logger->pushHandler(new MonoStreamHandler(__DIR__ . '../../logs/error_details.log', MonoLogger::DEBUG));

            $logger->error('Detailed Error', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'code' => $exception->getCode(),
                'trace' => $exception->getTrace(),
                'request' => isset($request) ? [
                    'method' => $request->getMethod(),
                    'uri' => (string) $request->getUri(),
                    'headers' => $request->getHeaders(),
                ] : null
            ]);
        }




        $response->getBody()->write(json_encode($error));
        return $response->withStatus($statusCode);
    }
}

?>