<?php

namespace App\Actions\Post;

use App\Models\Post;
use PDO;

class GetPostByIdAction {
    private Post $post;

    public function __construct() {
        $this->post = new Post();
    }

    public function execute(int $id): array|false {
        $stmt = $this->post->getDb()->prepare("
            SELECT p.*, u.name as author_name,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
            FROM posts p 
            LEFT JOIN users u ON p.user_id = u.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        return $post ? $this->post->withRelations($post) : false;
    }
} 