<?php

namespace App\Services;

class NotificationService
{
    protected $emailService;

    public function __construct($emailService)
    {
        $this->emailService = $emailService;
    }

    public function notifyNewBooking($bookingDetails)
    {
        // Logic to notify about a new booking
        $subject = "New Booking Confirmation";
        $message = "You have a new booking: " . json_encode($bookingDetails);
        $this->emailService->sendEmail($bookingDetails['user_email'], $subject, $message);
    }

    public function notifyBookingConfirmation($bookingDetails)
    {
        // Logic to notify about a booking confirmation
        $subject = "Booking Confirmation";
        $message = "Your booking has been confirmed: " . json_encode($bookingDetails);
        $this->emailService->sendEmail($bookingDetails['user_email'], $subject, $message);
    }
}