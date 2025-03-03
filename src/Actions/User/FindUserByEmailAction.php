<?php

namespace App\Actions\User;

use App\Models\User;
use PDO;

class FindUserByEmailAction {
    private User $user;

    public function __construct() {
        $this->user = new User();
    }

    public function execute(string $email): array|false {
        $stmt = $this->user->getDb()->prepare("SELECT * FROM {$this->user->getTable()} WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ? $this->user->withRelations($user) : false;
    }
} 