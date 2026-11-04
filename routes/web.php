<?php

use App\Core\Router;
use App\Middleware\CsrfMiddleware;
use App\Controllers\Public\HomeController;
use App\Controllers\Public\AboutController;
use App\Controllers\Public\CourseController;
use App\Controllers\Public\PlacementController;
use App\Controllers\Public\SuccessStoryController;
use App\Controllers\Public\GalleryController;
use App\Controllers\Public\ContactController;

/** @var Router $router */

$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [AboutController::class, 'index']);
$router->get('/courses', [CourseController::class, 'index']);
$router->get('/courses/{id}', [CourseController::class, 'show']);
$router->get('/placements', [PlacementController::class, 'index']);
$router->get('/success-stories', [SuccessStoryController::class, 'index']);
$router->get('/gallery', [GalleryController::class, 'index']);
$router->get('/contact', [ContactController::class, 'index']);

// Enquiry submissions
$router->post('/contact', [ContactController::class, 'submit'], [CsrfMiddleware::class]);
$router->post('/enquiries', [ContactController::class, 'submit'], [CsrfMiddleware::class]);
