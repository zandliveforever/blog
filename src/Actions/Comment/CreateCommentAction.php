<?php

namespace App\Actions\Comment;

use App\Models\Comment;
use PDO;

class CreateCommentAction {
    private Comment $comment;

    public function __construct() {
        $this->comment = new Comment();
    }

    public function execute(array $data): int {
        $stmt = $this->comment->getDb()->prepare("
            INSERT INTO comments (content, user_id, post_id, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        
        $stmt->execute([
            $data['content'],
            $data['user_id'],
            $data['post_id']
        ]);

        return (int) $this->comment->getDb()->lastInsertId();
    }
} 