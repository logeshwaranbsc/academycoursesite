<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\GalleryRepository;
use App\Services\ImageUploadService;
use App\Services\ValidationService;

class GalleryController
{
    private GalleryRepository $repo;
    private ImageUploadService $uploadService;

    public function __construct()
    {
        $this->repo = new GalleryRepository();
        $this->uploadService = new ImageUploadService();
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int)$request->input('page', 1));
        $search = $request->input('search');
        $category = $request->input('category');

        $pagination = $this->repo->paginate($page, 15, $search, $category);
        $categories = $this->repo->getCategories();

        if ($request->isAjax()) {
            return Response::json($pagination);
        }

        return new Response(View::render('admin.gallery.index', [
            'pagination' => $pagination,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $category,
            'title' => 'Gallery Management — Digital Teenz Academy Admin',
            'currentNav' => 'gallery',
        ], 'admin-layout'));
    }

    public function store(Request $request): Response
    {
        $validator = new ValidationService();
        $data = [
            'title' => trim((string)$request->input('title')),
            'category' => trim((string)$request->input('category')),
            'display_order' => (int)$request->input('display_order', 0),
            'status' => in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'active',
        ];

        if (!$validator->validate($data, [
            'title' => 'required|max:255',
            'category' => 'max:100',
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            return Response::redirect('/admin/gallery');
        }

        if (!$request->hasFile('image')) {
            if ($request->isAjax()) {
                return Response::json(['error' => 'An image file is required.'], 422);
            }
            flash('error', 'An image file is required.', 'error');
            return Response::redirect('/admin/gallery');
        }

        try {
            $data['image_url'] = $this->uploadService->upload($request->file('image'), 'gallery');
        } catch (\Exception $e) {
            if ($request->isAjax()) {
                return Response::json(['error' => $e->getMessage()], 422);
            }
            flash('error', $e->getMessage(), 'error');
            return Response::redirect('/admin/gallery');
        }

        $item = $this->repo->create($data);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Image added to gallery.', 'data' => $item]);
        }

        flash('success', 'Image added to gallery.');
        return Response::redirect('/admin/gallery');
    }

    public function update(Request $request, string|int $id): Response
    {
        $id = (int)$id;
        $item = $this->repo->find($id);
        if (!$item) {
            if ($request->isAjax()) {
                return Response::json(['error' => 'Gallery item not found.'], 404);
            }
            flash('error', 'Gallery item not found.', 'error');
            return Response::redirect('/admin/gallery');
        }

        $validator = new ValidationService();
        $data = [
            'title' => trim((string)$request->input('title')),
            'category' => trim((string)$request->input('category')),
            'display_order' => (int)$request->input('display_order', 0),
            'status' => in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'active',
        ];

        if (!$validator->validate($data, [
            'title' => 'required|max:255',
            'category' => 'max:100',
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            return Response::redirect('/admin/gallery');
        }

        if ($request->hasFile('image')) {
            try {
                $data['image_url'] = $this->uploadService->replace($request->file('image'), 'gallery', $item['image_url']);
            } catch (\Exception $e) {
                if ($request->isAjax()) {
                    return Response::json(['error' => $e->getMessage()], 422);
                }
                flash('error', $e->getMessage(), 'error');
                return Response::redirect('/admin/gallery');
            }
        }

        $updated = $this->repo->update($id, $data);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Gallery image updated.', 'data' => $updated]);
        }

        flash('success', 'Gallery image updated.');
        return Response::redirect('/admin/gallery');
    }

    public function destroy(Request $request, string|int $id): Response
    {
        $id = (int)$id;
        $item = $this->repo->find($id);
        if ($item) {
            $this->uploadService->delete($item['image_url']);
            $this->repo->delete($id);
        }

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Gallery image deleted.']);
        }

        flash('success', 'Gallery image deleted.');
        return Response::redirect('/admin/gallery');
    }

    public function toggleStatus(Request $request, string|int $id): Response
    {
        $newStatus = $this->repo->toggleStatus((int)$id);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'status' => $newStatus]);
        }

        flash('success', "Image status changed to {$newStatus}.");
        return Response::redirect('/admin/gallery');
    }
}
