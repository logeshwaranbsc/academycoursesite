<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class CsrfMiddleware
{
    public function handle(Request $request): ?Response
    {
        $method = $request->method();

        // Read-only requests are safe from CSRF
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return null;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $sessionToken = $_SESSION['csrf_token'] ?? '';
        $providedToken = $request->input('csrf_token') ?? $request->header('x-csrf-token');

        if (empty($sessionToken) || empty($providedToken) || !hash_equals($sessionToken, $providedToken)) {
            if ($request->isAjax()) {
                return Response::json(['error' => 'CSRF token mismatch. Please reload the page.'], 419);
            }

            http_response_code(419);
            return new Response("
                <!DOCTYPE html>
                <html>
                <head><title>419 Page Expired</title><style>body{font-family:sans-serif;text-align:center;padding:50px;}</style></head>
                <body>
                    <h2>419 - Page Expired</h2>
                    <p>Your session or CSRF token has expired. Please refresh the page and try again.</p>
                    <a href='javascript:history.back()'>Go Back</a>
                </body>
                </html>
            ", 419);
        }

        return null;
    }
}
