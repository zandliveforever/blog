<?php

namespace App\Requests;

class PostRequest extends Request {
    private bool $isUpdate = false;

    public function rules(): array {
        error_log('PostRequest data: ' . json_encode($this->data));
        return $this->isUpdate ? [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string']
        ] : [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'user_id' => ['required', 'integer']
        ];
    }

    public function getData(): array {
        $data = parent::getData();
        error_log('PostRequest getData: ' . json_encode($data));
        return $data;
    }

    public function forUpdate(): self {
        $this->isUpdate = true;
        return $this;
    }
} 