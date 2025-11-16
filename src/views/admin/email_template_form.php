
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?php echo isset($template) ? 'Edit Template' : 'Create Template'; ?> — Admin</title>
  <link rel="stylesheet" href="/css/admin.css">
</head>
<body>
  <div class="admin-container">
    <div class="card">
      <h1><?php echo isset($template) ? 'Edit Email Template' : 'Create Email Template'; ?></h1>
      <hr>
      <form method="post" action="<?php echo isset($template) ? '/admin/email-templates/update/' . $template['id'] : '/admin/email-templates/create'; ?>">
        <?php echo $csrfField; ?>

        <label for="name">Template Name</label>
        <input id="name" name="name" type="text" required value="<?php echo htmlspecialchars($template['name'] ?? '', ENT_QUOTES); ?>">

        <label for="subject">Email Subject</label>
        <input id="subject" name="subject" type="text" required value="<?php echo htmlspecialchars($template['subject'] ?? '', ENT_QUOTES); ?>">

        <label for="body">Email Body</label>
        <textarea id="body" name="body" required><?php echo htmlspecialchars($template['body'] ?? '', ENT_QUOTES); ?></textarea>

        <div class="actions" style="display: flex; gap: 12px; margin-top: 20px;">
          <button type="submit"><?php echo isset($template) ? 'Update Template' : 'Create Template'; ?></button>
          <a href="/admin/email-templates">← Cancel</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>