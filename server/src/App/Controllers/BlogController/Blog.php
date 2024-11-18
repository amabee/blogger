<?php

declare(strict_types=1);

namespace App\Controllers\BlogController;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Valitron\Validator;

use App\Repository\BlogRepos\BlogRepository;
use App\Repository\AuthRepository\AuthRepo;

class Blog
{
    public function __construct(
        private Validator $validator,
        private BlogRepository $blogRepository,
        private AuthRepo $authRepo
    ) {
        $this->validator->mapFieldsRules([
            'user_id' => ['required'],
            'title' => ['required', ['lengthMin', 8]],
            'content' => ['required', ['lengthMin', 8]],
            'blog_slug' => ['required'],
            'status' => ['required'],
        ]);
    }

    public function addPost(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        $this->validator = $this->validator->withData($data);

        if (!$this->validator->validate()) {

            $response->getBody()->write(json_encode(["success" => false, "message:" => $this->validator->errors()]));

            return $response->withStatus(422);
        }

        if ($this->authRepo->checkIfUserExistsByID($data['user_id']) === false) {
            $response->getBody()->write(json_encode(["success" => false, "message" => "This user is non-existent"]));
            return $response->withStatus(422);
        }

        if ($this->blogRepository->createBlog($data)) {
            $bodyData = json_encode(["success" => true, "message" => "Blog post created!"]);
        } else {
            $bodyData = json_encode(["success" => false, "message" => "Something went wrong"]);
        }


        $response->getBody()->write($bodyData);
        return $response->withStatus(201);
    }

    public function updatePost(Request $request, Response $response, string $post_id)
    {
        $data = $request->getParsedBody();

        $this->validator = $this->validator->withData($data);

        if (!$this->validator->validate()) {
            $response->getBody()->write(json_encode(["success" => false, "message:" => $this->validator->errors()]));
            return $response->withStatus(422);
        }

        if ($this->authRepo->checkIfUserExistsByID($data['user_id']) === false) {
            $response->getBody()->write(json_encode(["success" => false, "message" => "This user is non-existent"]));
            return $response->withStatus(422);
        }

        if ($this->blogRepository->updateBlog((int) $post_id, $data)) {
            $bodyData = json_encode(["success" => true, "message" => "Blog post updated!"]);
        } else {
            $bodyData = json_encode(["success" => false, "message" => "Something went wrong"]);
        }


        $response->getBody()->write($bodyData);
        return $response->withStatus(201);
    }
}
?>