<?php
namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFService
{
    protected $dompdf;

    public function __construct()
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $this->dompdf = new Dompdf($options);
    }

    public function generateBookingConfirmation(array $data): string
    {
        // Load HTML content
        $html = $this->generateHTML($data);
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        // Output the generated PDF to a string
        return $this->dompdf->output();
    }

    protected function generateHTML(array $data): string
    {
        // Create HTML content for the PDF
        return '
        <h1>Booking Confirmation</h1>
        <p>Thank you for your booking, ' . htmlspecialchars($data['name']) . '!</p>
        <p>Your appointment is scheduled for ' . htmlspecialchars($data['date']) . ' at ' . htmlspecialchars($data['time']) . '.</p>
        <p>Details:</p>
        <ul>
            <li>Name: ' . htmlspecialchars($data['name']) . '</li>
            <li>Email: ' . htmlspecialchars($data['email']) . '</li>
            <li>Phone: ' . htmlspecialchars($data['phone']) . '</li>
            <li>Notes: ' . htmlspecialchars($data['notes']) . '</li>
        </ul>
        <p>We look forward to seeing you!</p>
        ';
    }
}