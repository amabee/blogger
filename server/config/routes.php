<?php
declare(strict_types=1);
//WTF
use Slim\Routing\RouteCollectorProxy;
use App\Middleware\AddJsonResponseHeader;
use App\Controllers\AuthController\Signup;
use App\Controllers\AuthController\Login;
use App\Controllers\BlogController\BlogIndex;

$app->group("/auth", function (RouteCollectorProxy $authRoute) {
    $authRoute->post('/signup', [Signup::class, 'signup']);
    $authRoute->post('/login', [Login::class, 'login']);
})->add(AddJsonResponseHeader::class);


$app->group("/blog", function (RouteCollectorProxy $blogRoute) {
    $blogRoute->get("/", BlogIndex::class);
})->add(AddJsonResponseHeader::class);

?>