<?php
namespace App\Controllers;

use App\Repositories\EmailTemplateRepository;
use App\Services\EmailService;
use App\Utils\CSRF;
use App\Repositories\BookingRepository;
use App\Utils\Validator;

class BookingController
{
    protected $emailService;
    protected $bookingRepository;
    protected $templateRepository;

    public function __construct(EmailService $emailService, EmailTemplateRepository $templateRepository, BookingRepository $bookingRepository)
    {
        $this->emailService = $emailService;
        $this->templateRepository = $templateRepository;
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * Handles the creation of a new booking from the public form.
     */
    public function createBooking(): void
    {
        // ensure session started
        if (session_status() === PHP_SESSION_NONE) session_start();

        $post = $_POST;

        // CSRF check
        if (!CSRF::validate($post['csrf_token'] ?? null)) {
            $_SESSION['booking_old'] = $post;
            $_SESSION['booking_error'] = 'Invalid session token. Please reload the page and try again.';
            header('Location: /booking');
            exit;
        }

        // validate inputs
        $errors = Validator::validateBooking($post);
        if (!empty($errors)) {
            $_SESSION['booking_old'] = $post;
            $_SESSION['booking_error'] = implode(' ', array_values($errors));
            header('Location: /booking');
            exit;
        }

        // prepare clean data
        $data = [
            'name' => Validator::sanitizeString($post['name'] ?? ''),
            'email' => Validator::sanitizeString($post['email'] ?? ''),
            'phone' => Validator::sanitizeString($post['phone'] ?? ''),
            'date' => Validator::sanitizeString($post['date'] ?? ''),
            'time' => Validator::sanitizeString($post['time'] ?? ''),
            'notes' => Validator::sanitizeString($post['notes'] ?? ''),
            'status' => 'pending',
        ];

        if ($this->bookingRepository->create($data)) {
            // Send notification to admin
            $this->sendAdminNotification($data);

            // Send acknowledgement to user
            $this->sendUserAcknowledgementEmail($data);

            // Reset CSRF token and set success message
            CSRF::resetToken();
            $_SESSION['booking_success'] = 'Booking submitted. You will receive a confirmation email shortly.';
            header('Location: /booking');
        } else {
            $_SESSION['booking_old'] = $post;
            $_SESSION['booking_error'] = 'Failed to create booking. Please try again later.';
            header('Location: /booking');
        }
        exit;
    }

    private function sendAdminNotification(array $bookingData): void
    {
        $adminEmail = $_ENV['ADMIN_EMAIL'] ?? null;
        if (!$adminEmail) {
            error_log("Admin notification not sent: ADMIN_EMAIL environment variable is not set.");
            return;
        }

        $template = $this->templateRepository->findByName('new_booking_admin_notification');
        if (!$template) {
            error_log("Admin notification not sent: 'new_booking_admin_notification' template not found.");
            return;
        }

        // Replace placeholders in the template
        $placeholders = [
            '{{name}}' => htmlspecialchars($bookingData['name']),
            '{{email}}' => htmlspecialchars($bookingData['email']),
            '{{phone}}' => htmlspecialchars($bookingData['phone']),
            '{{date}}' => htmlspecialchars($bookingData['date']),
            '{{time}}' => htmlspecialchars($bookingData['time']),
            '{{notes}}' => htmlspecialchars($bookingData['notes']),
        ];

        $subject = str_replace(array_keys($placeholders), array_values($placeholders), $template['subject']);
        $body = nl2br(str_replace(array_keys($placeholders), array_values($placeholders), $template['body']));

        $this->emailService->send($adminEmail, $subject, $body);
    }

    private function sendUserAcknowledgementEmail(array $bookingData): void
    {
        $template = $this->templateRepository->findByName('booking_received_user');
        if (!$template) {
            error_log("User acknowledgement email not sent: 'booking_received_user' template not found.");
            return;
        }

        // Replace placeholders in the template
        $placeholders = [
            '{{name}}' => htmlspecialchars($bookingData['name']),
            '{{date}}' => htmlspecialchars($bookingData['date']),
            '{{time}}' => htmlspecialchars($bookingData['time']),
        ];

        $subject = str_replace(array_keys($placeholders), array_values($placeholders), $template['subject']);
        $body = nl2br(str_replace(array_keys($placeholders), array_values($placeholders), $template['body']));

        $this->emailService->send(
            $bookingData['email'],
            $subject,
            $body
        );
    }

    public function showBookingForm(): void
    {
        // ensure session started
        if (session_status() === PHP_SESSION_NONE) session_start();

        // prepare defaults and tokens for view
        $old = $_SESSION['booking_old'] ?? [];
        $successMessage = $_SESSION['booking_success'] ?? null;
        $errorMessage = $_SESSION['booking_error'] ?? null;
        unset($_SESSION['booking_old'], $_SESSION['booking_success'], $_SESSION['booking_error']);

        $csrfField = \App\Utils\CSRF::inputField();

        // make variables available to the view
        include __DIR__ . '/../../public/booking.php';
    }

    public function showCancellationPage(string $token): void
    {
        // ensure session started
        if (session_status() === PHP_SESSION_NONE) session_start();

        $booking = $this->bookingRepository->findByCancellationToken($token);
        $csrfField = CSRF::inputField();
        include PROJECT_ROOT . '/src/views/public/cancel_booking.php';
    }

    public function processCancellation(string $token): void
    {
        // ensure session started
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!CSRF::validate($_POST['csrf_token'] ?? null)) {
            $_SESSION['cancellation_error'] = 'Invalid session token. Please try again.';
            header('Location: /booking/cancel/' . $token);
            exit;
        }

        $booking = $this->bookingRepository->findByCancellationToken($token);

        if (!$booking) {
            $_SESSION['cancellation_error'] = 'This booking could not be found. It may have already been cancelled.';
            header('Location: /'); // Redirect home
            exit;
        }

        if ($booking['status'] === 'cancelled') {
            $_SESSION['cancellation_success'] = 'This booking has already been cancelled.';
            header('Location: /booking/cancel/' . $token);
            exit;
        }

        // Update status to 'cancelled'
        $this->bookingRepository->updateStatus($booking['id'], 'cancelled');

        // Send cancellation confirmation email
        $this->sendCancellationConfirmationEmail($booking);

        $_SESSION['cancellation_success'] = 'Your booking has been successfully cancelled.';
        header('Location: /booking/cancel/' . $token);
        exit;
    }

    private function sendCancellationConfirmationEmail(array $booking): void
    {
        $template = $this->templateRepository->findByName('cancellation_confirmed');
        if (!$template) {
            error_log("Cancellation email not sent: 'cancellation_confirmation' template not found.");
            return;
        }

        $placeholders = [
            '{{name}}' => htmlspecialchars($booking['name']),
            '{{date}}' => htmlspecialchars($booking['date']),
            '{{time}}' => htmlspecialchars($booking['time']),
        ];

        $subject = str_replace(array_keys($placeholders), array_values($placeholders), $template['subject']);
        $body = nl2br(str_replace(array_keys($placeholders), array_values($placeholders), $template['body']));

        $this->emailService->send(
            $booking['email'],
            $subject,
            $body
        );
    }
}