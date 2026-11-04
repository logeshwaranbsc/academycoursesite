<?php

use App\Core\Response;

if (!function_exists('json_response')) {
    function json_response(mixed $data, int $statusCode = 200, array $headers = []): Response
    {
        return Response::json($data, $statusCode, $headers);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url, int $statusCode = 302): Response
    {
        return Response::redirect($url, $statusCode);
    }
}
