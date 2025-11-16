<?php
// Simple CLI script to run seeders
require_once __DIR__ . '/../vendor/autoload.php';

// Load env (if dotenv is installed; optional for now)
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env as $k => $v) {
        putenv("{$k}={$v}");
    }
}

use Tools\Seeder;

$host = getenv('DB_HOST') ?: '127.0.0.1';
$name = getenv('DB_NAME') ?: 'bookpy';
$user = getenv('DB_USER') ?: 'bookpy';
$pass = getenv('DB_PASS') ?: '1234';

try {
    $pdo = new \PDO(
        "mysql:host={$host};dbname={$name};charset=utf8mb4",
        $user,
        $pass,
        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
    );
    echo "Connected to {$name}@{$host}\n\n";
} catch (\PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

$command = $argv[1] ?? 'seed';

$seeder = new Seeder($pdo);

if ($command === 'seed') {
    echo "Running seeder...\n";
    $seeder->seedBookings();
} elseif ($command === 'clear') {
    echo "Clearing bookings...\n";
    $seeder->clearBookings();
} else {
    echo "Usage: php seed.php [seed|clear]\n";
    exit(1);
}