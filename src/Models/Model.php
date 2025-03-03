<?php

namespace App\Models;

use App\Database\Database;
use PDO;

abstract class Model {
    protected PDO $db;
    protected string $table;
    protected array $fillable = [];
    protected array $relations = [];
    protected array $with = [];

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getDb(): PDO {
        return $this->db;
    }

    public function with(array $relations): self {
        $this->with = array_merge($this->with, $relations);
        return $this;
    }
    public function findWithRelations(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        return $item ? $this->loadRelations($item) : false;
    }

    protected function loadRelations(array $data): array {
        foreach ($this->with as $relation) {
            if (isset($this->relations[$relation])) {
                $data = $this->relations[$relation]($data);
            }
        }
        return $data;
    }

    protected function hasMany(string $model, string $foreignKey, string $localKey = 'id'): callable {
        return function ($data) use ($model, $foreignKey, $localKey) {
            $relatedModel = new $model();
            $stmt = $this->db->prepare("SELECT * FROM {$relatedModel->getTable()} WHERE $foreignKey = ?");
            $stmt->execute([$data[$localKey]]);
            return array_merge($data, [$this->getRelationName($model) => $stmt->fetchAll()]);
        };
    }

    protected function belongsTo(string $model, string $foreignKey, string $ownerKey = 'id'): callable {
        return function ($data) use ($model, $foreignKey, $ownerKey) {
            $relatedModel = new $model();
            $stmt = $this->db->prepare("SELECT * FROM {$relatedModel->getTable()} WHERE $ownerKey = ?");
            $stmt->execute([$data[$foreignKey]]);
            return array_merge($data, [$this->getRelationName($model) => $stmt->fetch()]);
        };
    }

    protected function getRelationName(string $model): string {
        $parts = explode('\\', $model);
        return strtolower(end($parts));
    }

    public function getTable(): string {
        return $this->table;
    }

    public function getFillable(): array {
        return $this->fillable;
    }

    public function withRelations(array $data): array {
        return $this->loadRelations($data);
    }
} 