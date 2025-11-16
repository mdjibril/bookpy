
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>bookpy — Book</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" href="/css/style.css">
  <style>
    :root{
      --bg:#f6f8fb; --card:#ffffff; --accent:#5561f2; --muted:#6b7280;
      --radius:12px; --maxw:720px;
    }
    *{box-sizing:border-box}
    body{margin:0;background:var(--bg);font-family:Inter,system-ui,Arial,sans-serif;color:#0f172a}
    .wrap{max-width:var(--maxw);margin:40px auto;padding:20px}
    .card{background:var(--card);border-radius:var(--radius);padding:28px;box-shadow:0 8px 30px rgba(18,38,63,0.06)}
    h1{margin:0 0 6px;font-size:22px}
    p.lead{margin:0 0 18px;color:var(--muted);font-size:14px}
    form{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    @media(max-width:640px){ form{grid-template-columns:1fr} .actions{grid-column:1/-1} }
    label{display:block;font-size:13px;color:#344054;margin-bottom:6px}
    .field{display:block}
    input[type="text"], input[type="email"], input[type="tel"], input[type="date"], input[type="time"], textarea {
      width:100%;padding:12px;border:1px solid #e6e9ef;border-radius:8px;background:#fff;color:#0f172a;font-size:14px;
    }
    input:focus, textarea:focus{outline:none;box-shadow:0 6px 18px rgba(85,97,242,0.08);border-color:var(--accent)}
    .full { grid-column:1/-1; }
    .actions { display:flex;gap:12px;align-items:center;justify-content:flex-start }
    button.primary { background:var(--accent);color:#fff;border:none;padding:12px 18px;border-radius:10px;font-weight:600;cursor:pointer }
    button.primary:hover{filter:brightness(.95)}
    a.secondary { color:var(--muted);text-decoration:none;font-size:14px }
    .note { font-size:13px;color:var(--muted);margin-top:8px }
    .success { background:#ecfdf5;color:#0f5132;padding:10px;border-radius:8px;margin-bottom:12px;border:1px solid #d1fae5 }
    .error { background:#fff1f2;color:#60142b;padding:10px;border-radius:8px;margin-bottom:12px;border:1px solid #fecaca }
    .meta { margin-top:14px;font-size:13px;color:var(--muted) }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>Book an appointment</h1>
      <p class="lead">Pick a date and time, enter your details and we will confirm shortly.</p>

      <?php
        // $old, $successMessage, $errorMessage may be provided by controller
        $old = $old ?? [];
        $successMessage = $successMessage ?? null;
        $errorMessage = $errorMessage ?? null;
      ?>

      <?php if (!empty($successMessage)): ?>
        <div class="success"><?php echo htmlspecialchars($successMessage); ?></div>
      <?php endif; ?>
      <?php if (!empty($errorMessage)): ?>
        <div class="error"><?php echo htmlspecialchars($errorMessage); ?></div>
      <?php endif; ?>

      <form method="post" action="/booking" novalidate>
        <?php echo \App\Utils\CSRF::inputField(); ?>

        <div class="field full">
          <label for="name">Full name</label>
          <input id="name" name="name" type="text" placeholder="Jane Doe" required
            value="<?php echo htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="field">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" placeholder="you@example.com" required
            value="<?php echo htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="field">
          <label for="phone">Phone (optional)</label>
          <input id="phone" name="phone" type="tel" placeholder="+234 80 000 0000"
            value="<?php echo htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="field">
          <label for="date">Date</label>
          <input id="date" name="date" type="date" required
            value="<?php echo htmlspecialchars($old['date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="field">
          <label for="time">Time</label>
          <select id="time" name="time" required>
            <option value="" disabled selected>Select a date first</option>
          </select>
          <div class="note" id="time-note" style="display: none; color: #d9534f;"></div>
        </div>

        <div class="field full">
          <label for="notes">Notes (optional)</label>
          <textarea id="notes" name="notes" rows="3" placeholder="Tell us any preferences..."><?php echo htmlspecialchars($old['notes'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>

        <div class="actions full">
          <button type="submit" class="primary">Submit booking</button>
          <a class="secondary" href="/">← Back to home</a>
        </div>

        <div class="meta full">
          By submitting you agree we may contact you to confirm this appointment.
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const dateInput = document.getElementById('date');
      const timeSelect = document.getElementById('time');
      const timeNote = document.getElementById('time-note');

      /**
       * Fetches available time slots for a given date and updates the time dropdown.
       * @param {string} selectedDate - The date in YYYY-MM-DD format.
       */
      function fetchAvailableSlots(selectedDate) {
        if (!selectedDate) {
          return;
        }

        timeSelect.innerHTML = '<option value="" disabled selected>Loading...</option>';
        timeSelect.disabled = true;
        timeNote.style.display = 'none';

        fetch(`/api/availability?date=${selectedDate}`)
          .then(response => {
            if (!response.ok) {
              throw new Error(`Network response was not ok, status: ${response.status}`);
            }
            return response.json();
          })
          .then(availableSlots => {
            timeSelect.innerHTML = '';
            timeSelect.disabled = false;

            if (availableSlots.length > 0) {
              timeSelect.innerHTML = '<option value="" disabled selected>Select a time</option>';
              availableSlots.forEach(slot => {
                timeSelect.add(new Option(slot, slot));
              });
            } else {
              timeSelect.innerHTML = '<option value="" disabled selected>No slots available</option>';
              timeNote.textContent = 'There are no available slots for this day. Please choose another date.';
              timeNote.style.display = 'block';
            }
          })
          .catch(error => {
            console.error('Error fetching time slots:', error);
            timeSelect.innerHTML = '<option value="" disabled selected>Error loading times</option>';
            timeNote.textContent = 'Could not load available times. Please try again.';
            timeNote.style.display = 'block';
          });
      }

      // Initialize Flatpickr
      flatpickr(dateInput, {
        altInput: true, // Shows a user-friendly date format
        altFormat: "F j, Y", // e.g., "November 14, 2025"
        dateFormat: "Y-m-d", // The format sent to the server
        minDate: "today",    // Prevents booking past dates
        onChange: function(selectedDates, dateStr) {
          // When a date is picked, fetch the available slots
          fetchAvailableSlots(dateStr);
        }
      });
    });
  </script>
</body>
</html>