<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\SettingsRepository;
use App\Services\ImageUploadService;
use App\Services\ValidationService;

class SettingsController
{
    private SettingsRepository $repo;
    private ImageUploadService $uploadService;

    public function __construct()
    {
        $this->repo = new SettingsRepository();
        $this->uploadService = new ImageUploadService();
    }

    public function index(Request $request): Response
    {
        $settings = $this->repo->get();

        if ($request->isAjax()) {
            return Response::json(['data' => $settings]);
        }

        return new Response(View::render('admin.settings.index', [
            'settings' => $settings,
            'title' => 'Website Settings — Meridian Admin',
            'currentNav' => 'settings',
        ], 'admin-layout'));
    }

    public function update(Request $request): Response
    {
        $current = $this->repo->get();

        $validator = new ValidationService();
        $data = [
            'academy_name' => trim((string)$request->input('academy_name')),
            'email' => trim((string)$request->input('email')),
            'phone' => trim((string)$request->input('phone')),
            'whatsapp' => trim((string)$request->input('whatsapp')),
            'address' => trim((string)$request->input('address')),
            'facebook_url' => trim((string)$request->input('facebook_url')),
            'instagram_url' => trim((string)$request->input('instagram_url')),
            'linkedin_url' => trim((string)$request->input('linkedin_url')),
            'youtube_url' => trim((string)$request->input('youtube_url')),
        ];

        if (!$validator->validate($data, [
            'academy_name' => 'required|max:255',
            'email' => 'email|max:255',
            'phone' => 'max:30',
            'whatsapp' => 'max:30',
            'address' => 'max:500',
            'facebook_url' => 'url',
            'instagram_url' => 'url',
            'linkedin_url' => 'url',
            'youtube_url' => 'url',
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            return Response::redirect('/admin/settings');
        }

        if ($request->hasFile('logo')) {
            try {
                $data['logo_url'] = $this->uploadService->replace($request->file('logo'), 'settings', $current['logo_url'] ?? null);
            } catch (\Exception $e) {
                if ($request->isAjax()) {
                    return Response::json(['error' => $e->getMessage()], 422);
                }
                flash('error', $e->getMessage(), 'error');
                return Response::redirect('/admin/settings');
            }
        }

        $updated = $this->repo->update($data);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Settings updated successfully.', 'data' => $updated]);
        }

        flash('success', 'Settings updated successfully.');
        return Response::redirect('/admin/settings');
    }
}
