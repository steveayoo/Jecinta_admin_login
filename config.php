<?php
// Database connection settings
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "events_db";  // use the name of the database you created

// Create connection to MySQL
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If needed, set the timezone (optional, for date handling)
date_default_timezone_set("Europe/London");
?>
