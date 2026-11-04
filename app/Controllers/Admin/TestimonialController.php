<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\TestimonialRepository;
use App\Services\ImageUploadService;
use App\Services\ValidationService;

class TestimonialController
{
    private TestimonialRepository $repo;
    private ImageUploadService $uploadService;

    public function __construct()
    {
        $this->repo = new TestimonialRepository();
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

        return new Response(View::render('admin.testimonials.index', [
            'pagination' => $pagination,
            'search' => $search,
            'title' => 'Testimonials — Meridian Admin',
            'currentNav' => 'testimonials',
        ], 'admin-layout'));
    }

    public function store(Request $request): Response
    {
        $validator = new ValidationService();
        $data = [
            'student_name' => trim((string)$request->input('student_name')),
            'course' => trim((string)$request->input('course')),
            'testimonial' => trim((string)$request->input('testimonial')),
            'rating' => max(1, min(5, (int)$request->input('rating', 5))),
            'video_url' => trim((string)$request->input('video_url')),
            'display_order' => (int)$request->input('display_order', 0),
            'status' => in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'active',
        ];

        if (!$validator->validate($data, [
            'student_name' => 'required|max:255',
            'course' => 'required|max:255',
            'testimonial' => 'required',
            'video_url' => 'url',
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            return Response::redirect('/admin/testimonials');
        }

        if ($request->hasFile('image')) {
            try {
                $data['student_image'] = $this->uploadService->upload($request->file('image'), 'testimonials');
            } catch (\Exception $e) {
                if ($request->isAjax()) {
                    return Response::json(['error' => $e->getMessage()], 422);
                }
                flash('error', $e->getMessage(), 'error');
                return Response::redirect('/admin/testimonials');
            }
        }

        $item = $this->repo->create($data);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Testimonial added successfully.', 'data' => $item]);
        }

        flash('success', 'Testimonial added successfully.');
        return Response::redirect('/admin/testimonials');
    }

    public function update(Request $request, string|int $id): Response
    {
        $id = (int)$id;
        $testimonial = $this->repo->find($id);
        if (!$testimonial) {
            if ($request->isAjax()) {
                return Response::json(['error' => 'Testimonial not found.'], 404);
            }
            flash('error', 'Testimonial not found.', 'error');
            return Response::redirect('/admin/testimonials');
        }

        $validator = new ValidationService();
        $data = [
            'student_name' => trim((string)$request->input('student_name')),
            'course' => trim((string)$request->input('course')),
            'testimonial' => trim((string)$request->input('testimonial')),
            'rating' => max(1, min(5, (int)$request->input('rating', 5))),
            'video_url' => trim((string)$request->input('video_url')),
            'display_order' => (int)$request->input('display_order', 0),
            'status' => in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'active',
        ];

        if (!$validator->validate($data, [
            'student_name' => 'required|max:255',
            'course' => 'required|max:255',
            'testimonial' => 'required',
            'video_url' => 'url',
        ])) {
            if ($request->isAjax()) {
                return Response::json(['error' => $validator->firstError()], 422);
            }
            flash('error', $validator->firstError(), 'error');
            return Response::redirect('/admin/testimonials');
        }

        if ($request->hasFile('image')) {
            try {
                $data['student_image'] = $this->uploadService->replace($request->file('image'), 'testimonials', $testimonial['student_image']);
            } catch (\Exception $e) {
                if ($request->isAjax()) {
                    return Response::json(['error' => $e->getMessage()], 422);
                }
                flash('error', $e->getMessage(), 'error');
                return Response::redirect('/admin/testimonials');
            }
        }

        $updated = $this->repo->update($id, $data);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Testimonial updated successfully.', 'data' => $updated]);
        }

        flash('success', 'Testimonial updated successfully.');
        return Response::redirect('/admin/testimonials');
    }

    public function destroy(Request $request, string|int $id): Response
    {
        $id = (int)$id;
        $testimonial = $this->repo->find($id);
        if ($testimonial) {
            $this->uploadService->delete($testimonial['student_image']);
            $this->repo->delete($id);
        }

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Testimonial deleted successfully.']);
        }

        flash('success', 'Testimonial deleted successfully.');
        return Response::redirect('/admin/testimonials');
    }

    public function toggleStatus(Request $request, string|int $id): Response
    {
        $newStatus = $this->repo->toggleStatus((int)$id);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'status' => $newStatus]);
        }

        flash('success', "Testimonial status changed to {$newStatus}.");
        return Response::redirect('/admin/testimonials');
    }
}
