<!DOCTYPE html>
<?php
session_start();

// Include the config file for database connection
include('config.php');
if (isset($_SESSION['user_id']) ) {
  switch ($_SESSION['user_type']) {
    case 'admin':
        header("Location: admin_home.php");
        break;
    case 'client':
        header("Location: client_home.php");
        break;
    case 'doctor':
        header("Location: doctor_home.php");
        break;
    default:
        header("Location: login.php");
        break;
}
  
  exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve form data
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check if email exists in the database
    $query = "SELECT * FROM users WHERE email = '$email' ";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
    
    // Check if user was found
    if ($user) {
        $hashed_password = $user['password'];
        $correct = password_verify($password, $hashed_password);
        
        if ($correct) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['user_type'] = $user['type']; // Store the user type in the session
            
            // Redirect based on user type
            switch ($user['type']) {
                case 'admin':
                    header("Location: admin_home.php");
                    break;
                case 'client':
                    header("Location: client_home.php");
                    break;
                case 'doctor':
                    header("Location: doctor_home.php");
                    break;
                default:
                    header("Location: login.php");
                    break;
            }
            exit; // Ensure no further execution
        } else {
            // Invalid password
            $error_message = "Invalid password.";
        }
    } else {
        // Email not found
        $error_message = "Invalid email.";
    }

    // Close the database connection
    $conn->close();
}
?>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Page</title>
  <link rel="stylesheet" href="signup.css" />
</head>
<body>
  <div class="signup-container">
    <h1 class="title">Login</h1>
    
    <?php if (isset($error_message)): ?>
      <div class="error-message">
        <?php echo $error_message; ?>
      </div>
    <?php endif; ?>
    
    <form class="signup-form" method="POST" novalidate>
      <label for="email">Email:</label>
      <input type="email" name="email" placeholder="Enter your email" required />

      <label for="password">Password:</label>
      <div class="password-group">
        <input type="password" name="password" placeholder="Enter your password" required />
        <button type="button" id="toggle-password" class="toggle-password" onclick="togglePasswordVisibility('password','toggle-password')">👁️</button>
      </div>

      <button class="submit-button" type="submit">Login</button>
    </form>
  </div>

  <script>
    function togglePasswordVisibility(id,btn) {
      const passwordField = document.querySelector(`input[name=${id}]`);
      const toggleButton = document.getElementById(btn);
      if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleButton.textContent = '🙈';
      } else {
        passwordField.type = "password";
        toggleButton.textContent = '👁️';
      }
    }
  </script>
</body>
</html>
