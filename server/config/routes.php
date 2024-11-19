<?php
declare(strict_types=1);
//WTF
use Slim\Routing\RouteCollectorProxy;
use App\Middleware\AddJsonResponseHeader;
use App\Controllers\AuthController\Signup;
use App\Controllers\AuthController\Login;
use App\Controllers\BlogController\BlogIndex;
use App\Controllers\BlogController\Blog;

use App\Middleware\RequireSessionKey;

$app->group("/auth", function (RouteCollectorProxy $authRoute) {
    $authRoute->post('/signup', [Signup::class, 'signup']);
    $authRoute->post('/login', [Login::class, 'login']);
})->add(AddJsonResponseHeader::class);


$app->group("/blog", function (RouteCollectorProxy $blogRoute) {
    $blogRoute->get("/", BlogIndex::class);
    $blogRoute->post('/', [Blog::class, "addPost"]);

    $blogRoute->group("", function (RouteCollectorProxy $blogRoute) {
        $blogRoute->get("/{post_id:[0-9]+}", [Blog::class, "readPostByID"]);
        $blogRoute->post("/{post_id:[0-9]+}", [Blog::class, "updatePost"]); //FOR UPDATING
        $blogRoute->delete('/{post_id:[0-9]+}', [Blog::class, "removePost"]);
    });

})->add(AddJsonResponseHeader::class)->add(RequireSessionKey::class);

?>