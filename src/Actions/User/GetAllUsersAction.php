<?php

namespace App\Actions\User;

use App\Models\User;
use PDO;

class GetAllUsersAction {
    private User $user;

    public function __construct() {
        $this->user = new User();
    }

    public function execute(array $relations = []): array {
        $this->user->with($relations);
        $stmt = $this->user->getDb()->query("
            SELECT u.*, 
                (SELECT COUNT(*) FROM posts WHERE user_id = u.id) as posts_count,
                (SELECT COUNT(*) FROM comments WHERE user_id = u.id) as comments_count
            FROM users u
        ");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($user) => $this->user->withRelations($user), $users);
    }
} 