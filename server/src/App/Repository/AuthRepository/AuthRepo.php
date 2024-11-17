<?php

declare(strict_types=1);

namespace App\Repository\AuthRepository;

use App\Database;
use PDO;


//WUT?
//WHY USE DEFUSE?
//WTF!
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

    public function login(array $data)
    {
        $password_hash = hash('sha256', $data['password']);
        $salt = password_hash($password_hash, PASSWORD_BCRYPT);

        $sql = "SELECT `user_id`, `first_name`, `last_name`, `username`, `email`, `date_of_birth`, `phone_number`, `gender`, `profile_picture`, `bio` FROM `users` 
                WHERE (username = :user OR email = :user) AND password = :password";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":user", $data['user'], PDO::PARAM_STR);
        $stmt->bindParam(":password", $password_hash, PDO::PARAM_STR);

        $stmt->execute();
        // $stmt->fetch(mode: PDO::FETCH_ASSOC);

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return null;
        }


    }

    //IMPLEMENT LATER

    // public function checkIfValidSession(array $data)
    // {
    //     $sql = "SELECT `session_key` FROM `sessions`
    //             WHERE session_key = :session_key AND user_id = :user_id";

    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->bindParam(":user_id", $data['user_id']);
    //     $stmt->bindParam(":session_key", $data['session_key']);
    //     $stmt->execute();

    //     if ($stmt->rowCount() > 0) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }
}

?>