<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'Meridian Academy',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim($_ENV['APP_URL'] ?? 'http://localhost:8000', '/'),
    'timezone' => 'Asia/Kolkata',
    'session_lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 7200),
    'session_name' => 'meridian_session',
];
