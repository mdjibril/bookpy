<?php
// ...existing code...
namespace App\Controllers;

class PublicController
{
    public function __construct()
    {
        // No dependencies needed for now
    }

    // Home / public calendar page
    public function index(): void
    {
        include __DIR__ . '/../../public/home.php';
    }
}