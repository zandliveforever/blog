<?php

namespace App\Actions\Comment;

use App\Models\Comment;

class DeleteCommentAction {
    private Comment $comment;

    public function __construct() {
        $this->comment = new Comment();
    }

    public function execute(int $id): bool {
        $pdoStatement = $this->comment->getDb()->prepare("DELETE FROM comments WHERE id = ?");
        return $pdoStatement->execute([$id]);
    }
} 