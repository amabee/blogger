<?php
declare(strict_types=1);

namespace App\Controllers\AuthController;


use App\Repository\AuthRepository\AuthRepo;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Valitron\Validator;

class CheckUsername
{


    public function __construct(private Validator $validator, private AuthRepo $authRepo)
    {

        $this->validator->mapFieldsRules([
            'user' => ['required'],
        ]);
    }


    public function checkUsernameAvailability(Request $request, Response $response)
    {

        $data = $request->getParsedBody();

        $this->validator = $this->validator->withData($data);

        if (!$this->validator->validate()) {
            $response->getBody()->write(json_encode(["success" => false, "message:" => $this->validator->errors()]));

            return $response->withStatus(422);
        }

        if ($this->authRepo->checkIfUserExists($data['user']) !== false) {
            $response->getBody()->write(json_encode(["success" => true, "data" => $this->authRepo->checkIfUserExists($data['user'])]));
        } else {
            $response->getBody()->write(json_encode(["success" => true, "data" => $this->authRepo->checkIfUserExists($data['user'])]));
        }

        return $response->withStatus(200);
    }
}

?>