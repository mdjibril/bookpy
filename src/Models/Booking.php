<?php

namespace App\Models;

class Booking
{
    private $id;
    private $userId;
    private $bookingDate;
    private $status;

    public function __construct($userId, $bookingDate)
    {
        $this->userId = $userId;
        $this->bookingDate = $bookingDate;
        $this->status = 'pending'; // Default status
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getBookingDate()
    {
        return $this->bookingDate;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }
}