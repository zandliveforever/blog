<?php

namespace App\Actions\Post;

use App\Models\Post;
use PDO;

class GetPostsByUserIdAction {
    private Post $post;

    public function __construct() {
        $this->post = new Post();
    }

    public function execute(int $userId): array {
        $stmt = $this->post->getDb()->prepare("
            SELECT p.*, u.name as author_name,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
            FROM posts p 
            LEFT JOIN users u ON p.user_id = u.id 
            WHERE p.user_id = ?
        ");
        $stmt->execute([$userId]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($post) => $this->post->withRelations($post), $posts);
    }
} 