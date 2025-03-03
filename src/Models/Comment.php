<?php

namespace App\Models;

class Comment extends Model {
    protected string $table = 'comments';
    protected array $fillable = ['content', 'user_id', 'post_id'];

    protected array $relations = [
        'user' => null,
        'post' => null
    ];

    public function __construct() {
        parent::__construct();
        $this->relations['user'] = $this->belongsTo(User::class, 'user_id');
        $this->relations['post'] = $this->belongsTo(Post::class, 'post_id');
    }
} 