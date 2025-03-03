<?php

namespace App\Actions\Comment;

use App\Models\Comment;
use PDO;

class GetCommentsCountAction {
    private Comment $comment;

    public function __construct() {
        $this->comment = new Comment();
    }

    public function execute(): array {
        $stmt = $this->comment->getDb()->query("
            SELECT u.name, COUNT(c.id) as comment_count
            FROM users u
            LEFT JOIN comments c ON u.id = c.user_id
            GROUP BY u.id, u.name
            ORDER BY comment_count DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 