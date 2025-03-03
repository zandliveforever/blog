<?php

namespace App\Actions\User;

use App\Models\User;
use PDO;

class CreateUserAction {
    private User $user;

    public function __construct() {
        $this->user = new User();
    }

    public function execute(array $data): int {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $fields = array_intersect_key($data, array_flip($this->user->getFillable()));
        $columns = implode(', ', array_keys($fields));
        $values = implode(', ', array_fill(0, count($fields), '?'));
        
        $stmt = $this->user->getDb()->prepare("
            INSERT INTO {$this->user->getTable()} ($columns)
            VALUES ($values)
        ");
        
        $stmt->execute(array_values($fields));
        return (int)$this->user->getDb()->lastInsertId();
    }
} 