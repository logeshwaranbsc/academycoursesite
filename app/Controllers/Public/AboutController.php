<?php

namespace App\Controllers\Public;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\SettingsRepository;
use App\Repositories\CourseRepository;

class AboutController
{
    public function index(Request $request): Response
    {
        $settingsRepo = new SettingsRepository();
        $courseRepo = new CourseRepository();

        $data = [
            'settings' => $settingsRepo->get(),
            'courses' => $courseRepo->getActive(),
            'title' => 'About Us — Our Mission & Story | Digital Teenz Academy',
            'metaDescription' => 'Learn about Digital Teenz Academy\'s mission to bridge academia and industry with real project training and 1-on-1 mentorship.',
        ];

        return new Response(View::render('public.about', $data));
    }
}
