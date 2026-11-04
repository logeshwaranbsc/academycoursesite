<?php

use App\Core\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\HeroBannerController;
use App\Controllers\Admin\CourseController;
use App\Controllers\Admin\BatchController;
use App\Controllers\Admin\PlacementController;
use App\Controllers\Admin\SuccessStoryController;
use App\Controllers\Admin\TestimonialController;
use App\Controllers\Admin\CultureVideoController;
use App\Controllers\Admin\GalleryController;
use App\Controllers\Admin\EnquiryController;
use App\Controllers\Admin\SettingsController;
use App\Core\Response;

/** @var Router $router */

// Admin Authentication (Public)
$router->get('/admin/login', [AuthController::class, 'showLogin']);
$router->post('/admin/login', [AuthController::class, 'login'], [CsrfMiddleware::class]);
$router->get('/admin/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);
$router->post('/admin/logout', [AuthController::class, 'logout'], [AuthMiddleware::class, CsrfMiddleware::class]);

// Admin Protected Dashboard & CRUD Routes
$router->group(['prefix' => '/admin', 'middleware' => [AuthMiddleware::class]], function (Router $router) {
    // Dashboard
    $router->get('/', function () {
        return Response::redirect('/admin/dashboard');
    });
    $router->get('/dashboard', [DashboardController::class, 'index']);

    // Hero Banners
    $router->get('/banners', [HeroBannerController::class, 'index']);
    $router->get('/hero-banners', [HeroBannerController::class, 'index']);
    $router->post('/banners', [HeroBannerController::class, 'store'], [CsrfMiddleware::class]);
    $router->post('/hero-banners', [HeroBannerController::class, 'store'], [CsrfMiddleware::class]);
    $router->post('/banners/{id}', [HeroBannerController::class, 'update'], [CsrfMiddleware::class]);
    $router->post('/hero-banners/{id}', [HeroBannerController::class, 'update'], [CsrfMiddleware::class]);
    $router->post('/banners/{id}/delete', [HeroBannerController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->post('/hero-banners/{id}/delete', [HeroBannerController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->delete('/banners/{id}', [HeroBannerController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->delete('/hero-banners/{id}', [HeroBannerController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->post('/banners/{id}/toggle', [HeroBannerController::class, 'toggleStatus'], [CsrfMiddleware::class]);
    $router->post('/hero-banners/{id}/toggle', [HeroBannerController::class, 'toggleStatus'], [CsrfMiddleware::class]);
    $router->patch('/banners/{id}/toggle', [HeroBannerController::class, 'toggleStatus'], [CsrfMiddleware::class]);
    $router->patch('/hero-banners/{id}/toggle', [HeroBannerController::class, 'toggleStatus'], [CsrfMiddleware::class]);

    // Courses
    $router->get('/courses', [CourseController::class, 'index']);
    $router->post('/courses', [CourseController::class, 'store'], [CsrfMiddleware::class]);
    $router->post('/courses/{id}', [CourseController::class, 'update'], [CsrfMiddleware::class]);
    $router->put('/courses/{id}', [CourseController::class, 'update'], [CsrfMiddleware::class]);
    $router->post('/courses/{id}/delete', [CourseController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->delete('/courses/{id}', [CourseController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->post('/courses/{id}/toggle', [CourseController::class, 'toggleStatus'], [CsrfMiddleware::class]);
    $router->patch('/courses/{id}/toggle', [CourseController::class, 'toggleStatus'], [CsrfMiddleware::class]);

    // Batches
    $router->get('/batches', [BatchController::class, 'index']);
    $router->post('/batches', [BatchController::class, 'store'], [CsrfMiddleware::class]);
    $router->post('/batches/{id}', [BatchController::class, 'update'], [CsrfMiddleware::class]);
    $router->put('/batches/{id}', [BatchController::class, 'update'], [CsrfMiddleware::class]);
    $router->post('/batches/{id}/delete', [BatchController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->delete('/batches/{id}', [BatchController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->post('/batches/{id}/toggle', [BatchController::class, 'toggleStatus'], [CsrfMiddleware::class]);
    $router->patch('/batches/{id}/toggle', [BatchController::class, 'toggleStatus'], [CsrfMiddleware::class]);

    // Placements
    $router->get('/placements', [PlacementController::class, 'index']);
    $router->post('/placements', [PlacementController::class, 'store'], [CsrfMiddleware::class]);
    $router->post('/placements/{id}', [PlacementController::class, 'update'], [CsrfMiddleware::class]);
    $router->put('/placements/{id}', [PlacementController::class, 'update'], [CsrfMiddleware::class]);
    $router->post('/placements/{id}/delete', [PlacementController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->delete('/placements/{id}', [PlacementController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->post('/placements/{id}/toggle', [PlacementController::class, 'toggleStatus'], [CsrfMiddleware::class]);
    $router->patch('/placements/{id}/toggle', [PlacementController::class, 'toggleStatus'], [CsrfMiddleware::class]);

    // Success Stories
    $router->get('/success-stories', [SuccessStoryController::class, 'index']);
    $router->post('/success-stories', [SuccessStoryController::class, 'store'], [CsrfMiddleware::class]);
    $router->post('/success-stories/{id}', [SuccessStoryController::class, 'update'], [CsrfMiddleware::class]);
    $router->put('/success-stories/{id}', [SuccessStoryController::class, 'update'], [CsrfMiddleware::class]);
    $router->post('/success-stories/{id}/delete', [SuccessStoryController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->delete('/success-stories/{id}', [SuccessStoryController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->post('/success-stories/{id}/toggle', [SuccessStoryController::class, 'toggleStatus'], [CsrfMiddleware::class]);
    $router->patch('/success-stories/{id}/toggle', [SuccessStoryController::class, 'toggleStatus'], [CsrfMiddleware::class]);

    // Testimonials
    $router->get('/testimonials', [TestimonialController::class, 'index']);
    $router->post('/testimonials', [TestimonialController::class, 'store'], [CsrfMiddleware::class]);
    $router->post('/testimonials/{id}', [TestimonialController::class, 'update'], [CsrfMiddleware::class]);
    $router->put('/testimonials/{id}', [TestimonialController::class, 'update'], [CsrfMiddleware::class]);
    $router->post('/testimonials/{id}/delete', [TestimonialController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->delete('/testimonials/{id}', [TestimonialController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->post('/testimonials/{id}/toggle', [TestimonialController::class, 'toggleStatus'], [CsrfMiddleware::class]);
    $router->patch('/testimonials/{id}/toggle', [TestimonialController::class, 'toggleStatus'], [CsrfMiddleware::class]);

    // Culture Videos
    $router->get('/culture-videos', [CultureVideoController::class, 'index']);
    $router->post('/culture-videos', [CultureVideoController::class, 'store'], [CsrfMiddleware::class]);
    $router->post('/culture-videos/{id}', [CultureVideoController::class, 'update'], [CsrfMiddleware::class]);
    $router->put('/culture-videos/{id}', [CultureVideoController::class, 'update'], [CsrfMiddleware::class]);
    $router->post('/culture-videos/{id}/delete', [CultureVideoController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->delete('/culture-videos/{id}', [CultureVideoController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->post('/culture-videos/{id}/toggle', [CultureVideoController::class, 'toggleStatus'], [CsrfMiddleware::class]);
    $router->patch('/culture-videos/{id}/toggle', [CultureVideoController::class, 'toggleStatus'], [CsrfMiddleware::class]);

    // Gallery
    $router->get('/gallery', [GalleryController::class, 'index']);
    $router->post('/gallery', [GalleryController::class, 'store'], [CsrfMiddleware::class]);
    $router->post('/gallery/{id}', [GalleryController::class, 'update'], [CsrfMiddleware::class]);
    $router->put('/gallery/{id}', [GalleryController::class, 'update'], [CsrfMiddleware::class]);
    $router->post('/gallery/{id}/delete', [GalleryController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->delete('/gallery/{id}', [GalleryController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->post('/gallery/{id}/toggle', [GalleryController::class, 'toggleStatus'], [CsrfMiddleware::class]);
    $router->patch('/gallery/{id}/toggle', [GalleryController::class, 'toggleStatus'], [CsrfMiddleware::class]);

    // Enquiries
    $router->get('/enquiries', [EnquiryController::class, 'index']);
    $router->post('/enquiries/{id}/status', [EnquiryController::class, 'updateStatus'], [CsrfMiddleware::class]);
    $router->patch('/enquiries/{id}/status', [EnquiryController::class, 'updateStatus'], [CsrfMiddleware::class]);
    $router->post('/enquiries/{id}/delete', [EnquiryController::class, 'destroy'], [CsrfMiddleware::class]);
    $router->delete('/enquiries/{id}', [EnquiryController::class, 'destroy'], [CsrfMiddleware::class]);

    // Settings
    $router->get('/settings', [SettingsController::class, 'index']);
    $router->post('/settings', [SettingsController::class, 'update'], [CsrfMiddleware::class]);
});
