<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\EnquiryRepository;
use App\Services\ValidationService;

class EnquiryController
{
    private EnquiryRepository $repo;

    public function __construct()
    {
        $this->repo = new EnquiryRepository();
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int)$request->input('page', 1));
        $search = $request->input('search');
        $status = $request->input('status');

        $pagination = $this->repo->paginate($page, 20, $search, $status);

        if ($request->isAjax()) {
            return Response::json($pagination);
        }

        return new Response(View::render('admin.enquiries.index', [
            'pagination' => $pagination,
            'search' => $search,
            'selectedStatus' => $status,
            'newCount' => $this->repo->countByStatus('new'),
            'contactedCount' => $this->repo->countByStatus('contacted'),
            'closedCount' => $this->repo->countByStatus('closed'),
            'title' => 'Enquiries Management — Digital Teenz Academy Admin',
            'currentNav' => 'enquiries',
        ], 'admin-layout'));
    }

    public function updateStatus(Request $request, string|int $id): Response
    {
        $id = (int)$id;
        $status = (string)$request->input('status');

        if (!in_array($status, ['new', 'contacted', 'closed'], true)) {
            if ($request->isAjax()) {
                return Response::json(['error' => 'Invalid status value.'], 422);
            }
            flash('error', 'Invalid status value.', 'error');
            return Response::redirect('/admin/enquiries');
        }

        $enquiry = $this->repo->updateStatus($id, $status);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => "Enquiry marked as {$status}.", 'data' => $enquiry]);
        }

        flash('success', "Enquiry status updated to {$status}.");
        return Response::redirect('/admin/enquiries');
    }

    public function destroy(Request $request, string|int $id): Response
    {
        $this->repo->delete((int)$id);

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => 'Enquiry deleted.']);
        }

        flash('success', 'Enquiry deleted.');
        return Response::redirect('/admin/enquiries');
    }
}
