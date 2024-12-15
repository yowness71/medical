<?php
// Start the session to access session data
session_start();

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header('login.php');
    exit();
}

// Retrieve the user's first and last name from the session
$user_first_name = $_SESSION['first_name'];
$user_last_name = $_SESSION['last_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Home Page</title>
  <link rel="stylesheet" href="signup.css" />
</head>
<body>
  <div class="home-container">
    <h1 class="title">Welcome, <?php echo htmlspecialchars($user_first_name . ' ' . $user_last_name); ?>!</h1>

    <p>You are now logged in.</p>

    <!-- Logout button -->
    <form method="POST" action="logout.php">
      <button type="submit" class="submit-button">Logout</button>
    </form>
  </div>
</body>
</html> 
