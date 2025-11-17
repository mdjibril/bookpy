
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>bookpy — Book</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" href="/css/style.css">
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