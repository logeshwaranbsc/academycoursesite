<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\SuccessStoryRepository;
use App\Services\ImageUploadService;
use App\Services\ValidationService;

class SuccessStoryController
{
    private SuccessStoryRepository $repo;
    private ImageUploadService $uploadService;

    public function __construct()
    {
        $this->repo = new SuccessStoryRepository();
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

        return new Response(View::render('admin.success-stories.index', [
            'pagination' => $pagination,
            'search' => $search,
            'title' => 'Success Stories — Digital Teenz Academy Admin',
            'currentNav' => 'success-stories',
        ], 'admin-layout'));
    }

    public function store(Request $request): Response
    {
        $validator = new ValidationService();
        $data = [
            'title' => trim((string)$request->input('title')),
            'student_name' => trim((string)$request->input('student_name')),
            'description' => trim((string)$request->input('description')),
            'video_url' => trim((string)$request->input('video_url')),
            'display_order' => (int)$request->input('display_order', 0),
            'status' => in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'active',
        ];

        if (!$validator->validate($data, [
            'title' => 'required|max:255',
            'student_name' => 'required|max:255',
            'description' => 'required',
            'video_url' => 'url',
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            return Response::redirect('/admin/success-stories');
        }

        if ($request->hasFile('image')) {
            try {
                $data['image_url'] = $this->uploadService->upload($request->file('image'), 'success-stories');
            } catch (\Exception $e) {
                if ($request->isAjax()) {
                    return Response::json(['error' => $e->getMessage()], 422);
                }
                flash('error', $e->getMessage(), 'error');
                return Response::redirect('/admin/success-stories');
            }
        }

        $story = $this->repo->create($data);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Success story added successfully.', 'data' => $story]);
        }

        flash('success', 'Success story added successfully.');
        return Response::redirect('/admin/success-stories');
    }

    public function update(Request $request, string|int $id): Response
    {
        $id = (int)$id;
        $story = $this->repo->find($id);
        if (!$story) {
            if ($request->isAjax()) {
                return Response::json(['error' => 'Story not found.'], 404);
            }
            flash('error', 'Story not found.', 'error');
            return Response::redirect('/admin/success-stories');
        }

        $validator = new ValidationService();
        $data = [
            'title' => trim((string)$request->input('title')),
            'student_name' => trim((string)$request->input('student_name')),
            'description' => trim((string)$request->input('description')),
            'video_url' => trim((string)$request->input('video_url')),
            'display_order' => (int)$request->input('display_order', 0),
            'status' => in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'active',
        ];

        if (!$validator->validate($data, [
            'title' => 'required|max:255',
            'student_name' => 'required|max:255',
            'description' => 'required',
            'video_url' => 'url',
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            return Response::redirect('/admin/success-stories');
        }

        if ($request->hasFile('image')) {
            try {
                $data['image_url'] = $this->uploadService->replace($request->file('image'), 'success-stories', $story['image_url']);
            } catch (\Exception $e) {
                if ($request->isAjax()) {
                    return Response::json(['error' => $e->getMessage()], 422);
                }
                flash('error', $e->getMessage(), 'error');
                return Response::redirect('/admin/success-stories');
            }
        }

        $updated = $this->repo->update($id, $data);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Success story updated successfully.', 'data' => $updated]);
        }

        flash('success', 'Success story updated successfully.');
        return Response::redirect('/admin/success-stories');
    }

    public function destroy(Request $request, string|int $id): Response
    {
        $id = (int)$id;
        $story = $this->repo->find($id);
        if ($story) {
            $this->uploadService->delete($story['image_url']);
            $this->repo->delete($id);
        }

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Story deleted successfully.']);
        }

        flash('success', 'Story deleted successfully.');
        return Response::redirect('/admin/success-stories');
    }

    public function toggleStatus(Request $request, string|int $id): Response
    {
        $newStatus = $this->repo->toggleStatus((int)$id);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'status' => $newStatus]);
        }

        flash('success', "Story status changed to {$newStatus}.");
        return Response::redirect('/admin/success-stories');
    }
}
