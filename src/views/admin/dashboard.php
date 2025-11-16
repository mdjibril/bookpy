
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>bookpy — Admin Dashboard</title>
  <link rel="stylesheet" href="/css/admin.css">
</head>
<body>
  <header class="admin-header">
    <div>
      <h1>bookpy Admin</h1>
    </div>
    <div class="header-right">
      <div class="user-info">
        Logged in as: <strong><?php echo htmlspecialchars(\App\Utils\Auth::getUsername()); ?></strong>
      </div>
      <div class="nav">
        <a href="/admin">Dashboard</a>
        <a href="/admin/bookings?status=pending">Pending</a>
        <a href="/admin/bookings?status=confirmed">Confirmed</a>
        <a href="/admin/bookings?status=all">All</a>
        <a href="/admin/email-templates">Email Template</a>
      </div>
      <a href="/admin/logout" class="logout-btn">Logout</a>
    </div>
  </header>

  <div class="admin-container">
    <!-- rest of dashboard content stays same -->
    <div class="card">
      <h2>Dashboard</h2>
      <div style="display: flex; gap: 20px;">
        <div class="stat">
          <div class="stat-value" style="font-size: 32px; font-weight: bold; color: #0066cc;"><?php echo count($pending); ?></div>
          <div class="stat-label" style="font-size: 14px; color: #666; margin-top: 4px;">Pending Bookings</div>
        </div>
        <div class="stat">
          <div class="stat-value" style="font-size: 32px; font-weight: bold; color: #0066cc;"><?php echo count($bookings); ?></div>
          <div class="stat-label" style="font-size: 14px; color: #666; margin-top: 4px;">Total Bookings</div>
        </div>
      </div>
    </div>

    <div class="card">
      <h2>Recent Pending Bookings</h2>
      <?php if (empty($pending)): ?>
        <p>No pending bookings.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Date</th>
                <th>Time</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach (array_slice($pending, 0, 5) as $booking): ?>
                <tr>
                  <td><?php echo htmlspecialchars($booking['name']); ?></td>
                  <td><?php echo htmlspecialchars($booking['email']); ?></td>
                  <td><?php echo $booking['date']; ?></td>
                  <td><?php echo $booking['time']; ?></td>
                  <td class="actions">
                    <form method="post" action="/admin/bookings/confirm">
                      <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>"/>
                      <button type="submit" class="btn btn-success">Confirm</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>