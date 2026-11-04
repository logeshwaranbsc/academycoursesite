<?php

namespace App\Controllers\Public;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\HeroBannerRepository;
use App\Repositories\CourseRepository;
use App\Repositories\BatchRepository;
use App\Repositories\PlacementRepository;
use App\Repositories\SuccessStoryRepository;
use App\Repositories\TestimonialRepository;
use App\Repositories\CultureVideoRepository;
use App\Repositories\GalleryRepository;
use App\Repositories\SettingsRepository;

class HomeController
{
    public function index(Request $request): Response
    {
        $heroBannerRepo = new HeroBannerRepository();
        $courseRepo = new CourseRepository();
        $batchRepo = new BatchRepository();
        $placementRepo = new PlacementRepository();
        $storyRepo = new SuccessStoryRepository();
        $testimonialRepo = new TestimonialRepository();
        $videoRepo = new CultureVideoRepository();
        $galleryRepo = new GalleryRepository();
        $settingsRepo = new SettingsRepository();

        $data = [
            'settings' => $settingsRepo->get(),
            'heroBanners' => $heroBannerRepo->getActive(),
            'courses' => $courseRepo->getActive(),
            'upcomingBatches' => $batchRepo->getUpcomingActive(),
            'placements' => $placementRepo->getActive(),
            'successStories' => $storyRepo->getActive(),
            'testimonials' => $testimonialRepo->getActive(),
            'cultureVideos' => $videoRepo->getActive(),
            'galleryItems' => $galleryRepo->getActive(),
            'galleryCategories' => $galleryRepo->getCategories(),
            'title' => 'Meridian Academy — Build Your Career With Confidence',
            'metaDescription' => 'Meridian Academy offers industry-relevant professional courses with expert mentorship, practical training, and dedicated career support.',
        ];

        return new Response(View::render('public.home', $data));
    }
}
