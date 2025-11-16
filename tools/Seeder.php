<?php
namespace Tools;

class Seeder
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function seedBookings(): void
    {
        $bookings = [
            ['name' => 'Alice Johnson', 'email' => 'alice@example.com', 'phone' => '555-0101', 'date' => '2025-11-13', 'time' => '09:00:00', 'status' => 'pending', 'notes' => 'First appointment'],
            ['name' => 'Bob Smith', 'email' => 'bob@example.com', 'phone' => '555-0102', 'date' => '2025-11-13', 'time' => '10:00:00', 'status' => 'confirmed', 'notes' => 'Confirmed booking'],
            ['name' => 'Carol White', 'email' => 'carol@example.com', 'phone' => '555-0103', 'date' => '2025-11-14', 'time' => '14:00:00', 'status' => 'pending', 'notes' => 'Afternoon slot'],
            ['name' => 'David Brown', 'email' => 'david@example.com', 'phone' => '555-0104', 'date' => '2025-11-14', 'time' => '15:30:00', 'status' => 'confirmed', 'notes' => 'Follow-up'],
            ['name' => 'Eve Davis', 'email' => 'eve@example.com', 'phone' => '555-0105', 'date' => '2025-11-15', 'time' => '11:00:00', 'status' => 'cancelled', 'notes' => 'Cancelled by user'],
            ['name' => 'Frank Miller', 'email' => 'frank@example.com', 'phone' => '555-0106', 'date' => '2025-11-15', 'time' => '13:00:00', 'status' => 'pending', 'notes' => 'New booking'],
            ['name' => 'Grace Lee', 'email' => 'grace@example.com', 'phone' => '555-0107', 'date' => '2025-11-16', 'time' => '09:30:00', 'status' => 'confirmed', 'notes' => 'Confirmed'],
            ['name' => 'Henry Wilson', 'email' => 'henry@example.com', 'phone' => '555-0108', 'date' => '2025-11-16', 'time' => '16:00:00', 'status' => 'pending', 'notes' => 'Evening slot'],
        ];

        $stmt = $this->pdo->prepare(
            "INSERT INTO bookings (name, email, phone, date, time, status, notes, created_at) 
             VALUES (:name, :email, :phone, :date, :time, :status, :notes, NOW())"
        );

        foreach ($bookings as $booking) {
            $ok = $stmt->execute($booking);
            if ($ok) {
                echo "✓ Inserted: {$booking['name']}\n";
            } else {
                echo "✗ Failed: {$booking['name']} - " . json_encode($stmt->errorInfo()) . "\n";
            }
        }

        echo "\nSeed complete.\n";
    }

    public function clearBookings(): void
    {
        $this->pdo->exec("TRUNCATE TABLE bookings");
        echo "✓ Bookings table cleared.\n";
    }
}