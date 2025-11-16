<?php
namespace App\Controllers\Admin;

use App\Repositories\BookingRepository;
use App\Repositories\EmailTemplateRepository;
use App\Services\EmailService;
use App\Services\PdfService;
use App\Utils\Auth;

class AdminController
{
    protected $bookingRepository;
    protected $emailService;
    protected $pdfService;
    protected $templateRepository;

    public function __construct(
        EmailService $emailService,
        BookingRepository $bookingRepository,
        PdfService $pdfService,
        EmailTemplateRepository $templateRepository
    )
    {
        // Require authentication
        Auth::requireLogin();

        $this->emailService = $emailService;
        $this->bookingRepository = $bookingRepository;
        $this->pdfService = $pdfService;
        $this->templateRepository = $templateRepository;
    }

    public function dashboard(): void
    {
        $bookings = $this->bookingRepository->findAll();
        $pending = array_filter($bookings, fn($b) => $b['status'] === 'pending');

        include __DIR__ . '/../../views/admin/dashboard.php';
    }

    public function listBookings(): void
    {
        $status = $_GET['status'] ?? 'pending';
        $bookings = $this->bookingRepository->findAll();

        if ($status !== 'all') {
            $bookings = array_filter($bookings, fn($b) => $b['status'] === $status);
        }

        include __DIR__ . '/../../views/admin/bookings.php';
    }

    // public function confirmBooking(): void
    // {
    //     $id = $_POST['booking_id'] ?? null;

    //     if (!$id) {
    //         http_response_code(400);
    //         echo "Missing booking_id";
    //         return;
    //     }

    //     $booking = $this->bookingRepository->findById((int)$id);
    //     if (!$booking) {
    //         http_response_code(404);
    //         echo "Booking not found";
    //         return;
    //     }

    //     $ok = $this->bookingRepository->updateStatus((int)$id, 'confirmed');
    //     if (!$ok) {
    //         http_response_code(500);
    //         echo "Failed to confirm booking";
    //         return;
    //     }

    //     $subject = "Your Booking is Confirmed - {$booking['name']}";
    //     $body = "Hi {$booking['name']},\n\n"
    //           . "Your booking for {$booking['date']} at {$booking['time']} has been confirmed.\n\n"
    //           . "Please find your appointment details attached.\n\n"
    //           . "Best regards,\nbookpy Team";

    //     $this->emailService->send($booking['email'], $subject, $body);

    //     header('Location: /admin/bookings?status=confirmed&message=Booking+confirmed');
    //     exit;
    // }

    public function confirmBooking()
    {
        $bookingId = $_POST['booking_id'] ?? null;
        if (!$bookingId) {
            // Handle error: no booking ID
            header('Location: /admin/bookings?error=Missing ID');
            return;
        }

        $booking = $this->bookingRepository->findById((int)$bookingId);

        if (!$booking) {
            // Handle error: booking not found
            header('Location: /admin/bookings?error=Not Found');
            return;
        }

        // 1. Update booking status in the database
        $this->bookingRepository->updateStatus((int)$bookingId, 'confirmed');

        // 2. Generate the PDF
        $pdfContent = $this->pdfService->generateFromView(
            PROJECT_ROOT . '/src/views/pdf/booking_confirmation.php',
            ['booking' => $booking]
        );

        // 3. Fetch and render the email template
        $template = $this->templateRepository->findByName('booking_confirmation');
        if (!$template) {
            // Fallback or error handling if template is not found
            error_log("Critical: 'booking_confirmation' email template not found in database.");
            // You might want to redirect with an error message
            header('Location: /admin/bookings?error=Template not found');
            return;
        }

        $appUrl = $_ENV['APP_URL'] ?? 'http://localhost:8000';
        $cancellationLink = $appUrl . '/booking/cancel/' . $booking['cancellation_token'];

        // Simple placeholder replacement
        $placeholders = [
            '{{name}}' => htmlspecialchars($booking['name']),
            '{{email}}' => htmlspecialchars($booking['email']),
            '{{date}}' => htmlspecialchars($booking['date']),
            '{{time}}' => htmlspecialchars($booking['time']),
            '{{cancellation_link}}' => $cancellationLink,
        ];
        $emailSubject = str_replace(array_keys($placeholders), array_values($placeholders), $template['subject']);
        $emailBody = nl2br(str_replace(array_keys($placeholders), array_values($placeholders), $template['body']));


        // 4. Prepare the email attachment
        $attachment = [
            'content' => $pdfContent,
            'filename' => 'booking-confirmation-' . $bookingId . '.pdf',
            'type' => 'application/pdf'
        ];

        // 5. Send the confirmation email with the attachment
        $this->emailService->send(
            $booking['email'],
            $emailSubject,
            $emailBody,
            [$attachment] // Pass the attachment here
        );

        // ensure session started
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Redirect back with a success message
        // Use a different session key to avoid conflicts with public pages
        $_SESSION['admin_success'] = "Booking #{$bookingId} confirmed and notification sent.";
        header('Location: /admin/bookings');
    }
}