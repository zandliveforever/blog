<?php

namespace App\Actions\Comment;

use App\Models\Comment;
use PDO;

class GetCommentsByUserIdAction {
    private Comment $comment;

    public function __construct() {
        $this->comment = new Comment();
    }

    public function execute(int $userId): array {
        $stmt = $this->comment->getDb()->prepare("
            SELECT c.*, p.title as post_title
            FROM comments c
            LEFT JOIN posts p ON c.post_id = p.id
            WHERE c.user_id = ?
        ");
        $stmt->execute([$userId]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($comment) => $this->comment->withRelations($comment), $comments);
    }
} 