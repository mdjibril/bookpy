
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>bookpy — Home</title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
  <div class="wrap">
    <div class="hero">
      <div>
        <div class="card">
          <h1 class="title">Book an appointment — simple & fast</h1>
          <p class="lead">Choose a date and time, provide your details and we will confirm your appointment. Secure, lightweight booking for small businesses.</p>

          <p>
            <a class="cta" href="/booking">Book now</a>
            <a style="margin-left:12px;color:var(--muted);text-decoration:none" href="/admin" title="Admin">Admin</a>
          </p>

          <hr style="border:none;border-top:1px solid #f0f2f7;margin:18px 0">

          <div class="calendar-placeholder">
            Calendar placeholder — integrate a date-picker (flatpickr / Pikaday) here
          </div>
        </div>
      </div>

      <div class="side">
        <div class="card">
          <div class="stat">
            <div>
              <div class="label">Pending bookings</div>
              <div class="value"><?php echo isset($stats['pending']) ? (int)$stats['pending'] : '—'; ?></div>
            </div>
            <div style="font-size:22px;color:var(--accent)">🕒</div>
          </div>

          <div class="stat" style="margin-top:12px">
            <div>
              <div class="label">Confirmed</div>
              <div class="value"><?php echo isset($stats['confirmed']) ? (int)$stats['confirmed'] : '—'; ?></div>
            </div>
            <div style="font-size:22px;color:#16a34a">✔️</div>
          </div>

          <div style="margin-top:14px">
            <h4 style="margin:0 0 8px">Upcoming</h4>
            <div class="list">
              <?php if (!empty($bookings) && is_array($bookings)): ?>
                <?php foreach (array_slice($bookings, 0, 5) as $b): ?>
                  <div class="list-item">
                    <div>
                      <div style="font-weight:600"><?php echo htmlspecialchars($b['name'] ?? ($b['email'] ?? 'Guest')); ?></div>
                      <div style="font-size:13px;color:var(--muted)"><?php echo ($b['date'] ?? '—') . ' • ' . ($b['time'] ?? '—'); ?></div>
                    </div>
                    <div style="font-size:12px;color:var(--muted)"><?php echo ucfirst($b['status'] ?? 'pending'); ?></div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div style="color:var(--muted);font-size:14px">No upcoming bookings</div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="card" style="text-align:center;">
          <div style="font-size:14px;color:var(--muted)">Need help?</div>
          <div style="margin-top:8px"><a class="inline" href="mailto:<?php echo htmlspecialchars(getenv('MAIL_FROM') ?: 'no-reply@example.com'); ?>">Contact support</a></div>
        </div>
      </div>
    </div>
  </div>

  <footer>
    © <?php echo date('Y'); ?> bookpy — Built with PHP • <a class="inline" href="/booking">Make a booking</a>
  </footer>
</body>
</html>