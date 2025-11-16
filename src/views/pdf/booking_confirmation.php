<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmation</title>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .container { padding: 20px; }
        h1 { font-size: 24px; color: #222; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .details { margin-top: 20px; }
        .details p { margin: 5px 0; }
        .details strong { display: inline-block; width: 120px; }
        .footer { margin-top: 40px; font-size: 10px; color: #777; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Booking Confirmation</h1>
        <p>Thank you for your booking. Here are your appointment details:</p>

        <div class="details">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($booking['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking['phone']); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($booking['date']); ?></p>
            <p><strong>Time:</strong> <?php echo htmlspecialchars($booking['time']); ?></p>
        </div>

        <div class="footer">
            <p>If you have any questions, please contact us. We look forward to seeing you!</p>
        </div>
    </div>
</body>
</html>