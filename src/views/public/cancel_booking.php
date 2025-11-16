<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Cancel Booking</title>
  <link rel="stylesheet" href="/css/style.css">
  <style>
    body { background: #f4f4f4; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
    .card { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-width: 500px; text-align: center; }
    h1 { margin-top: 0; }
    .booking-details { text-align: left; margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 3px solid #007bff; }
    .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
    .alert-success { background-color: #d4edda; color: #155724; }
    .alert-danger { background-color: #f8d7da; color: #721c24; }
    .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
    .btn-danger { background-color: #dc3545; color: white; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Cancel Your Booking</h1>

    <?php
      $successMessage = $_SESSION['cancellation_success'] ?? null;
      $errorMessage = $_SESSION['cancellation_error'] ?? null;
      unset($_SESSION['cancellation_success'], $_SESSION['cancellation_error']);
    ?>

    <?php if ($successMessage): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <?php if (!$booking): ?>
      <p>This cancellation link is invalid or has expired.</p>
      <a href="/">Return to Homepage</a>
    <?php elseif ($booking['status'] === 'cancelled'): ?>
      <p>This booking has already been cancelled.</p>
      <div class="booking-details">
        <strong>Name:</strong> <?php echo htmlspecialchars($booking['name']); ?><br>
        <strong>Date:</strong> <?php echo htmlspecialchars($booking['date']); ?><br>
        <strong>Time:</strong> <?php echo htmlspecialchars($booking['time']); ?>
      </div>
      <a href="/">Return to Homepage</a>
    <?php else: ?>
      <p>You are about to cancel the following booking:</p>
      <div class="booking-details">
        <strong>Name:</strong> <?php echo htmlspecialchars($booking['name']); ?><br>
        <strong>Date:</strong> <?php echo htmlspecialchars($booking['date']); ?><br>
        <strong>Time:</strong> <?php echo htmlspecialchars($booking['time']); ?>
      </div>
      <form method="post" onsubmit="return confirm('Are you sure you want to permanently cancel this booking?');">
        <?php echo $csrfField; ?>
        <button type="submit" class="btn btn-danger">Yes, Cancel This Booking</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>