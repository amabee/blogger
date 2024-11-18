<?php

declare(strict_types=1);

namespace App\Controllers\AuthController;

use App\Repository\AuthRepository\AuthRepo;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Valitron\Validator;


class Signup
{

    public function __construct(private Validator $validator, private AuthRepo $authRepo)
    {
        $this->validator->mapFieldsRules([
            'firstname' => ['required'],
            'lastname' => ['required'],
            'username' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', ['lengthMin', 8]],
            'dob' => ['required'],
            'phone_num' => ['required'],
            'gender' => ['required'],
        ]);
    }

    public function signup(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        $this->validator = $this->validator->withData($data);

        if (!$this->validator->validate()) {

            $response->getBody()->write(json_encode(["success" => false, "message:" => $this->validator->errors()]));

            return $response->withStatus(422);
        }

        if (
            $this->authRepo->checkIfUserExists($data['username'])
            ||
            $this->authRepo->checkIfUserExists($data['email'])
        ) {
            $response->getBody()->write(json_encode(["success" => false, "message" => "This email or username is already taken"]));
            return $response->withStatus(422);
        }

        $this->authRepo->signup($data);

        $bodyData = json_encode(["success" => true, "message" => "Account was successfully created"]);

        $response->getBody()->write($bodyData);
        return $response->withStatus(201);
    }
}

?>