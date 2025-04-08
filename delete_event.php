<?php
session_start();
include 'config.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $eventId = intval($_GET['id']);
    // Get the image file name (to delete the file)
    $stmt = $conn->prepare("SELECT image FROM events WHERE id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
        $imageFile = $event['image'];
        // Delete the event record from the database
        $delStmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $delStmt->bind_param("i", $eventId);
        $delStmt->execute();
        // Remove the image file from the server, if it exists
        if ($imageFile) {
            @unlink(__DIR__ . "/uploads/$imageFile");
        }
    }
}
// Redirect back to dashboard with a deletion message
header("Location: admin_dashboard.php?deleted=1");
exit;
?>
