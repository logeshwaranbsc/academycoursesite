<?php

namespace App\Services;

use App\Core\Config;
use PDO;

class AuthService
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_SECONDS = 900; // 15 minutes

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            $sessionPath = dirname(dirname(__DIR__)) . '/storage/sessions';
            if (!is_dir($sessionPath)) {
                @mkdir($sessionPath, 0777, true);
            }
            @session_save_path($sessionPath);
            $lifetime = Config::get('app.session_lifetime', 7200);
            @session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            @session_start();
        }

        $this->checkSessionTimeout();
    }

    public function checkSessionTimeout(): void
    {
        if (!empty($_SESSION['admin_user'])) {
            $lifetime = Config::get('app.session_lifetime', 7200);
            $lastActivity = $_SESSION['admin_last_activity'] ?? time();

            if (time() - $lastActivity > $lifetime) {
                $this->logout();
            } else {
                $_SESSION['admin_last_activity'] = time();
            }
        }
    }

    public function isThrottled(string $ip): bool
    {
        if (isset($_SESSION['login_throttle'][$ip])) {
            $data = $_SESSION['login_throttle'][$ip];
            if ($data['attempts'] >= self::MAX_ATTEMPTS) {
                if (time() - $data['last_attempt'] < self::LOCKOUT_SECONDS) {
                    return true;
                } else {
                    unset($_SESSION['login_throttle'][$ip]);
                }
            }
        }
        return false;
    }

    public function getRemainingLockoutTime(string $ip): int
    {
        if (isset($_SESSION['login_throttle'][$ip])) {
            $data = $_SESSION['login_throttle'][$ip];
            $elapsed = time() - $data['last_attempt'];
            return max(0, self::LOCKOUT_SECONDS - $elapsed);
        }
        return 0;
    }

    public function recordFailedAttempt(string $ip): void
    {
        if (!isset($_SESSION['login_throttle'][$ip])) {
            $_SESSION['login_throttle'][$ip] = [
                'attempts' => 1,
                'last_attempt' => time(),
            ];
        } else {
            $_SESSION['login_throttle'][$ip]['attempts']++;
            $_SESSION['login_throttle'][$ip]['last_attempt'] = time();
        }
    }

    public function clearFailedAttempts(string $ip): void
    {
        unset($_SESSION['login_throttle'][$ip]);
    }

    public function attempt(string $email, string $password, string $ip = '127.0.0.1'): array
    {
        if ($this->isThrottled($ip)) {
            $remaining = ceil($this->getRemainingLockoutTime($ip) / 60);
            return [
                'success' => false,
                'message' => "Too many failed login attempts. Please try again in {$remaining} minutes."
            ];
        }

        $pdo = DatabaseService::getConnection();
        $stmt = $pdo->prepare("SELECT id, name, email, password FROM admins WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => trim($email)]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($password, $admin['password'])) {
            $this->recordFailedAttempt($ip);
            return [
                'success' => false,
                'message' => 'The provided credentials do not match our records.'
            ];
        }

        // Login successful: reset throttling
        $this->clearFailedAttempts($ip);

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        $_SESSION['admin_user'] = [
            'id' => (int)$admin['id'],
            'name' => $admin['name'],
            'email' => $admin['email'],
        ];
        $_SESSION['admin_last_activity'] = time();

        return [
            'success' => true,
            'admin' => $_SESSION['admin_user'],
            'message' => 'Login successful.'
        ];
    }

    public function check(): bool
    {
        return !empty($_SESSION['admin_user']['id']);
    }

    public function user(): ?array
    {
        return $_SESSION['admin_user'] ?? null;
    }

    public function logout(): void
    {
        unset($_SESSION['admin_user'], $_SESSION['admin_last_activity']);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}
