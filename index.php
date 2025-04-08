<?php
include 'config.php';
// Fetch all events from database (you could filter out past events if desired)
$result = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
$events = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Upcoming Events</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <h1>Community Events</h1>
  <p style="text-align:right;"><a href="login.php">Admin Login</a></p>
  
  <!-- Search bar and category filter -->
  <div id="search-bar">
    <input type="text" id="searchInput" placeholder="Search events...">
    <select id="categoryFilter">
      <option value="">All Categories</option>
      <option value="Workshop">Workshop</option>
      <option value="Seminar">Seminar</option>
      <option value="Conference">Conference</option>
    </select>
  </div>
  
  <div id="eventList">
    <?php if (count($events) === 0): ?>
      <p>No upcoming events at the moment.</p>
    <?php else: ?>
      <?php foreach ($events as $event): ?>
        <div class="event-item">
          <?php if ($event['image']): ?>
            <img src="uploads/<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="event-img">
          <?php endif; ?>
          <h3><?php echo htmlspecialchars($event['title']); ?></h3>
          <p><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?>
             | <strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?>
             | <strong>Category:</strong> <?php echo htmlspecialchars($event['category']); ?></p>
          <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  
  <script src="assets/script.js"></script>
</body>
</html>
