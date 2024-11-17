<?php
declare(strict_types=1);

namespace App\Controllers\AuthController;


use App\Repository\AuthRepository\AuthRepo;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Valitron\Validator;

class Login
{


    public function __construct(private Validator $validator, private AuthRepo $authRepo)
    {

        $this->validator->mapFieldsRules([
            'user' => ['required'],
            'password' => ['required'],
        ]);
    }


    public function login(Request $request, Response $response)
    {

        $data = $request->getParsedBody();

        $this->validator = $this->validator->withData($data);

        if (!$this->validator->validate()) {
            $response->getBody()->write(json_encode(["success" => false, "message:" => $this->validator->errors()]));

            return $response->withStatus(422);
        }

        if ($this->authRepo->login($data) !== null) {
            $response->getBody()->write(json_encode(["success" => true, "data" => $this->authRepo->login($data)]));
        } else {
            $response->getBody()->write(json_encode(["success" => false, "message" => "Invalid Credentials"]));
        }

        return $response->withStatus(200);
    }
}

?>