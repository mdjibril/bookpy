<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    private Dompdf $dompdf;

    public function __construct()
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // Allows loading images, etc.

        $this->dompdf = new Dompdf($options);
    }

    /**
     * Generates a PDF from a view file and returns the raw PDF string.
     *
     * @param string $viewPath Path to the view file.
     * @param array $data Data to be passed to the view.
     * @return string The raw PDF content.
     */
    public function generateFromView(string $viewPath, array $data): string
    {
        // Start output buffering to capture the HTML from the view
        ob_start();
        // Extract data so it's available as variables in the view
        extract($data);
        require $viewPath;
        $html = ob_get_clean();

        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        return $this->dompdf->output();
    }
}