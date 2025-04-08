<?php
session_start();
include 'config.php';

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['username'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$error = "";
// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Prepare and execute a query to find the user
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify the password using password_verify (password is hashed in DB)
        if (password_verify($password, $user['password'])) {
            // Correct credentials - set session and redirect to admin dashboard
            $_SESSION['username'] = $user['username'];
            header("Location: admin_dashboard.php");
            exit;
        } else {
            // Password didn't match
            $error = "Incorrect username or password.";
        }
    } else {
        // No such username
        $error = "Incorrect username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Login - Event Dashboard</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <h2>Admin Login</h2>
  <form method="POST" action="">
    <div>
      <label for="username">Username:</label><br>
      <input type="text" name="username" id="username" required>
    </div>
    <div>
      <label for="password">Password:</label><br>
      <input type="password" name="password" id="password" required>
    </div>
    <button type="submit">Login</button>
    <?php if ($error): ?>
      <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
  </form>
</body>
</html>
