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

    public function getBlogByID(int $post_id)
    {
        $sql = "SELECT * FROM posts WHERE post_id = :post_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":post_id", $post_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            return ["success" => false, "message" => "Post not found"];
        }

        return ["success" => true, "data" => $result];


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

                for ($i = 0; $i < $total_files; $i++) {
                    if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                        $image_name = $_FILES['images']['name'][$i];
                        $temp_name = $_FILES['images']['tmp_name'][$i];
                        $upload_dir = 'media/';
                        $target_file = $upload_dir . basename($image_name);

                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }

                        if (move_uploaded_file($temp_name, $target_file)) {
                            try {
                                $image_query = "INSERT INTO images (post_id, media_name) VALUES (:post_id, :image_name)";
                                $image_stmt = $this->conn->prepare($image_query);
                                $image_stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
                                $image_stmt->bindParam(':image_name', $image_name, PDO::PARAM_STR);
                                $image_stmt->execute();
                            } catch (PDOException $e) {
                                error_log("Failed to insert image record: " . $e->getMessage());
                                return false;
                            }
                        } else {
                            error_log("Failed to move uploaded file: $image_name");
                            return false;
                        }
                    } else {
                        $error_code = $_FILES['images']['error'][$i];
                        error_log("File upload error (code $error_code) for file $i");
                        return false;
                    }
                }
            }

            if ($post_id !== null || $post_id !== 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error creating blog post: " . $e->getMessage();
        }
    }

    public function updateBlog(int $post_id, array $data)
    {
        $uploadDir = 'media/';

        try {
            $data['user_id'] = filter_var($data['user_id'], FILTER_VALIDATE_INT);
            $data['title'] = htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8');
            $data['content'] = htmlspecialchars($data['content'], ENT_QUOTES, 'UTF-8');
            $data['blog_slug'] = htmlspecialchars($data['blog_slug'], ENT_QUOTES, 'UTF-8');
            $data['status'] = htmlspecialchars($data['status'], ENT_QUOTES, 'UTF-8');

            $sql = "UPDATE `posts` 
                    SET `user_id` = :user_id, `title` = :title, `content` = :content, 
                        `blog_slug` = :blog_slug, `status` = :status, `updated_at` = NOW() 
                    WHERE `post_id` = :post_id";
            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':title', $data['title'], PDO::PARAM_STR);
            $stmt->bindParam(':content', $data['content'], PDO::PARAM_STR);
            $stmt->bindParam(':blog_slug', $data['blog_slug'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);

            $stmt->execute();

            if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
                $total_files = count($_FILES['images']['name']);

                for ($i = 0; $i < $total_files; $i++) {
                    if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                        $image_name = $_FILES['images']['name'][$i];
                        $temp_name = $_FILES['images']['tmp_name'][$i];
                        $target_file = $uploadDir . basename($image_name);

                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        if (move_uploaded_file($temp_name, $target_file)) {
                            try {
                                $image_query = "INSERT INTO images (post_id, media_name) VALUES (:post_id, :image_name)";
                                $image_stmt = $this->conn->prepare($image_query);
                                $image_stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
                                $image_stmt->bindParam(':image_name', $image_name, PDO::PARAM_STR);
                                $image_stmt->execute();
                            } catch (PDOException $e) {
                                error_log("Failed to insert image record: " . $e->getMessage());
                                return false;
                            }
                        } else {
                            error_log("Failed to move uploaded file: $image_name");
                            return false;
                        }
                    } else {
                        $error_code = $_FILES['images']['error'][$i];
                        error_log("File upload error (code $error_code) for file $i");
                        return false;
                    }
                }
            }

            return true;

        } catch (PDOException $e) {
            return "Error updating blog post: " . $e->getMessage();
        }
    }

    public function removeBlog(int $post_id)
    {
        try {

            $selectPostIDSql = "SELECT COUNT(`post_id`) AS post_count from `posts` WHERE post_id = :post_id";
            $selectPostIDStmt = $this->conn->prepare($selectPostIDSql);
            $selectPostIDStmt->bindParam(":post_id", $post_id, PDO::PARAM_INT);
            $selectPostIDStmt->execute();
            $result = $selectPostIDStmt->fetch(PDO::FETCH_ASSOC);
            //WTF NOT ROW COUNT
            if ($result['post_count'] <= 0) {
                return ['success' => false, "message" => "Post not found"];
            } else {
                $sql = "DELETE FROM `posts` WHERE `post_id` = :post_id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":post_id", $post_id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    return [
                        'success' => true,
                        'message' => 'Post deleted successfully'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Failed to delete post'
                    ];
                }
            }

        } catch (PDOException $ex) {
            return [
                'success' => false,
                'message' => "Exception Handled: " . $ex->getMessage()
            ];
        }
    }


}

?>