<?php

use App\Utils\Router;
use App\Controllers\PublicController;
use App\Controllers\BookingController;
use App\Controllers\AuthController;
use App\Controllers\Admin\AdminController;
use App\Controllers\Admin\EmailTemplateController;


$router = new Router();

// Public Routes (use class::class array handlers to be explicit)
$router->get('/', [PublicController::class, 'index']);
$router->get('/booking', [PublicController::class, 'showBookingForm']);
$router->post('/booking', [BookingController::class, 'createBooking']);

// Auth Routes (no login required)
$router->get('/admin/login', [AuthController::class, 'showLogin']);
$router->post('/admin/login', [AuthController::class, 'handleLogin']);
$router->get('/admin/logout', [AuthController::class, 'logout']);

// Admin Routes (login required via Auth::requireLogin() in AdminController constructor)
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/bookings', [AdminController::class, 'listBookings']);
$router->post('/admin/bookings/confirm', [AdminController::class, 'confirmBooking']);



// Email Templates Routes
$router->get('/admin/email-templates', [EmailTemplateController::class, 'index']);
$router->get('/admin/email-templates/create', [EmailTemplateController::class, 'showCreate']);
$router->post('/admin/email-templates/create', [EmailTemplateController::class, 'create']);
$router->get('/admin/email-templates/edit/{id}', [EmailTemplateController::class, 'showEdit']);
$router->post('/admin/email-templates/update/{id}', [EmailTemplateController::class, 'update']);
$router->post('/admin/email-templates/delete/{id}', [EmailTemplateController::class, 'delete']);
$router->put('/admin/email-templates/{id}', [EmailTemplateController::class, 'update']);
$router->delete('/admin/email-templates/{id}', [EmailTemplateController::class, 'delete']);



// Dispatch
$router->dispatch();


