<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'config.php';

session_start();
include 'config.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$message = "";
// If redirected with a status message (added/edited/deleted), capture it
if (isset($_GET['added'])) {
    $message = "New event added successfully!";
}
if (isset($_GET['edited'])) {
    $message = "Event updated successfully!";
}
if (isset($_GET['deleted'])) {
    $message = "Event deleted successfully!";
}

// Handle the Add Event form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form fields
    $title = $_POST['title'];
    $date = $_POST['event_date'];
    $location = $_POST['location'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    
    // Handle file upload (if an image was chosen)
    $imageName = "";
    if (isset($_FILES['image']) && $_FILES['image']['name'] !== "") {
        // Generate a unique name for the uploaded file
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = __DIR__ . "/uploads/" . $imageName;
        // Optionally, validate file type (e.g., only allow jpg/png)
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $message = "Error uploading image file.";
            $imageName = "";
        }
    }
    // Insert the new event into the database
    $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, location, category, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $title, $description, $date, $location, $category, $imageName);
    if ($stmt->execute()) {
        // Redirect to avoid duplicate form submissions
        header("Location: admin_dashboard.php?added=1");
        exit;
    } else {
        $message = "Database error: " . $stmt->error;
    }
}

// Fetch all events from the database to display
$result = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
$events = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard - Manage Events</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <h2>Event Management Admin Dashboard</h2>
  <p>Welcome, <?php echo $_SESSION['username']; ?>! <a href="logout.php">Logout</a></p>
  
  <!-- Display any success/error messages -->
  <?php if ($message): ?>
    <p class="message"><?php echo $message; ?></p>
  <?php endif; ?>
  
  <h3>Add New Event</h3>
  <form method="POST" action="admin_dashboard.php" enctype="multipart/form-data">
    <div>
      <label for="title">Event Title:</label><br>
      <input type="text" name="title" id="title" required>
    </div>
    <div>
      <label for="event_date">Date:</label><br>
      <input type="date" name="event_date" id="event_date" required>
    </div>
    <div>
      <label for="location">Location:</label><br>
      <input type="text" name="location" id="location" required>
    </div>
    <div>
      <label for="category">Category:</label><br>
      <select name="category" id="category" required>
        <option value="">-- Select Category --</option>
        <option value="Workshop">Workshop</option>
        <option value="Seminar">Seminar</option>
        <option value="Conference">Conference</option>
      </select>
    </div>
    <div>
      <label for="description">Description:</label><br>
      <textarea name="description" id="description" rows="4" required></textarea>
    </div>
    <div>
      <label for="image">Event Image:</label><br>
      <input type="file" name="image" id="image" accept="image/*">
    </div>
    <button type="submit">Add Event</button>
  </form>
  
  <h3>Existing Events</h3>
  <?php if (count($events) === 0): ?>
    <p>No events found.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>Title</th>
        <th>Date</th>
        <th>Location</th>
        <th>Category</th>
        <th>Actions</th>
      </tr>
      <?php foreach ($events as $event): ?>
      <tr>
        <td><?php echo htmlspecialchars($event['title']); ?></td>
        <td><?php echo htmlspecialchars($event['event_date']); ?></td>
        <td><?php echo htmlspecialchars($event['location']); ?></td>
        <td><?php echo htmlspecialchars($event['category']); ?></td>
        <td>
          <a href="edit_event.php?id=<?php echo $event['id']; ?>">Edit</a> | 
          <a href="delete_event.php?id=<?php echo $event['id']; ?>" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</body>
</html>
