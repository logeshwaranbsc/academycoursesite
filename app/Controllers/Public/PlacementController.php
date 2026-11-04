<?php

namespace App\Controllers\Public;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\PlacementRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\CourseRepository;

class PlacementController
{
    public function index(Request $request): Response
    {
        $placementRepo = new PlacementRepository();
        $settingsRepo = new SettingsRepository();
        $courseRepo = new CourseRepository();

        $data = [
            'settings' => $settingsRepo->get(),
            'placements' => $placementRepo->getActive(),
            'courses' => $courseRepo->getActive(),
            'title' => 'Student Placements & Hiring Partners | Meridian Academy',
            'metaDescription' => 'Over 94% placement rate across leading MNCs and tech startups. See where Meridian Academy graduates work.',
        ];

        return new Response(View::render('public.placements', $data));
    }
}
