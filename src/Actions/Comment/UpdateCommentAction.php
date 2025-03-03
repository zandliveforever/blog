<?php

namespace App\Actions\Comment;

use App\Models\Comment;
use PDO;

class UpdateCommentAction {
    private Comment $comment;

    public function __construct() {
        $this->comment = new Comment();
    }

    public function execute(int $id, array $data): bool {
        $checkStmt = $this->comment->getDb()->prepare("SELECT 1 FROM comments WHERE id = ?");
        $checkStmt->execute([$id]);
        if (!$checkStmt->fetch()) {
            throw new \Exception('Comment not found', 404);
        }

        $fields = array_intersect_key($data, array_flip($this->comment->getFillable()));
        if (empty($fields)) {
            return false;
        }

        $setClause = implode(' = ?, ', array_keys($fields)) . ' = ?';
        $fields['updated_at'] = date('Y-m-d H:i:s');
        $setClause .= ', updated_at = ?';

        $stmt = $this->comment->getDb()->prepare("
            UPDATE {$this->comment->getTable()}
            SET $setClause
            WHERE id = ?
        ");

        $values = array_values($fields);
        $values[] = $id;

        return $stmt->execute($values);
    }
} 