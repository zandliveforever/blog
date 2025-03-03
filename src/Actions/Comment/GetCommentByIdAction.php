<?php

namespace App\Actions\Comment;

use App\Models\Comment;
use PDO;

class GetCommentByIdAction {
    private Comment $comment;

    public function __construct() {
        $this->comment = new Comment();
    }

    public function execute(int $id): array|false {
        $stmt = $this->comment->getDb()->prepare("
            SELECT c.*, u.name as author_name, p.title as post_title
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            LEFT JOIN posts p ON c.post_id = p.id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);
        return $comment ? $this->comment->withRelations($comment) : false;
    }
} 