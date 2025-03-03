<?php

namespace App\Actions\Post;

use App\Models\Post;
use PDO;

class UpdatePostAction {
    private Post $post;

    public function __construct() {
        $this->post = new Post();
    }

    public function execute(int $id, array $data): bool {
        $checkStmt = $this->post->getDb()->prepare("SELECT 1 FROM posts WHERE id = ?");
        $checkStmt->execute([$id]);
        if (!$checkStmt->fetch()) {
            throw new \Exception('Post not found', 404);
        }

        $fields = array_intersect_key($data, array_flip($this->post->getFillable()));
        if (empty($fields)) {
            return false;
        }

        $setClause = implode(' = ?, ', array_keys($fields)) . ' = ?';
        $fields['updated_at'] = date('Y-m-d H:i:s');
        $setClause .= ', updated_at = ?';

        $stmt = $this->post->getDb()->prepare("
            UPDATE {$this->post->getTable()}
            SET $setClause
            WHERE id = ?
        ");

        $values = array_values($fields);
        $values[] = $id;

        return $stmt->execute($values);
    }
} 