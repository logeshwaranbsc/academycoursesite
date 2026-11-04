<?php

namespace App\Controllers\Public;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\SettingsRepository;
use App\Repositories\CourseRepository;
use App\Repositories\EnquiryRepository;
use App\Services\ValidationService;

class ContactController
{
    public function index(Request $request): Response
    {
        $settingsRepo = new SettingsRepository();
        $courseRepo = new CourseRepository();

        $data = [
            'settings' => $settingsRepo->get(),
            'courses' => $courseRepo->getActive(),
            'title' => 'Contact Us & Admissions Support | Digital Teenz Academy',
            'metaDescription' => 'Get in touch with Digital Teenz Academy admissions advisors for course counselling, batch schedules, and fee details.',
        ];

        return new Response(View::render('public.contact', $data));
    }

    public function submit(Request $request): Response
    {
        // Basic honeypot spam protection
        if (!empty($request->input('website_url'))) {
            // Bot filled out the invisible honeypot field
            if ($request->isAjax()) {
                return Response::json(['message' => 'Thank you for your enquiry.'], 200);
            }
            return Response::redirect('/contact');
        }

        $validator = new ValidationService();
        $data = [
            'name' => trim((string)$request->input('name')),
            'email' => trim((string)$request->input('email')),
            'phone' => trim((string)$request->input('phone')),
            'course' => trim((string)$request->input('course')),
            'message' => trim((string)$request->input('message')),
        ];

        $rules = [
            'name' => 'required|max:255',
            'phone' => 'required|phone|max:20',
            'email' => 'email|max:255',
            'course' => 'max:255',
            'message' => 'max:2000',
        ];

        if (!$validator->validate($data, $rules)) {
            if ($request->isAjax()) {
                return Response::json([
                    'error' => $validator->firstError(),
                    'errors' => $validator->errors()
                ], 422);
            }
            flash('enquiry_error', $validator->firstError(), 'error');
            $_SESSION['_old_input'] = $data;
            return Response::redirect($request->header('referer') ?: '/contact');
        }

        // Insert into database
        $enquiryRepo = new EnquiryRepository();
        $enquiry = $enquiryRepo->create($data);

        $msg = 'Thank you for your enquiry! Our academic advisor will contact you within one business day.';

        if ($request->isAjax()) {
            return Response::json([
                'success' => true,
                'message' => $msg,
                'data' => $enquiry
            ], 201);
        }

        flash('enquiry_success', $msg, 'success');
        return Response::redirect($request->header('referer') ?: '/contact');
    }
}
