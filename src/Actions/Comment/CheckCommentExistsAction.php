<?php

namespace App\Actions\Comment;

use App\Models\Comment;
use PDO;

class CheckCommentExistsAction {
    private Comment $comment;

    public function __construct() {
        $this->comment = new Comment();
    }

    public function execute(int $id): bool {
        $stmt = $this->comment->getDb()->prepare("SELECT 1 FROM comments WHERE id = ?");
        $stmt->execute([$id]);
        return (bool)$stmt->fetch(PDO::FETCH_COLUMN);
    }
} 