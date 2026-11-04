<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Services\AuthService;
use App\Services\ValidationService;

class AuthController
{
    public function showLogin(Request $request): Response
    {
        $auth = new AuthService();
        if ($auth->check()) {
            return Response::redirect('/admin/dashboard');
        }

        return new Response(View::render('admin.login', [
            'title' => 'Admin Login — Meridian Academy'
        ], null));
    }

    public function login(Request $request): Response
    {
        $auth = new AuthService();
        if ($auth->check()) {
            if ($request->isAjax()) {
                return Response::json(['redirect' => '/admin/dashboard']);
            }
            return Response::redirect('/admin/dashboard');
        }

        $email = (string)$request->input('email');
        $password = (string)$request->input('password');

        $validator = new ValidationService();
        if (!$validator->validate(['email' => $email, 'password' => $password], [
            'email' => 'required|email',
            'password' => 'required'
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            $_SESSION['_old_input'] = ['email' => $email];
            return Response::redirect('/admin/login');
        }

        $result = $auth->attempt($email, $password, $request->ip());

        if (!$result['success']) {
            if ($request->isAjax()) {
                return Response::json(['error' => $result['message']], 401);
            }
            flash('error', $result['message'], 'error');
            $_SESSION['_old_input'] = ['email' => $email];
            return Response::redirect('/admin/login');
        }

        if ($request->isAjax()) {
            return Response::json([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => '/admin/dashboard'
            ]);
        }

        flash('success', 'Welcome back, ' . $result['admin']['name'] . '!');
        return Response::redirect('/admin/dashboard');
    }

    public function logout(Request $request): Response
    {
        $auth = new AuthService();
        $auth->logout();

        if ($request->isAjax()) {
            return Response::json(['redirect' => '/admin/login']);
        }

        flash('success', 'You have been logged out.');
        return Response::redirect('/admin/login');
    }
}
