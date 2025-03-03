<?php

namespace App\Models;

class User extends Model {
    protected string $table = 'users';
    protected array $fillable = ['name', 'email', 'password'];

    protected array $relations = [
        'posts' => null,
        'comments' => null
    ];

    public function __construct() {
        parent::__construct();
        $this->relations['posts'] = $this->hasMany(Post::class, 'user_id');
        $this->relations['comments'] = $this->hasMany(Comment::class, 'user_id');
    }
} 