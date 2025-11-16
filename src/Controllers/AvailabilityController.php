<?php

namespace App\Controllers;

use App\Repositories\BookingRepository;

class AvailabilityController
{
    private $bookingRepository;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    public function getAvailableSlots()
    {
        header('Content-Type: application/json');

        $date = $_GET['date'] ?? null;
        if (!$date || !\DateTime::createFromFormat('Y-m-d', $date)) {
            http_response_code(400);
            echo json_encode(['error' => 'A valid date in YYYY-MM-DD format is required.']);
            return;
        }

        // --- Define Business Logic ---
        $startTime = new \DateTime('09:00');
        $endTime = new \DateTime('17:00');
        $slotInterval = new \DateInterval('PT30M'); // 30-minute slots

        // 1. Get all potential slots for the day
        $allSlots = [];
        $current = clone $startTime;
        while ($current < $endTime) {
            $allSlots[] = $current->format('H:i');
            $current->add($slotInterval);
        }

        // 2. Get already booked slots for the selected date
        $bookedAppointments = $this->bookingRepository->findByDate($date);
        $bookedSlots = array_map(function ($booking) {
            // Assuming 'time' is stored as 'HH:MM:SS', we just need 'HH:MM'
            return substr($booking['time'], 0, 5);
        }, $bookedAppointments);

        // 3. Filter out the booked slots to find what's available
        $availableSlots = array_diff($allSlots, $bookedSlots);

        // Ensure the response is a simple array for easy JS consumption
        echo json_encode(array_values($availableSlots));
    }
}