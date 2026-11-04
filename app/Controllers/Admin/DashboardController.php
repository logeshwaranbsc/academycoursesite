<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\CourseRepository;
use App\Repositories\PlacementRepository;
use App\Repositories\TestimonialRepository;
use App\Repositories\CultureVideoRepository;
use App\Repositories\EnquiryRepository;
use App\Repositories\BatchRepository;
use App\Repositories\HeroBannerRepository;

class DashboardController
{
    public function index(Request $request): Response
    {
        $courseRepo = new CourseRepository();
        $placementRepo = new PlacementRepository();
        $testimonialRepo = new TestimonialRepository();
        $videoRepo = new CultureVideoRepository();
        $enquiryRepo = new EnquiryRepository();
        $batchRepo = new BatchRepository();
        $bannerRepo = new HeroBannerRepository();

        $stats = [
            'total_courses' => $courseRepo->count(),
            'active_courses' => $courseRepo->count("status = 'active'"),
            'total_placements' => $placementRepo->count(),
            'total_testimonials' => $testimonialRepo->count(),
            'total_videos' => $videoRepo->count(),
            'total_banners' => $bannerRepo->count(),
            'total_enquiries' => $enquiryRepo->count(),
            'new_enquiries' => $enquiryRepo->countByStatus('new'),
            'contacted_enquiries' => $enquiryRepo->countByStatus('contacted'),
            'closed_enquiries' => $enquiryRepo->countByStatus('closed'),
            'upcoming_batches' => $batchRepo->getUpcomingActive(),
            'recent_enquiries' => $enquiryRepo->getRecent(10),
        ];

        if ($request->isAjax()) {
            return Response::json(['data' => $stats]);
        }

        return new Response(View::render('admin.dashboard', [
            'stats' => $stats,
            'title' => 'Dashboard Overview — Digital Teenz Academy Admin',
            'currentNav' => 'dashboard',
        ], 'admin-layout'));
    }
}
