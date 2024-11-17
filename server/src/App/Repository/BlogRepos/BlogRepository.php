<?php
//QUERY OPERATIONS HERE


declare(strict_types=1);

namespace App\Repository\BlogRepos;

use App\Database;
use PDO;

class BlogRepository
{

    private ?PDO $conn;

    public function __construct(private Database $db)
    {
        $this->conn = $this->db->getConnection();
    }

    public function getAllBlog()
    {
        $sql = "SELECT * FROM posts";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //TO BE CONTINUED
}

?>