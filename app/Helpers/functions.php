<?php

use App\Core\Config;
use App\Core\View;

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('ensure_session_started')) {
    function ensure_session_started(): void
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        ensure_session_started();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'] ?? '';
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        $appUrl = Config::get('app.url', '');
        if ($appUrl === '') {
            $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
            $appUrl = "{$scheme}://{$host}";
        }
        $appUrl = rtrim($appUrl, '/');
        $path = ltrim($path, '/');
        return $path === '' ? $appUrl : "{$appUrl}/{$path}";
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return base_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('upload_url')) {
    function upload_url(?string $path): string
    {
        if (empty($path)) {
            return '';
        }
        // If it's already an absolute URL (e.g. from legacy external URLs)
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return base_url(ltrim($path, '/'));
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        if (isset($_SESSION['_old_input'][$key])) {
            $val = $_SESSION['_old_input'][$key];
            unset($_SESSION['_old_input'][$key]);
            return $val;
        }
        return $default;
    }
}

if (!function_exists('flash')) {
    function flash(string $key, mixed $message, string $type = 'success'): void
    {
        ensure_session_started();
        $_SESSION['_flash'][$key] = [
            'message' => $message,
            'type' => $type
        ];
    }
}

if (!function_exists('has_flash')) {
    function has_flash(string $key): bool
    {
        ensure_session_started();
        return isset($_SESSION['_flash'][$key]);
    }
}

if (!function_exists('get_flash')) {
    function get_flash(string $key): ?array
    {
        ensure_session_started();
        if (isset($_SESSION['_flash'][$key])) {
            $val = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $val;
        }
        return null;
    }
}

if (!function_exists('format_date')) {
    function format_date(?string $date): string
    {
        if (empty($date)) {
            return '';
        }
        try {
            $dt = new DateTime($date);
            return $dt->format('d M Y');
        } catch (\Exception) {
            return $date;
        }
    }
}

if (!function_exists('format_time')) {
    function format_time(?string $time): string
    {
        if (empty($time)) {
            return '';
        }
        try {
            $dt = new DateTime($time);
            return $dt->format('g:i A');
        } catch (\Exception) {
            return $time;
        }
    }
}

if (!function_exists('format_price')) {
    function format_price(?string $price): string
    {
        if (empty($price)) {
            return 'Contact for Fee';
        }
        if (is_numeric($price)) {
            return '₹' . number_format((float)$price);
        }
        return '₹' . $price;
    }
}

if (!function_exists('get_initials')) {
    function get_initials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));
        $initials = '';
        foreach ($words as $w) {
            if ($w !== '') {
                $initials .= mb_strtoupper(mb_substr($w, 0, 1));
            }
            if (mb_strlen($initials) >= 2) {
                break;
            }
        }
        return $initials ?: 'MA';
    }
}

if (!function_exists('youtube_embed_url')) {
    function youtube_embed_url(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }
        // Match standard youtube, youtu.be, or shorts
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }
        return $url;
    }
}

if (!function_exists('youtube_thumbnail')) {
    function youtube_thumbnail(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
            return 'https://img.youtube.com/vi/' . $matches[1] . '/hqdefault.jpg';
        }
        return null;
    }
}

if (!function_exists('current_user')) {
    function current_user(): ?array
    {
        ensure_session_started();
        return $_SESSION['admin_user'] ?? null;
    }
}

if (!function_exists('is_authenticated')) {
    function is_authenticated(): bool
    {
        ensure_session_started();
        return !empty($_SESSION['admin_user']['id']);
    }
}

if (!function_exists('view')) {
    function view(string $template, array $data = [], ?string $layout = 'public-layout'): string
    {
        return View::render($template, $data, $layout);
    }
}
