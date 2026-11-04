<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\CourseRepository;
use App\Services\ImageUploadService;
use App\Services\ValidationService;

class CourseController
{
    private CourseRepository $repo;
    private ImageUploadService $uploadService;

    public function __construct()
    {
        $this->repo = new CourseRepository();
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

        return new Response(View::render('admin.courses.index', [
            'pagination' => $pagination,
            'search' => $search,
            'title' => 'Course Management — Meridian Admin',
            'currentNav' => 'courses',
        ], 'admin-layout'));
    }

    public function store(Request $request): Response
    {
        $validator = new ValidationService();
        $data = [
            'title' => trim((string)$request->input('title')),
            'description' => trim((string)$request->input('description')),
            'duration' => trim((string)$request->input('duration')),
            'mode' => trim((string)$request->input('mode', 'Classroom')),
            'price' => trim((string)$request->input('price')),
            'status' => in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'active',
        ];

        if (!$validator->validate($data, [
            'title' => 'required|max:255',
            'description' => 'required',
            'duration' => 'required|max:100',
            'mode' => 'required|max:100',
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            return Response::redirect('/admin/courses');
        }

        if ($request->hasFile('image')) {
            try {
                $data['image_url'] = $this->uploadService->upload($request->file('image'), 'courses');
            } catch (\Exception $e) {
                if ($request->isAjax()) {
                    return Response::json(['error' => $e->getMessage()], 422);
                }
                flash('error', $e->getMessage(), 'error');
                return Response::redirect('/admin/courses');
            }
        }

        $course = $this->repo->create($data);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Course created successfully.', 'data' => $course]);
        }

        flash('success', 'Course created successfully.');
        return Response::redirect('/admin/courses');
    }

    public function update(Request $request, string|int $id): Response
    {
        $id = (int)$id;
        $course = $this->repo->find($id);
        if (!$course) {
            if ($request->isAjax()) {
                return Response::json(['error' => 'Course not found.'], 404);
            }
            flash('error', 'Course not found.', 'error');
            return Response::redirect('/admin/courses');
        }

        $validator = new ValidationService();
        $data = [
            'title' => trim((string)$request->input('title')),
            'description' => trim((string)$request->input('description')),
            'duration' => trim((string)$request->input('duration')),
            'mode' => trim((string)$request->input('mode', 'Classroom')),
            'price' => trim((string)$request->input('price')),
            'status' => in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'active',
        ];

        if (!$validator->validate($data, [
            'title' => 'required|max:255',
            'description' => 'required',
            'duration' => 'required|max:100',
            'mode' => 'required|max:100',
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            return Response::redirect('/admin/courses');
        }

        if ($request->hasFile('image')) {
            try {
                $data['image_url'] = $this->uploadService->replace($request->file('image'), 'courses', $course['image_url']);
            } catch (\Exception $e) {
                if ($request->isAjax()) {
                    return Response::json(['error' => $e->getMessage()], 422);
                }
                flash('error', $e->getMessage(), 'error');
                return Response::redirect('/admin/courses');
            }
        }

        $updated = $this->repo->update($id, $data);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Course updated successfully.', 'data' => $updated]);
        }

        flash('success', 'Course updated successfully.');
        return Response::redirect('/admin/courses');
    }

    public function destroy(Request $request, string|int $id): Response
    {
        $id = (int)$id;
        $course = $this->repo->find($id);
        if ($course) {
            $this->uploadService->delete($course['image_url']);
            $this->repo->delete($id);
        }

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Course deleted successfully.']);
        }

        flash('success', 'Course deleted successfully.');
        return Response::redirect('/admin/courses');
    }

    public function toggleStatus(Request $request, string|int $id): Response
    {
        $newStatus = $this->repo->toggleStatus((int)$id);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'status' => $newStatus]);
        }

        flash('success', "Course status changed to {$newStatus}.");
        return Response::redirect('/admin/courses');
    }
}
