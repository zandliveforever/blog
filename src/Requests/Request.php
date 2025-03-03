<?php

namespace App\Requests;

abstract class Request {
    protected array $data;
    protected array $rules = [];
    protected array $errors = [];

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function validate(): bool {
        $this->errors = [];
        foreach ($this->rules as $field => $rules) {
            if (!isset($this->data[$field]) && in_array('required', $rules)) {
                $this->errors[$field][] = "The {$field} field is required.";
                continue;
            }
            if (isset($this->data[$field])) {
                $this->validateField($field, $this->data[$field], $rules);
            }
        }
        return empty($this->errors);
    }

    protected function validateField(string $field, $value, array $rules): void {
        foreach ($rules as $rule) {
            if (str_contains($rule, ':')) {
                [$rule, $param] = explode(':', $rule);
            }
            
            switch ($rule) {
                case 'string':
                    if (!is_string($value)) {
                        $this->errors[$field][] = "The {$field} must be a string.";
                    }
                    break;
                case 'integer':
                    if (!filter_var($value, FILTER_VALIDATE_INT)) {
                        $this->errors[$field][] = "The {$field} must be an integer.";
                    }
                    break;
                case 'min':
                    if (is_string($value) && strlen($value) < (int)$param) {
                        $this->errors[$field][] = "The {$field} must be at least {$param} characters.";
                    } elseif (is_numeric($value) && $value < (int)$param) {
                        $this->errors[$field][] = "The {$field} must be at least {$param}.";
                    }
                    break;
                case 'max':
                    if (is_string($value) && strlen($value) > (int)$param) {
                        $this->errors[$field][] = "The {$field} must not exceed {$param} characters.";
                    }
                    break;
                case 'email':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->errors[$field][] = "The {$field} must be a valid email address.";
                    }
                    break;
            }
        }
    }

    public function getData(): array {
        return array_intersect_key($this->data, array_flip(array_keys($this->rules())));
    }

    public function getErrors(): array {
        return $this->errors;
    }

    abstract public function rules(): array;
} 