<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Version History — <?php echo htmlspecialchars($template['name']); ?></title>
  <link rel="stylesheet" href="/css/admin.css">
</head>
<body>
  <header class="admin-header">
    <div>
      <h1>Version History: <?php echo htmlspecialchars($template['name']); ?></h1>
    </div>
    <div class="header-right">
      <a href="/admin/email-templates">← Back to Templates</a>
      <a href="/admin/logout" class="logout-btn">Logout</a>
    </div>
  </header>

  <div class="admin-container">
    <div class="card">
      <?php if (empty($versions)): ?>
        <p>No historical versions found for this template.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Version Date</th>
                <th>Subject</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($versions as $version): ?>
                <tr>
                  <td><?php echo htmlspecialchars($version['created_at']); ?></td>
                  <td><?php echo htmlspecialchars($version['subject']); ?></td>
                  <td class="actions">
                      <form method="post" action="/admin/email-templates/restore/<?php echo $version['id']; ?>" onsubmit="return confirm('Are you sure you want to restore this version? The current version will be archived.');">
                        <?php echo $csrfField; ?>
                        <button type="submit" class="btn btn-primary">Restore</button>
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