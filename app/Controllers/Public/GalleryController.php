<?php

namespace App\Controllers\Public;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\GalleryRepository;
use App\Repositories\CultureVideoRepository;
use App\Repositories\SettingsRepository;

class GalleryController
{
    public function index(Request $request): Response
    {
        $galleryRepo = new GalleryRepository();
        $videoRepo = new CultureVideoRepository();
        $settingsRepo = new SettingsRepository();

        $category = $request->input('category');

        $data = [
            'settings' => $settingsRepo->get(),
            'galleryItems' => $galleryRepo->getActive($category),
            'galleryCategories' => $galleryRepo->getCategories(),
            'cultureVideos' => $videoRepo->getActive(),
            'activeCategory' => $category ?: 'All',
            'title' => 'Campus Life & Photo Gallery | Digital Teenz Academy',
            'metaDescription' => 'Explore photos and videos of workshops, graduation ceremonies, hackathons, and campus life at Digital Teenz Academy.',
        ];

        return new Response(View::render('public.gallery', $data));
    }
}
