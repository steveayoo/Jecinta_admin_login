<?php
session_start();
include 'config.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$event = null;
$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = "";

// If the form is submitted (update event)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get event ID from hidden field
    $eventId = $_POST['id'];
    // Get updated fields from form
    $title = $_POST['title'];
    $date = $_POST['event_date'];
    $location = $_POST['location'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    // Handle image update
    $oldImage = $_POST['old_image'];             // existing image filename
    $newImageName = $oldImage;                   // default to old image
    if (isset($_FILES['image']) && $_FILES['image']['name'] !== "") {
        // A new image was uploaded
        $newImageName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = __DIR__ . "/uploads/" . $newImageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            // Delete the old image file from server if a new one is uploaded successfully
            if ($oldImage) {
                @unlink(__DIR__ . "/uploads/$oldImage");
            }
        } else {
            // If upload failed, keep the old image name
            $newImageName = $oldImage;
        }
    }
    // Update the event record in the database
    $stmt = $conn->prepare("UPDATE events SET title=?, description=?, event_date=?, location=?, category=?, image=? WHERE id=?");
    $stmt->bind_param("ssssssi", $title, $description, $date, $location, $category, $newImageName, $eventId);
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?edited=1");
        exit;
    } else {
        $error = "Update failed: " . $stmt->error;
    }
} else {
    // If GET request, fetch the event data to pre-fill the form
    if ($eventId) {
        $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $event = $result->fetch_assoc();
        } else {
            die("Event not found.");
        }
    } else {
        die("No event ID specified.");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Event</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <h2>Edit Event</h2>
  <?php if ($error): ?>
    <p class="error"><?php echo $error; ?></p>
  <?php endif; ?>
  
  <?php if ($event): ?>
  <form method="POST" action="edit_event.php" enctype="multipart/form-data">
    <!-- Include the event ID and old image name in hidden fields -->
    <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
    <input type="hidden" name="old_image" value="<?php echo htmlspecialchars($event['image']); ?>">
    
    <div>
      <label for="title">Event Title:</label><br>
      <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
    </div>
    <div>
      <label for="event_date">Date:</label><br>
      <input type="date" name="event_date" id="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
    </div>
    <div>
      <label for="location">Location:</label><br>
      <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>
    </div>
    <div>
      <label for="category">Category:</label><br>
      <select name="category" id="category" required>
        <option value="Workshop" <?php if ($event['category']=="Workshop") echo "selected"; ?>>Workshop</option>
        <option value="Seminar" <?php if ($event['category']=="Seminar") echo "selected"; ?>>Seminar</option>
        <option value="Conference" <?php if ($event['category']=="Conference") echo "selected"; ?>>Conference</option>
      </select>
    </div>
    <div>
      <label for="description">Description:</label><br>
      <textarea name="description" id="description" rows="4" required><?php echo htmlspecialchars($event['description']); ?></textarea>
    </div>
    <div>
      <label for="image">Event Image: <small>(leave blank to keep current image)</small></label><br>
      <input type="file" name="image" id="image" accept="image/*">
      <?php if ($event['image']): ?>
        <p>Current image: <img src="uploads/<?php echo htmlspecialchars($event['image']); ?>" alt="Current Image" width="100"></p>
      <?php endif; ?>
    </div>
    <button type="submit">Update Event</button>
    <a href="admin_dashboard.php">Cancel</a>
  </form>
  <?php endif; ?>
</body>
</html>
