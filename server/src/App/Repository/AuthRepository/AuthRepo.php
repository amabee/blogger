<?php

declare(strict_types=1);

namespace App\Repository\AuthRepository;

use App\Database;
use PDO;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthRepo
{
    private ?PDO $conn;

    public function __construct(private Database $db)
    {
        $this->conn = $this->db->getConnection();
    }


    public function signup(array $data)
    {
        $password_hash = hash('sha256', $data['password']);


        $sql = "INSERT INTO `users`(`first_name`, `last_name`, `username`, `email`, `password`, `date_of_birth`, `phone_number`, `gender`, `created_at`)
             VALUES (:firstname, :lastname, :username, :email, :password,:dob, :phone_num, :gender, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":firstname", $data['firstname'], PDO::PARAM_STR);
        $stmt->bindParam(":lastname", $data['lastname'], PDO::PARAM_STR);
        $stmt->bindParam(":username", $data['username'], PDO::PARAM_STR);
        $stmt->bindParam(":email", $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(":password", $password_hash, PDO::PARAM_STR);
        $stmt->bindParam(":dob", $data['dob']);
        $stmt->bindParam(":phone_num", $data['phone_num'], PDO::PARAM_STR);
        $stmt->bindParam(":gender", $data['gender'], PDO::PARAM_STR);

        $stmt->execute();
    }

    public function checkIfUserExists(string $value): array|bool
    {
        $sql = "SELECT * FROM users WHERE `email` = :value OR `username` = :value";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":value", $value, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch() !== false;
    }

    public function checkIfPhoneExists(string $value): array|bool
    {
        $sql = "SELECT * FROM users WHERE `phone_number` = :value";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":value", $value, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch() !== false;
    }

    public function checkIfUserExistsByID(string $value): array|bool
    {
        $sql = "SELECT * FROM users WHERE user_id = :value";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":value", $value);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }

    }

    public function login(array $data)
    {
        $password_hash = hash('sha256', $data['password']);
        $sql = "SELECT `user_id`, `first_name`, `last_name`, `username`, `email`, `date_of_birth`, `phone_number`, `gender`, `profile_picture`, `bio` 
            FROM `users` 
            WHERE (username = :user OR email = :user) AND password = :password";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":user", $data['user'], PDO::PARAM_STR);
        $stmt->bindParam(":password", $password_hash, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // JWT payload
            $payload = [
                "iss" => "localhost",
                "aud" => "localhost",
                "iat" => time(),
                "exp" => time() + (60 * 60 * 2),
                "data" => [
                    "user_id" => $user['user_id'],
                    "firstname" => $user['first_name'],
                    "lastname" => $user['last_name'],
                    "username" => $user['username'],
                    "email" => $user['email'],
                    "profile_pic" => $user['profile_picture'],
                ]
            ];

            // Generate the JWT token
            $secretKey = "your-secret-key";
            $jwt = JWT::encode($payload, $secretKey, 'HS256');

            // Additional session details
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $createdAt = date('Y-m-d H:i:s');

            // Check for an existing session for this user
            $existingSessionSQL = "SELECT 1 FROM `sessions` WHERE `user_id` = :user_id AND `ip_addr` = :ip_addr LIMIT 1";
            $existingSessionStmt = $this->conn->prepare($existingSessionSQL);
            $existingSessionStmt->bindParam(":user_id", $user['user_id'], PDO::PARAM_INT);
            $existingSessionStmt->bindParam(":ip_addr", $ipAddress, PDO::PARAM_STR);
            $existingSessionStmt->execute();

            if ($existingSessionStmt->rowCount() == 0) {
                // Insert session into database
                $insertSessionSQL = "INSERT INTO `sessions` (`session_key`, `user_agent`, `created_at`, `ip_addr`, `user_id`) 
                                    VALUES (:session_key, :user_agent, :created_at, :ip_addr, :user_id)";
                $sessionStmt = $this->conn->prepare($insertSessionSQL);
                $sessionStmt->bindParam(":session_key", $jwt, PDO::PARAM_STR);
                $sessionStmt->bindParam(":user_agent", $userAgent, PDO::PARAM_STR);
                $sessionStmt->bindParam(":created_at", $createdAt, PDO::PARAM_STR);
                $sessionStmt->bindParam(":ip_addr", $ipAddress, PDO::PARAM_STR);
                $sessionStmt->bindParam(":user_id", $user['user_id'], PDO::PARAM_INT);
                $sessionStmt->execute();
            }

            setcookie("session", $jwt, [
                'expires' => time() + (60 * 60 * 2),
                'path' => '/',
                'domain' => '',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

            return [
                "success" => true,
                "message" => "Login successful",
                "token" => $jwt
            ];
        } else {
            return [
                "success" => false,
                "message" => "Invalid username or password"
            ];
        }
    }



    public function checkIfValidSession(string $session_key, int $user_id)
    {
        $sql = "SELECT `session_key` FROM `sessions`
                WHERE session_key = :session_key AND user_id = :user_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":session_key", $session_key, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}

?>