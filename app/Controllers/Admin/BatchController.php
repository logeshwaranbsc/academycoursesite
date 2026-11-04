<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\BatchRepository;
use App\Repositories\CourseRepository;
use App\Services\ValidationService;

class BatchController
{
    private BatchRepository $repo;
    private CourseRepository $courseRepo;

    public function __construct()
    {
        $this->repo = new BatchRepository();
        $this->courseRepo = new CourseRepository();
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int)$request->input('page', 1));
        $search = $request->input('search');

        $pagination = $this->repo->paginate($page, 15, $search);
        $courses = $this->courseRepo->all('title', 'ASC');

        if ($request->isAjax()) {
            return Response::json($pagination);
        }

        return new Response(View::render('admin.batches.index', [
            'pagination' => $pagination,
            'courses' => $courses,
            'search' => $search,
            'title' => 'Batch Management — Meridian Admin',
            'currentNav' => 'batches',
        ], 'admin-layout'));
    }

    public function store(Request $request): Response
    {
        $validator = new ValidationService();
        $data = [
            'course_id' => (int)$request->input('course_id'),
            'batch_name' => trim((string)$request->input('batch_name')),
            'start_date' => trim((string)$request->input('start_date')),
            'start_time' => trim((string)$request->input('start_time')),
            'duration' => trim((string)$request->input('duration')),
            'location' => trim((string)$request->input('location')),
            'available_seats' => max(0, (int)$request->input('available_seats', 30)),
            'status' => in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'active',
        ];

        if (!$validator->validate($data, [
            'course_id' => 'required|integer',
            'batch_name' => 'required|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'duration' => 'required|max:100',
            'location' => 'required|max:255',
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            return Response::redirect('/admin/batches');
        }

        $batch = $this->repo->create($data);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Batch created successfully.', 'data' => $batch]);
        }

        flash('success', 'Batch created successfully.');
        return Response::redirect('/admin/batches');
    }

    public function update(Request $request, string|int $id): Response
    {
        $id = (int)$id;
        $batch = $this->repo->find($id);
        if (!$batch) {
            if ($request->isAjax()) {
                return Response::json(['error' => 'Batch not found.'], 404);
            }
            flash('error', 'Batch not found.', 'error');
            return Response::redirect('/admin/batches');
        }

        $validator = new ValidationService();
        $data = [
            'course_id' => (int)$request->input('course_id'),
            'batch_name' => trim((string)$request->input('batch_name')),
            'start_date' => trim((string)$request->input('start_date')),
            'start_time' => trim((string)$request->input('start_time')),
            'duration' => trim((string)$request->input('duration')),
            'location' => trim((string)$request->input('location')),
            'available_seats' => max(0, (int)$request->input('available_seats', 30)),
            'status' => in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'active',
        ];

        if (!$validator->validate($data, [
            'course_id' => 'required|integer',
            'batch_name' => 'required|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'duration' => 'required|max:100',
            'location' => 'required|max:255',
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            return Response::redirect('/admin/batches');
        }

        $updated = $this->repo->update($id, $data);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Batch updated successfully.', 'data' => $updated]);
        }

        flash('success', 'Batch updated successfully.');
        return Response::redirect('/admin/batches');
    }

    public function destroy(Request $request, string|int $id): Response
    {
        $this->repo->delete((int)$id);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Batch deleted successfully.']);
        }

        flash('success', 'Batch deleted successfully.');
        return Response::redirect('/admin/batches');
    }

    public function toggleStatus(Request $request, string|int $id): Response
    {
        $newStatus = $this->repo->toggleStatus((int)$id);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'status' => $newStatus]);
        }

        flash('success', "Batch status changed to {$newStatus}.");
        return Response::redirect('/admin/batches');
    }
}
