<?php

namespace App\Actions\Comment;

use App\Models\Comment;
use PDO;

class GetAllCommentsAction {
    private Comment $comment;

    public function __construct() {
        $this->comment = new Comment();
    }

    public function execute(): array {
        $stmt = $this->comment->getDb()->query("
            SELECT c.*, u.name as author_name, p.title as post_title
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            LEFT JOIN posts p ON c.post_id = p.id
        ");
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($comment) => $this->comment->withRelations($comment), $comments);
    }
} 