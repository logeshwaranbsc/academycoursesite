<?php
use App\Core\View;

// Hero Banner Slider
echo View::component('hero-slider', ['heroBanners' => $heroBanners]);

// 4 Pillars Highlights
echo View::component('highlights');

// Course Catalog Grid (Show top 6 on homepage)
echo View::component('courses-grid', [
    'courses' => $courses,
    'limit' => 6,
    'showAllLink' => true,
]);

// Placements Grid (Show top 8 on homepage)
echo View::component('placements-grid', [
    'placements' => $placements,
    'limit' => 8,
    'showAllLink' => true,
]);

// Success Stories Grid (Show top 3 on homepage)
echo View::component('success-stories-grid', [
    'successStories' => $successStories,
    'limit' => 3,
    'showAllLink' => true,
]);

// Testimonials Grid (Show top 3 on homepage)
echo View::component('testimonials-grid', [
    'testimonials' => $testimonials,
    'limit' => 3,
]);

// Culture Videos Grid (Show top 3 on homepage)
echo View::component('culture-videos-grid', [
    'cultureVideos' => $cultureVideos,
    'limit' => 3,
]);

// Campus Gallery
echo View::component('gallery-grid', [
    'galleryItems' => $galleryItems,
    'galleryCategories' => $galleryCategories,
    'limit' => 8,
]);

// Upcoming Batches Table
echo View::component('batches-table', [
    'upcomingBatches' => $upcomingBatches,
]);

// Why Choose Us Section
echo View::component('why-choose-us');

// Public Enquiry Section
echo View::component('enquiry-form', [
    'courses' => $courses,
]);
