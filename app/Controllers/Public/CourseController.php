<?php

namespace App\Controllers\Public;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\CourseRepository;
use App\Repositories\BatchRepository;
use App\Repositories\SettingsRepository;

class CourseController
{
    public function index(Request $request): Response
    {
        $courseRepo = new CourseRepository();
        $settingsRepo = new SettingsRepository();

        $data = [
            'settings' => $settingsRepo->get(),
            'courses' => $courseRepo->getActive(),
            'title' => 'Professional Courses & Certification | Meridian Academy',
            'metaDescription' => 'Explore industry-aligned courses in Web Development, Data Science, Digital Marketing, and UI/UX Design with job placement support.',
        ];

        return new Response(View::render('public.courses', $data));
    }

    public function show(Request $request, string|int $id): Response
    {
        $courseRepo = new CourseRepository();
        $batchRepo = new BatchRepository();
        $settingsRepo = new SettingsRepository();

        $course = $courseRepo->findActive((int)$id);
        if (!$course) {
            return new Response(View::render('public.404', ['title' => 'Course Not Found - Meridian Academy']), 404);
        }

        $batches = $batchRepo->getUpcomingActive((int)$id);

        $data = [
            'settings' => $settingsRepo->get(),
            'course' => $course,
            'batches' => $batches,
            'title' => "{$course['title']} | Meridian Academy",
            'metaDescription' => mb_substr(strip_tags($course['description']), 0, 160),
        ];

        return new Response(View::render('public.course-detail', $data));
    }
}
