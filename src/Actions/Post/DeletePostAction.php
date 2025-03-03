<?php

namespace App\Actions\Post;

use App\Models\Post;
use PDO;

class DeletePostAction {
    private Post $post;

    public function __construct() {
        $this->post = new Post();
    }

    public function execute(int $id): bool {
        $stmt = $this->post->getDb()->prepare("DELETE FROM {$this->post->getTable()} WHERE id = ?");
        return $stmt->execute([$id]);
    }
} 