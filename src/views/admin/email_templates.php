
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Email Templates — Admin</title>
  <link rel="stylesheet" href="/css/admin.css">
</head>
<body>
  <header class="admin-header">
    <div>
      <h1>Email Templates</h1>
    </div>
    <div class="header-right">
      <a href="/admin">← Back to Dashboard</a>
      <a href="/admin/logout" class="logout-btn">Logout</a>
    </div>
  </header>

  <div class="admin-container">
    <?php
      $successMessage = $_SESSION['template_success'] ?? null;
      $errorMessage = $_SESSION['template_error'] ?? null;
      unset($_SESSION['template_success'], $_SESSION['template_error']);
    ?>

    <?php if (!empty($successMessage)): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>
    <?php if (!empty($errorMessage)): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <div class="card">
      <a href="/admin/email-templates/create" class="btn btn-primary" style="margin-bottom: 20px;">+ Create New Template</a>

      <?php if (empty($templates)): ?>
        <p>No email templates found. <a href="/admin/email-templates/create">Create one now</a>.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Subject</th>
                <th>Created</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($templates as $template): ?>
                <tr>
                  <td><?php echo htmlspecialchars($template['name']); ?></td>
                  <td><?php echo htmlspecialchars($template['subject']); ?></td>
                  <td><?php echo substr($template['created_at'], 0, 10); ?></td>
                  <td class="actions">
                      <a href="/admin/email-templates/edit/<?php echo $template['id']; ?>" class="btn btn-primary">Edit</a>
                      <a href="/admin/email-templates/history/<?php echo $template['id']; ?>" class="btn btn-secondary" style="background-color: #6c757d; color: white;">History</a>
                      <form method="post" action="/admin/email-templates/delete/<?php echo $template['id']; ?>" onsubmit="return confirm('Are you sure?');">
                        <?php echo $csrfField; ?>
                        <button type="submit" class="btn btn-danger">Delete</button>
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