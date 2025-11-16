# Bookpy Project

Bookpy is a PHP web application designed for managing bookings. It features a public booking page, an admin panel for managing bookings, and an email notification system to keep users informed.

## Features

- **Public Booking Page**: Users can view available bookings and submit their requests through a user-friendly interface.
- **Admin Panel**: Administrators can manage bookings, view details, and confirm or cancel requests.
- **Email Notifications**: Users receive email notifications for booking confirmations and updates.

## Project Structure

```
bookpy
├── public
│   ├── index.php
│   ├── booking.php
│   └── assets
│       ├── css
│       │   └── app.css
│       └── js
│           └── app.js
├── src
│   ├── Controllers
│   │   ├── PublicController.php
│   │   ├── BookingController.php
│   │   └── Admin
│   │       └── AdminController.php
│   ├── Models
│   │   ├── Booking.php
│   │   └── User.php
│   ├── Services
│   │   ├── EmailService.php
│   │   └── NotificationService.php
│   ├── Repositories
│   │   └── BookingRepository.php
│   └── Routes
│       └── web.php
├── templates
│   ├── public
│   │   └── booking.twig
│   └── admin
│       ├── dashboard.twig
│       └── bookings.twig
├── config
│   ├── config.php
│   └── services.php
├── migrations
│   └── 0001_create_bookings_table.sql
├── tests
│   ├── Controllers
│   └── Services
├── composer.json
├── phpunit.xml
├── .env.example
└── README.md
```

## Installation

1. Clone the repository:
   ```
   git clone <repository-url>
   ```
2. Navigate to the project directory:
   ```
   cd bookpy
   ```
3. Install dependencies using Composer:
   ```
   composer install
   ```
4. Configure your environment variables by copying `.env.example` to `.env` and updating the values accordingly.
5. Run the database migrations:
   ```
   php artisan migrate
   ```

## Usage

- Access the public booking page at `http://yourdomain.com/public/booking.php`.
- Log in to the admin panel to manage bookings.

## Contributing

Contributions are welcome! Please submit a pull request or open an issue for any enhancements or bug fixes.

## License

This project is licensed under the MIT License. See the LICENSE file for details.