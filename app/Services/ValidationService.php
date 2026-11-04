<?php

namespace App\Services;

class ValidationService
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = is_array($ruleString) ? $ruleString : explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $params = [];
                if (strpos($rule, ':') !== false) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                $this->applyRule($field, $value, $rule, $params);
            }
        }

        return empty($this->errors);
    }

    private function applyRule(string $field, mixed $value, string $rule, array $params): void
    {
        $label = ucfirst(str_replace('_', ' ', $field));

        switch ($rule) {
            case 'required':
                if ($value === null || (is_string($value) && trim($value) === '') || (is_array($value) && empty($value))) {
                    $this->addError($field, "{$label} is required.");
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "{$label} must be a valid email address.");
                }
                break;

            case 'phone':
                if (!empty($value) && !preg_match('/^[+\d\s()-]{7,20}$/', (string)$value)) {
                    $this->addError($field, "{$label} must be a valid phone number.");
                }
                break;

            case 'url':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addError($field, "{$label} must be a valid URL.");
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "{$label} must be a number.");
                }
                break;

            case 'integer':
                if (!empty($value) && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $this->addError($field, "{$label} must be an integer.");
                }
                break;

            case 'min':
                $min = (int)($params[0] ?? 0);
                if (is_numeric($value) && (float)$value < $min) {
                    $this->addError($field, "{$label} must be at least {$min}.");
                } elseif (is_string($value) && mb_strlen($value) < $min) {
                    $this->addError($field, "{$label} must be at least {$min} characters.");
                }
                break;

            case 'max':
                $max = (int)($params[0] ?? 255);
                if (is_numeric($value) && (float)$value > $max) {
                    $this->addError($field, "{$label} must not exceed {$max}.");
                } elseif (is_string($value) && mb_strlen($value) > $max) {
                    $this->addError($field, "{$label} must not exceed {$max} characters.");
                }
                break;

            case 'in':
                if (!empty($value) && !in_array($value, $params, true)) {
                    $this->addError($field, "{$label} must be one of: " . implode(', ', $params));
                }
                break;

            case 'date':
                if (!empty($value) && strtotime((string)$value) === false) {
                    $this->addError($field, "{$label} must be a valid date.");
                }
                break;
        }
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
}
