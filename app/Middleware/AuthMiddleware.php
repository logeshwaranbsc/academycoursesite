<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;

class AuthMiddleware
{
    public function handle(Request $request): ?Response
    {
        $auth = new AuthService();
        if (!$auth->check()) {
            if ($request->isAjax()) {
                return Response::json(['error' => 'Unauthorized. Please log in.'], 401);
            }
            return Response::redirect('/admin/login');
        }

        return null;
    }
}
