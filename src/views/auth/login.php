<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>bookpy — Admin Login</title>
  <link rel="stylesheet" href="/css/admin.css">
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; min-height: 100vh;">
  <div class="card" style="width: 100%; max-width: 400px;">
    <h1>bookpy Admin</h1>

    <?php if (!empty($error)): ?>
      <div class="error">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form method="post" action="/admin/login">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autofocus />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />
      </div>

      <button type="submit">Sign In</button>
    </form>

    <div class="footer" style="text-align: center; margin-top: 20px;">
      <p><a href="/">← Back to Home</a></p>
    </div>
  </div>
</body>
</html>