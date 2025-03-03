<?php

namespace App\Actions\User;

use App\Models\User;
use PDO;

class DeleteUserAction {
    private User $user;

    public function __construct() {
        $this->user = new User();
    }

    public function execute(int $id): bool {
        $stmt = $this->user->getDb()->prepare("DELETE FROM {$this->user->getTable()} WHERE id = ?");
        return $stmt->execute([$id]);
    }
} 