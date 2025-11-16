<?php

use App\Utils\Router;
use App\Controllers\PublicController;
use App\Utils\Database;
use App\Controllers\BookingController;
use App\Controllers\AuthController;
use App\Controllers\AvailabilityController;
use App\Controllers\Admin\AdminController;
use App\Controllers\Admin\EmailTemplateController;
use App\Repositories\BookingRepository;
use App\Repositories\EmailTemplateRepository;
use App\Services\EmailService;
use App\Services\PdfService;


// Get the single database connection instance
$pdo = Database::getInstance();

// Instantiate services and repositories to be shared
$emailService = new EmailService();
$pdfService = new PdfService();
$bookingRepository = new BookingRepository($pdo);
$templateRepository = new EmailTemplateRepository($pdo);

// Create a simple container for our services
$container = [
    EmailService::class => $emailService,
    PdfService::class => $pdfService,
    BookingRepository::class => $bookingRepository,
    EmailTemplateRepository::class => $templateRepository,
];

$router = new Router($container);
// Public Routes (use class::class array handlers to be explicit)
$router->get('/', [PublicController::class, 'index']);
$router->get('/booking', [BookingController::class, 'showBookingForm']);
$router->post('/booking', [BookingController::class, 'createBooking']);
$router->get('/booking/cancel/{token}', [BookingController::class, 'showCancellationPage']);
$router->post('/booking/cancel/{token}', [BookingController::class, 'processCancellation']);

// API Routes
$router->get('/api/availability', [AvailabilityController::class, 'getAvailableSlots']);

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
$router->get('/admin/email-templates/history/{id}', [EmailTemplateController::class, 'showHistory']);
$router->post('/admin/email-templates/restore/{id}', [EmailTemplateController::class, 'restore']);

// Dispatch
$router->dispatch();
