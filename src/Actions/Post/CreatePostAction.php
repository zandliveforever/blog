<?php

namespace App\Actions\Post;

use App\Models\Post;
use PDO;
use PDOException;

class CreatePostAction {
    private Post $post;

    public function __construct() {
        $this->post = new Post();
    }

    public function execute(array $data): int {
        try {
            $fields = array_intersect_key($data, array_flip($this->post->getFillable()));
            
            $requiredFields = ['title', 'content', 'user_id'];
            $missingFields = array_diff($requiredFields, array_keys($fields));
            if (!empty($missingFields)) {
                throw new \Exception('Missing required fields: ' . implode(', ', $missingFields), 422);
            }

            $now = date('Y-m-d H:i:s');
            $fields['created_at'] = $now;
            $fields['updated_at'] = $now;

            $columns = implode(', ', array_keys($fields));
            $values = implode(', ', array_fill(0, count($fields), '?'));
            
            $stmt = $this->post->getDb()->prepare("
                INSERT INTO {$this->post->getTable()} ($columns)
                VALUES ($values)
            ");
            
            $stmt->execute(array_values($fields));
            return (int)$this->post->getDb()->lastInsertId();
        } catch (PDOException $e) {
            $message = 'Failed to create post';
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                throw new \Exception($message . ': Duplicate entry', 409);
            } else {
                throw new \Exception($message . ': Database error - ' . $e->getMessage(), 500);
            }
        }
    }
} 