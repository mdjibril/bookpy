<?php
namespace App\Repositories;

class BookingRepository
{
    protected ?\PDO $pdo;
    protected $columns = [];

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;

        if ($this->pdo) {
            try {
                $stmt = $this->pdo->query("DESCRIBE bookings");
                $cols = $stmt->fetchAll(\PDO::FETCH_COLUMN);
                $this->columns = $cols ?: [];
                error_log("BookingRepository columns: " . implode(',', $this->columns));
            } catch (\Exception $e) {
                error_log("BookingRepository describe error: " . $e->getMessage());
                $this->columns = [];
            }
        }
    }

    protected function hasColumn(string $name): bool
    {
        return in_array($name, $this->columns, true);
    }

    public function create(array $data): bool
    {
        if (!$this->pdo) {
            error_log("BookingRepository: PDO not available");
            return false;
        }

        // The PDO object is set to throw exceptions on error, so we can wrap this.
        // The controller will catch it and display the debug info.
        $token = bin2hex(random_bytes(32)); // Generate a secure token
        $stmt = $this->pdo->prepare(
            "INSERT INTO bookings (name, email, phone, date, time, notes, status, cancellation_token, created_at, updated_at) 
             VALUES (:name, :email, :phone, :date, :time, :notes, :status, :cancellation_token, NOW(), NOW())"
        );
        return $stmt->execute([
            ':name'   => $data['name'] ?? null,
            ':email'  => $data['email'] ?? null,
            ':phone'  => $data['phone'] ?? null,
            ':date'   => $data['date'] ?? null,
            ':time'   => $data['time'] ?? null,
            ':notes'  => $data['notes'] ?? null,
            ':status' => $data['status'] ?? 'pending',
            ':cancellation_token' => $token,
        ]);
    }

    public function findAll(): array
    {
        if (!$this->pdo) {
            error_log("BookingRepository findAll: PDO not available");
            return [];
        }

        try {
            // Choose ordering column
            $orderBy = $this->hasColumn('date') ? 'date DESC' : ($this->hasColumn('booking_date') ? 'booking_date DESC' : 'id DESC');
            error_log("BookingRepository findAll: executing SELECT order={$orderBy}");
            $stmt = $this->pdo->query("SELECT * FROM bookings ORDER BY {$orderBy}");
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Normalize rows to canonical shape for views
            $normalized = [];
            foreach ($rows as $r) {
                $item = [
                    'id' => $r['id'] ?? null,
                    'name' => $r['name'] ?? null,
                    'email' => $r['email'] ?? null,
                    'phone' => $r['phone'] ?? null,
                    'date' => null,
                    'time' => null,
                    'status' => $r['status'] ?? ($r['status'] ?? 'pending'),
                    'cancellation_token' => $r['cancellation_token'] ?? null,
                    'created_at' => $r['created_at'] ?? ($r['created_at'] ?? null),
                ];

                if (isset($r['date']) && isset($r['time'])) {
                    $item['date'] = $r['date'];
                    $item['time'] = $r['time'];
                } elseif (isset($r['booking_date'])) {
                    // split booking_date into date/time
                    $bd = $r['booking_date'];
                    $item['date'] = substr($bd, 0, 10);
                    $item['time'] = substr($bd, 11, 8);
                } elseif (isset($r['booking_date_time'])) {
                    $bd = $r['booking_date_time'];
                    $item['date'] = substr($bd, 0, 10);
                    $item['time'] = substr($bd, 11, 8);
                }

                // If name/email missing but user_id present, attempt to resolve user (best-effort)
                if ((empty($item['name']) || empty($item['email'])) && isset($r['user_id']) && $r['user_id']) {
                    try {
                        $uid = (int)$r['user_id'];
                        $uStmt = $this->pdo->prepare("SELECT id, name, email FROM users WHERE id = :id LIMIT 1");
                        $uStmt->execute([':id' => $uid]);
                        $u = $uStmt->fetch(\PDO::FETCH_ASSOC);
                        if ($u) {
                            if (empty($item['name'])) $item['name'] = $u['name'] ?? $u['id'];
                            if (empty($item['email'])) $item['email'] = $u['email'] ?? null;
                        }
                    } catch (\Exception $e) {
                        // ignore user lookup errors
                    }
                }

                $normalized[] = $item;
            }

            return $normalized;
        } catch (\Exception $e) {
            error_log("BookingRepository findAll error: " . $e->getMessage());
            return [];
        }
    }

    public function findById(int $id): ?array
    {
        if (!$this->pdo) {
            error_log("BookingRepository findById: PDO not available");
            return null;
        }

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM bookings WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $r = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$r) return null;

            $rows = $this->findAll(); // reuse normalization - but optimize small project: just map this row
            // map single row
            $item = [
                'id' => $r['id'] ?? null,
                'name' => $r['name'] ?? null,
                'email' => $r['email'] ?? null,
                'phone' => $r['phone'] ?? null,
                'date' => null,
                'time' => null,
                'status' => $r['status'] ?? 'pending',
                'cancellation_token' => $r['cancellation_token'] ?? null,
                'created_at' => $r['created_at'] ?? null,
            ];

            if (isset($r['date']) && isset($r['time'])) {
                $item['date'] = $r['date'];
                $item['time'] = $r['time'];
            } elseif (isset($r['booking_date'])) {
                $bd = $r['booking_date'];
                $item['date'] = substr($bd, 0, 10);
                $item['time'] = substr($bd, 11, 8);
            }

            return $item;
        } catch (\Exception $e) {
            error_log("BookingRepository findById error: " . $e->getMessage());
            return null;
        }
    }

    public function updateStatus(int $id, string $status): bool
    {
        if (!$this->pdo) {
            error_log("BookingRepository updateStatus: PDO not available");
            return false;
        }

        try {
            if ($this->hasColumn('status')) {
                $stmt = $this->pdo->prepare("UPDATE bookings SET status = :status WHERE id = :id");
                $ok = $stmt->execute([':status' => $status, ':id' => $id]);
                if (!$ok) {
                    error_log("BookingRepository updateStatus failed: " . json_encode($stmt->errorInfo()));
                }
                return (bool)$ok;
            }

            // Legacy schema: nothing to update for status column — return false
            error_log("BookingRepository updateStatus: status column not present");
            return false;
        } catch (\Exception $e) {
            error_log("BookingRepository updateStatus error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Finds all bookings for a specific date.
     *
     * @param string $date The date in 'Y-m-d' format.
     * @return array An array of bookings.
     */
    public function findByDate(string $date): array
    {
        if (!$this->pdo) {
            error_log("BookingRepository findByDate: PDO not available");
            return [];
        }

        try {
            // Handle canonical schema with a dedicated 'date' column
            if ($this->hasColumn('date')) {
                $stmt = $this->pdo->prepare("SELECT * FROM bookings WHERE date = :date");
                $stmt->execute([':date' => $date]);
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            // Handle legacy schema with a 'booking_date' datetime column
            if ($this->hasColumn('booking_date')) {
                $stmt = $this->pdo->prepare("SELECT *, SUBSTRING(booking_date, 11, 5) as time FROM bookings WHERE DATE(booking_date) = :date");
                $stmt->execute([':date' => $date]);
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            // Handle another legacy schema with a 'booking_date_time' datetime column
            if ($this->hasColumn('booking_date_time')) {
                $stmt = $this->pdo->prepare("SELECT *, SUBSTRING(booking_date_time, 11, 5) as time FROM bookings WHERE DATE(booking_date_time) = :date");
                $stmt->execute([':date' => $date]);
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            error_log("BookingRepository findByDate: No suitable date column found.");
            return [];
        } catch (\Exception $e) {
            error_log("BookingRepository findByDate error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Finds a single booking by its unique cancellation token.
     *
     * @param string $token The cancellation token.
     * @return array|null The booking data or null if not found.
     */
    public function findByCancellationToken(string $token): ?array
    {
        if (!$this->pdo) {
            error_log("BookingRepository findByCancellationToken: PDO not available");
            return null;
        }

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM bookings WHERE cancellation_token = :token");
            $stmt->execute([':token' => $token]);
            $booking = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $booking ?: null;
        } catch (\Exception $e) {
            error_log("BookingRepository findByCancellationToken error: " . $e->getMessage());
            return null;
        }
    }
}