<?php

namespace App\Models;

class Post extends Model {
    protected string $table = 'posts';
    protected array $fillable = ['title', 'content', 'user_id'];

    protected array $relations = [
        'user' => null,
        'comments' => null
    ];

    public function __construct() {
        parent::__construct();
        $this->relations['user'] = $this->belongsTo(User::class, 'user_id');
        $this->relations['comments'] = $this->hasMany(Comment::class, 'post_id');
    }
} 