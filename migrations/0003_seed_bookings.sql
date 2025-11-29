INSERT INTO bookings (name, email, phone, date, time, status, notes, created_at) VALUES
('Alice Johnson', 'alice@example.com', '555-0101', '2025-11-13', '09:00:00', 'pending', 'First appointment', NOW()),
('Bob Smith', 'bob@example.com', '555-0102', '2025-11-13', '10:00:00', 'confirmed', 'Confirmed booking', NOW()),
('Carol White', 'carol@example.com', '555-0103', '2025-11-14', '14:00:00', 'pending', 'Afternoon slot', NOW()),
('David Brown', 'david@example.com', '555-0104', '2025-11-14', '15:30:00', 'confirmed', 'Follow-up', NOW()),
('Eve Davis', 'eve@example.com', '555-0105', '2025-11-15', '11:00:00', 'cancelled', 'Cancelled by user', NOW()),
('Frank Miller', 'frank@example.com', '555-0106', '2025-11-15', '13:00:00', 'pending', 'New booking', NOW()),
('Grace Lee', 'grace@example.com', '555-0107', '2025-11-16', '09:30:00', 'confirmed', 'Confirmed', NOW()),
('Henry Wilson', 'henry@example.com', '555-0108', '2025-11-16', '16:00:00', 'pending', 'Evening slot', NOW());