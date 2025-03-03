<?php

namespace App\Requests;

class CommentRequest extends Request {
    private bool $isUpdate = false;

    public function rules(): array {
        return $this->isUpdate ? [
            'content' => ['required', 'string', 'max:1000']
        ] : [
            'content' => ['required', 'string', 'max:1000'],
            'user_id' => ['required', 'integer'],
            'post_id' => ['required', 'integer']
        ];
    }

    public function forUpdate(): self {
        $this->isUpdate = true;
        return $this;
    }
} 