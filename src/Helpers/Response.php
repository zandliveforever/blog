<?php

namespace App\Helpers;

class Response {
    public static function success($data = null, string $message = ''): array {
        return [
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ];
    }

    public static function error(string $message, int $code = 400, array $errors = []): array {
        return [
            'status' => 'error',
            'message' => $message,
            'code' => $code,
            'errors' => $errors
        ];
    }
} 