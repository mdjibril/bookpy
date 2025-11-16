
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>bookpy — Bookings</title>
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
      </div>
      <a href="/admin/logout" class="logout-btn">Logout</a>
    </div>
  </header>

  <div class="admin-container">
    <?php
      $successMessage = $_SESSION['admin_success'] ?? null;
      unset($_SESSION['admin_success']);
    ?>

    <?php if ($successMessage): ?>
      <div class="alert alert-success">
        <?php echo htmlspecialchars($successMessage); ?>
      </div>
    <?php endif; ?>
    <div class="card">
      <h2>All Bookings</h2>
      <div class="filters" style="margin-bottom: 20px;">
        <a href="/admin/bookings?status=pending" class="<?php echo $status === 'pending' ? 'active' : ''; ?>">Pending</a>
        <a href="/admin/bookings?status=confirmed" class="<?php echo $status === 'confirmed' ? 'active' : ''; ?>">Confirmed</a>
        <a href="/admin/bookings?status=cancelled" class="<?php echo $status === 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
        <a href="/admin/bookings?status=all" class="<?php echo $status === 'all' ? 'active' : ''; ?>">All</a>
      </div>

      <?php if (empty($bookings)): ?>
        <p>No bookings found.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Created</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($bookings as $booking): ?>
                <tr>
                  <td><?php echo $booking['id']; ?></td>
                  <td><?php echo htmlspecialchars($booking['name']); ?></td>
                  <td><?php echo htmlspecialchars($booking['email']); ?></td>
                  <td><?php echo htmlspecialchars($booking['phone'] ?? '-'); ?></td>
                  <td><?php echo $booking['date']; ?></td>
                  <td><?php echo $booking['time']; ?></td>
                  <td>
                    <span class="status-badge status-<?php echo $booking['status']; ?>">
                      <?php echo ucfirst($booking['status']); ?>
                    </span>
                  </td>
                  <td><?php echo substr($booking['created_at'], 0, 10); ?></td>
                  <td class="actions">
                    <?php if ($booking['status'] === 'pending'): ?>
                      <form method="post" action="/admin/bookings/confirm">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>"/>
                        <button type="submit" class="btn btn-success">Confirm</button>
                      </form>
                    <?php else: ?>
                      <span style="color:#999">-</span>
                    <?php endif; ?>
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