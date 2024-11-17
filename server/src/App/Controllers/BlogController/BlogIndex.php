<?php
declare(strict_types=1);

namespace App\Controllers\BlogController;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Repository\BlogRepos\BlogRepository;

class BlogIndex
{
    public function __construct(private BlogRepository $blogRepository)
    {

    }

    public function __invoke(Request $request, Response $response)
    {
        $data = $this->blogRepository->getAllBlog();

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;
    }
}

?>