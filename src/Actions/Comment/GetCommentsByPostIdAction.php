<?php

namespace App\Actions\Comment;

use App\Models\Comment;
use PDO;

class GetCommentsByPostIdAction {
    private Comment $comment;

    public function __construct() {
        $this->comment = new Comment();
    }

    public function execute(int $postId): array {
        $stmt = $this->comment->getDb()->prepare("
            SELECT c.*, u.name as author_name
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.post_id = ?
        ");
        $stmt->execute([$postId]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($comment) => $this->comment->withRelations($comment), $comments);
    }
} 