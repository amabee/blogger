<?php
//QUERY OPERATIONS HERE


declare(strict_types=1);

namespace App\Repository\BlogRepos;

use App\Database;
use PDO;
use PDOException;

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

    public function createBlog(array $data)
    {
        try {
            $data['user_id'] = filter_var($data['user_id'], FILTER_VALIDATE_INT);
            $data['title'] = htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8');
            $data['content'] = htmlspecialchars($data['content'], ENT_QUOTES, 'UTF-8');
            $data['blog_slug'] = htmlspecialchars($data['blog_slug'], ENT_QUOTES, 'UTF-8');
            $data['status'] = htmlspecialchars($data['status'], ENT_QUOTES, 'UTF-8');

            $sql = "INSERT INTO `posts`(`user_id`, `title`, `content`, `blog_slug`, `status`, `created_at`) 
                    VALUES (:user_id, :title, :content, :blog_slug, :status, NOW())";
            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':title', $data['title'], PDO::PARAM_STR);
            $stmt->bindParam(':content', $data['content'], PDO::PARAM_STR);
            $stmt->bindParam(':blog_slug', $data['blog_slug'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);

            $stmt->execute();
            $post_id = $this->conn->lastInsertId();

            if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
                $total_files = count($_FILES['images']['name']);
                $image_names = [];

                for ($i = 0; $i < $total_files; $i++) {
                    if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                        $image_name = $_FILES['images']['name'][$i];
                        $temp_name = $_FILES['images']['tmp_name'][$i];
                        $upload_dir = 'POST_IMAGES/';
                        $target_file = $upload_dir . basename($image_name);

                        if (move_uploaded_file($temp_name, $target_file)) {
                            $image_names[] = $image_name;

                            $image_query = "INSERT INTO images (post_id, image_url) VALUES (:post_id, :image_name)";
                            $image_stmt = $this->conn->prepare($image_query);
                            $image_stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
                            $image_stmt->bindParam(':image_name', $image_name, PDO::PARAM_STR);
                            $image_stmt->execute();
                        } else {
                            return "Failed to move uploaded file: $image_name";
                        }
                    } else {
                        return "File upload error: " . $_FILES['images']['error'][$i];
                    }
                }
            }
        } catch (PDOException $e) {
            return "Error creating blog post: " . $e->getMessage();
        }
    }


}

?>