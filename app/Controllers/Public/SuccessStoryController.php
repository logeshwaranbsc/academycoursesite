<?php

namespace App\Controllers\Public;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\SuccessStoryRepository;
use App\Repositories\TestimonialRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\CourseRepository;

class SuccessStoryController
{
    public function index(Request $request): Response
    {
        $storyRepo = new SuccessStoryRepository();
        $testimonialRepo = new TestimonialRepository();
        $settingsRepo = new SettingsRepository();
        $courseRepo = new CourseRepository();

        $data = [
            'settings' => $settingsRepo->get(),
            'successStories' => $storyRepo->getActive(),
            'testimonials' => $testimonialRepo->getActive(),
            'courses' => $courseRepo->getActive(),
            'title' => 'Student Success Stories & Reviews | Meridian Academy',
            'metaDescription' => 'Read inspiring stories and reviews from Meridian Academy alumni who transformed their careers.',
        ];

        return new Response(View::render('public.success-stories', $data));
    }
}
