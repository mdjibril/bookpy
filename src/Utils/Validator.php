<?php
namespace App\Utils;

class Validator
{
    public static function sanitizeString(?string $v): string
    {
        return trim((string) $v);
    }

    public static function validateBooking(array $data): array
    {
        $errors = [];

        $name = self::sanitizeString($data['name'] ?? '');
        $email = self::sanitizeString($data['email'] ?? '');
        $phone = self::sanitizeString($data['phone'] ?? '');
        $date = self::sanitizeString($data['date'] ?? '');
        $time = self::sanitizeString($data['time'] ?? '');

        if ($name === '') {
            $errors['name'] = 'Name is required.';
        } elseif (mb_strlen($name) > 255) {
            $errors['name'] = 'Name is too long.';
        }

        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email is not valid.';
        }

        if ($phone !== '' && mb_strlen($phone) > 50) {
            $errors['phone'] = 'Phone is too long.';
        }

        // date YYYY-MM-DD
        if ($date === '') {
            $errors['date'] = 'Date is required.';
        } elseif (!self::isValidDate($date)) {
            $errors['date'] = 'Date format is invalid.';
        }

        // time HH:MM or HH:MM:SS
        if ($time === '') {
            $errors['time'] = 'Time is required.';
        } elseif (!self::isValidTime($time)) {
            $errors['time'] = 'Time format is invalid.';
        }

        return $errors;
    }

    public static function isValidDate(string $d): bool
    {
        $parts = explode('-', $d);
        if (count($parts) !== 3) return false;
        return checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0]);
    }

    public static function isValidTime(string $t): bool
    {
        // accept HH:MM or HH:MM:SS
        return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $t) === 1;
    }
}