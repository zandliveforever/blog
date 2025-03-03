<?php

namespace App\Requests;

class UserRequest extends Request {
    private bool $isUpdate = false;

    public function rules(): array {
        return $this->isUpdate ? [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255']
        ] : [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6']
        ];
    }

    public function forUpdate(): self {
        $this->isUpdate = true;
        return $this;
    }
} 