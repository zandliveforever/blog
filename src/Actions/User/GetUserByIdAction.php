<?php

namespace App\Actions\User;

use App\Models\User;
use PDO;

class GetUserByIdAction {
    private User $user;

    public function __construct() {
        $this->user = new User();
    }

    public function execute(int $id): array|false {
        $stmt = $this->user->getDb()->prepare("
            SELECT u.*, 
                (SELECT COUNT(*) FROM posts WHERE user_id = u.id) as posts_count,
                (SELECT COUNT(*) FROM comments WHERE user_id = u.id) as comments_count
            FROM users u
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ? $this->user->withRelations($user) : false;
    }
} 