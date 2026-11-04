<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\PlacementRepository;
use App\Services\ImageUploadService;
use App\Services\ValidationService;

class PlacementController
{
    private PlacementRepository $repo;
    private ImageUploadService $uploadService;

    public function __construct()
    {
        $this->repo = new PlacementRepository();
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

        return new Response(View::render('admin.placements.index', [
            'pagination' => $pagination,
            'search' => $search,
            'title' => 'Placed Students — Meridian Admin',
            'currentNav' => 'placements',
        ], 'admin-layout'));
    }

    public function store(Request $request): Response
    {
        $validator = new ValidationService();
        $data = [
            'student_name' => trim((string)$request->input('student_name')),
            'course' => trim((string)$request->input('course')),
            'company' => trim((string)$request->input('company')),
            'designation' => trim((string)$request->input('designation')),
            'description' => trim((string)$request->input('description')),
            'display_order' => (int)$request->input('display_order', 0),
            'status' => in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'active',
        ];

        if (!$validator->validate($data, [
            'student_name' => 'required|max:255',
            'course' => 'required|max:255',
            'company' => 'required|max:255',
            'designation' => 'required|max:255',
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            return Response::redirect('/admin/placements');
        }

        if ($request->hasFile('image')) {
            try {
                $data['image_url'] = $this->uploadService->upload($request->file('image'), 'placements');
            } catch (\Exception $e) {
                if ($request->isAjax()) {
                    return Response::json(['error' => $e->getMessage()], 422);
                }
                flash('error', $e->getMessage(), 'error');
                return Response::redirect('/admin/placements');
            }
        }

        $student = $this->repo->create($data);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Placement added successfully.', 'data' => $student]);
        }

        flash('success', 'Placement added successfully.');
        return Response::redirect('/admin/placements');
    }

    public function update(Request $request, string|int $id): Response
    {
        $id = (int)$id;
        $student = $this->repo->find($id);
        if (!$student) {
            if ($request->isAjax()) {
                return Response::json(['error' => 'Placement not found.'], 404);
            }
            flash('error', 'Placement not found.', 'error');
            return Response::redirect('/admin/placements');
        }

        $validator = new ValidationService();
        $data = [
            'student_name' => trim((string)$request->input('student_name')),
            'course' => trim((string)$request->input('course')),
            'company' => trim((string)$request->input('company')),
            'designation' => trim((string)$request->input('designation')),
            'description' => trim((string)$request->input('description')),
            'display_order' => (int)$request->input('display_order', 0),
            'status' => in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'active',
        ];

        if (!$validator->validate($data, [
            'student_name' => 'required|max:255',
            'course' => 'required|max:255',
            'company' => 'required|max:255',
            'designation' => 'required|max:255',
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            return Response::redirect('/admin/placements');
        }

        if ($request->hasFile('image')) {
            try {
                $data['image_url'] = $this->uploadService->replace($request->file('image'), 'placements', $student['image_url']);
            } catch (\Exception $e) {
                if ($request->isAjax()) {
                    return Response::json(['error' => $e->getMessage()], 422);
                }
                flash('error', $e->getMessage(), 'error');
                return Response::redirect('/admin/placements');
            }
        }

        $updated = $this->repo->update($id, $data);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Placement updated successfully.', 'data' => $updated]);
        }

        flash('success', 'Placement updated successfully.');
        return Response::redirect('/admin/placements');
    }

    public function destroy(Request $request, string|int $id): Response
    {
        $id = (int)$id;
        $student = $this->repo->find($id);
        if ($student) {
            $this->uploadService->delete($student['image_url']);
            $this->repo->delete($id);
        }

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Placement deleted successfully.']);
        }

        flash('success', 'Placement deleted successfully.');
        return Response::redirect('/admin/placements');
    }

    public function toggleStatus(Request $request, string|int $id): Response
    {
        $newStatus = $this->repo->toggleStatus((int)$id);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'status' => $newStatus]);
        }

        flash('success', "Placement status changed to {$newStatus}.");
        return Response::redirect('/admin/placements');
    }
}
