<?php

namespace App\Actions\User;

use App\Models\User;
use PDO;

class UpdateUserAction {
    private User $user;

    public function __construct() {
        $this->user = new User();
    }

    public function execute(int $id, array $data): bool {
        $checkStmt = $this->user->getDb()->prepare("SELECT 1 FROM users WHERE id = ?");
        $checkStmt->execute([$id]);
        if (!$checkStmt->fetch()) {
            throw new \Exception('User not found', 404);
        }

        if (isset($data['email'])) {
            $emailStmt = $this->user->getDb()->prepare("SELECT 1 FROM users WHERE email = ? AND id != ?");
            $emailStmt->execute([$data['email'], $id]);
            if ($emailStmt->fetch()) {
                throw new \Exception('Email already exists', 409);
            }
        }

        $fields = array_intersect_key($data, array_flip($this->user->getFillable()));
        if (empty($fields)) {
            return false;
        }

        if (isset($fields['password'])) {
            $fields['password'] = password_hash($fields['password'], PASSWORD_DEFAULT);
        }

        $setClause = implode(' = ?, ', array_keys($fields)) . ' = ?';
        $fields['updated_at'] = date('Y-m-d H:i:s');
        $setClause .= ', updated_at = ?';

        $stmt = $this->user->getDb()->prepare("
            UPDATE {$this->user->getTable()}
            SET $setClause
            WHERE id = ?
        ");

        $values = array_values($fields);
        $values[] = $id;

        return $stmt->execute($values);
    }
} 