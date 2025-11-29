-- Create bookings table for bookpy

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    date DATE NOT NULL,
    time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create email_templates table for editable templates
CREATE TABLE IF NOT EXISTS email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    subject VARCHAR(255) NOT NULL,
    body LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default email templates
INSERT INTO email_templates (name, subject, body) VALUES
(
    'booking_acknowledgement',
    'Booking Received - {{name}}',
    'Hi {{name}},\n\nThank you for your booking request for {{date}} at {{time}}.\n\nWe have received your submission and will confirm shortly.\n\nBest regards,\nbookpy Team'
),
(
    'booking_confirmation',
    'Your Booking is Confirmed - {{name}}',
    'Hi {{name}},\n\nGreat news! Your booking for {{date}} at {{time}} has been confirmed.\n\nPlease find your appointment details attached.\n\nIf you have any questions, feel free to contact us.\n\nBest regards,\nbookpy Team'
),
(
    'booking_cancelled',
    'Your Booking Has Been Cancelled',
    'Hi {{name}},\n\nYour booking for {{date}} at {{time}} has been cancelled.\n\nIf you would like to reschedule, please visit our booking page.\n\nBest regards,\nbookpy Team'
);