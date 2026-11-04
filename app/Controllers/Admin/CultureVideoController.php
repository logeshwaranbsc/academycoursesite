<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\CultureVideoRepository;
use App\Services\ImageUploadService;
use App\Services\ValidationService;

class CultureVideoController
{
    private CultureVideoRepository $repo;
    private ImageUploadService $uploadService;

    public function __construct()
    {
        $this->repo = new CultureVideoRepository();
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

        return new Response(View::render('admin.culture-videos.index', [
            'pagination' => $pagination,
            'search' => $search,
            'title' => 'Culture Videos — Digital Teenz Academy Admin',
            'currentNav' => 'culture-videos',
        ], 'admin-layout'));
    }

    public function store(Request $request): Response
    {
        $validator = new ValidationService();
        $data = [
            'title' => trim((string)$request->input('title')),
            'description' => trim((string)$request->input('description')),
            'video_url' => trim((string)$request->input('video_url')),
            'display_order' => (int)$request->input('display_order', 0),
            'status' => in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'active',
        ];

        if (!$validator->validate($data, [
            'title' => 'required|max:255',
            'video_url' => 'required|url',
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            return Response::redirect('/admin/culture-videos');
        }

        if ($request->hasFile('thumbnail')) {
            try {
                $data['thumbnail_url'] = $this->uploadService->upload($request->file('thumbnail'), 'culture-videos');
            } catch (\Exception $e) {
                if ($request->isAjax()) {
                    return Response::json(['error' => $e->getMessage()], 422);
                }
                flash('error', $e->getMessage(), 'error');
                return Response::redirect('/admin/culture-videos');
            }
        }

        $video = $this->repo->create($data);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Video added successfully.', 'data' => $video]);
        }

        flash('success', 'Video added successfully.');
        return Response::redirect('/admin/culture-videos');
    }

    public function update(Request $request, string|int $id): Response
    {
        $id = (int)$id;
        $video = $this->repo->find($id);
        if (!$video) {
            if ($request->isAjax()) {
                return Response::json(['error' => 'Video not found.'], 404);
            }
            flash('error', 'Video not found.', 'error');
            return Response::redirect('/admin/culture-videos');
        }

        $validator = new ValidationService();
        $data = [
            'title' => trim((string)$request->input('title')),
            'description' => trim((string)$request->input('description')),
            'video_url' => trim((string)$request->input('video_url')),
            'display_order' => (int)$request->input('display_order', 0),
            'status' => in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'active',
        ];

        if (!$validator->validate($data, [
            'title' => 'required|max:255',
            'video_url' => 'required|url',
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            return Response::redirect('/admin/culture-videos');
        }

        if ($request->hasFile('thumbnail')) {
            try {
                $data['thumbnail_url'] = $this->uploadService->replace($request->file('thumbnail'), 'culture-videos', $video['thumbnail_url']);
            } catch (\Exception $e) {
                if ($request->isAjax()) {
                    return Response::json(['error' => $e->getMessage()], 422);
                }
                flash('error', $e->getMessage(), 'error');
                return Response::redirect('/admin/culture-videos');
            }
        }

        $updated = $this->repo->update($id, $data);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Video updated successfully.', 'data' => $updated]);
        }

        flash('success', 'Video updated successfully.');
        return Response::redirect('/admin/culture-videos');
    }

    public function destroy(Request $request, string|int $id): Response
    {
        $id = (int)$id;
        $video = $this->repo->find($id);
        if ($video) {
            $this->uploadService->delete($video['thumbnail_url']);
            $this->repo->delete($id);
        }

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Video deleted successfully.']);
        }

        flash('success', 'Video deleted successfully.');
        return Response::redirect('/admin/culture-videos');
    }

    public function toggleStatus(Request $request, string|int $id): Response
    {
        $newStatus = $this->repo->toggleStatus((int)$id);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'status' => $newStatus]);
        }

        flash('success', "Video status changed to {$newStatus}.");
        return Response::redirect('/admin/culture-videos');
    }
}
