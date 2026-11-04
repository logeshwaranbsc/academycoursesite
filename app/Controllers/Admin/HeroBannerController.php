<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\HeroBannerRepository;
use App\Services\ImageUploadService;
use App\Services\ValidationService;

class HeroBannerController
{
    private HeroBannerRepository $repo;
    private ImageUploadService $uploadService;

    public function __construct()
    {
        $this->repo = new HeroBannerRepository();
        $this->uploadService = new ImageUploadService();
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int)$request->input('page', 1));
        $search = $request->input('search');

        $pagination = $this->repo->paginate($page, 15, $search);

        if ($request->isAjax()) {
            return Response::json($pagination);
        }

        return new Response(View::render('admin.banners.index', [
            'pagination' => $pagination,
            'search' => $search,
            'title' => 'Hero Banner Management — Digital Teenz Academy Admin',
            'currentNav' => 'hero-banners',
        ], 'admin-layout'));
    }

    public function store(Request $request): Response
    {
        $validator = new ValidationService();
        $data = [
            'title' => trim((string)$request->input('title')),
            'subtitle' => trim((string)$request->input('subtitle')),
            'button_text' => trim((string)$request->input('button_text')),
            'button_link' => trim((string)$request->input('button_link')),
            'display_order' => (int)$request->input('display_order', 0),
            'status' => in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'active',
        ];

        if (!$validator->validate($data, [
            'title' => 'max:255',
            'button_text' => 'max:100',
            'button_link' => 'max:255',
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            return Response::redirect('/admin/banners');
        }

        $file = $request->file('image');
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            if ($request->isAjax()) {
                return Response::json(['error' => 'A banner image file is required.'], 422);
            }
            flash('error', 'A banner image file is required.', 'error');
            return Response::redirect('/admin/banners');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $msg = match ($file['error']) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The uploaded banner image exceeds the server upload limit.',
                UPLOAD_ERR_PARTIAL => 'The uploaded banner image was only partially uploaded.',
                default => 'An error occurred during image upload (Error Code: ' . $file['error'] . ').',
            };
            if ($request->isAjax()) {
                return Response::json(['error' => $msg], 422);
            }
            flash('error', $msg, 'error');
            return Response::redirect('/admin/banners');
        }

        try {
            $data['image_url'] = $this->uploadService->upload($file, 'banners');
        } catch (\Exception $e) {
            if ($request->isAjax()) {
                return Response::json(['error' => $e->getMessage()], 422);
            }
            flash('error', $e->getMessage(), 'error');
            return Response::redirect('/admin/banners');
        }

        $banner = $this->repo->create($data);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Hero banner added successfully.', 'data' => $banner]);
        }

        flash('success', 'Hero banner added successfully.');
        return Response::redirect('/admin/banners');
    }

    public function update(Request $request, string|int $id): Response
    {
        $id = (int)$id;
        $banner = $this->repo->find($id);
        if (!$banner) {
            if ($request->isAjax()) {
                return Response::json(['error' => 'Banner not found.'], 404);
            }
            flash('error', 'Banner not found.', 'error');
            return Response::redirect('/admin/banners');
        }

        $validator = new ValidationService();
        $data = [
            'title' => trim((string)$request->input('title')),
            'subtitle' => trim((string)$request->input('subtitle')),
            'button_text' => trim((string)$request->input('button_text')),
            'button_link' => trim((string)$request->input('button_link')),
            'display_order' => (int)$request->input('display_order', 0),
            'status' => in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'active',
        ];

        if (!$validator->validate($data, [
            'title' => 'max:255',
            'button_text' => 'max:100',
            'button_link' => 'max:255',
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            return Response::redirect('/admin/banners');
        }

        $file = $request->file('image');
        if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $msg = match ($file['error']) {
                    UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The uploaded banner image exceeds the server upload limit.',
                    UPLOAD_ERR_PARTIAL => 'The uploaded banner image was only partially uploaded.',
                    default => 'An error occurred during image upload (Error Code: ' . $file['error'] . ').',
                };
                if ($request->isAjax()) {
                    return Response::json(['error' => $msg], 422);
                }
                flash('error', $msg, 'error');
                return Response::redirect('/admin/banners');
            }

            try {
                $data['image_url'] = $this->uploadService->replace($file, 'banners', $banner['image_url']);
            } catch (\Exception $e) {
                if ($request->isAjax()) {
                    return Response::json(['error' => $e->getMessage()], 422);
                }
                flash('error', $e->getMessage(), 'error');
                return Response::redirect('/admin/banners');
            }
        }

        $updated = $this->repo->update($id, $data);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Hero banner updated successfully.', 'data' => $updated]);
        }

        flash('success', 'Hero banner updated successfully.');
        return Response::redirect('/admin/banners');
    }

    public function destroy(Request $request, string|int $id): Response
    {
        $id = (int)$id;
        $banner = $this->repo->find($id);
        if ($banner) {
            $this->uploadService->delete($banner['image_url']);
            $this->repo->delete($id);
        }

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Banner deleted.']);
        }

        flash('success', 'Banner deleted.');
        return Response::redirect('/admin/banners');
    }

    public function toggleStatus(Request $request, string|int $id): Response
    {
        $newStatus = $this->repo->toggleStatus((int)$id);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'status' => $newStatus]);
        }

        flash('success', "Banner status changed to {$newStatus}.");
        return Response::redirect('/admin/banners');
    }
}
