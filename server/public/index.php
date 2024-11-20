<?php

declare(strict_types=1);

header("Access-Control-Allow-Origin: *");

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Slim\Handlers\Strategies\RequestResponseArgs;
use App\Middleware\ErrorHandler;

define('APP_ROOT', dirname(__DIR__));
require APP_ROOT . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$builder = new ContainerBuilder();
$container = $builder->addDefinitions(APP_ROOT . '/config/definitions.php')->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$collector = $app->getRouteCollector();
$collector->setDefaultInvocationStrategy(new RequestResponseArgs);

$app->addBodyParsingMiddleware();

$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});


$app->add(function ($request, $handler) {
    $response = $handler->handle($request);

    $response = $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Allow-Credentials', 'true');

    if ($request->getMethod() === 'OPTIONS') {
        return $response
            ->withStatus(200);
    }

    return $response;
});


$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler(new ErrorHandler());

require APP_ROOT . '/config/routes.php';

$app->run();
