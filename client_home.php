<?php
// Start the session to access session data
session_start();

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['user_id']) || $_SESSION['user_type']!="client") {
  header("Location: login.php");
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
  <link rel="stylesheet" href="home.css" />
</head>

<body>
    
    <div class="navbar">
    <!-- Navigation Bar with buttons to switch between home and search -->
    <button class="nav-button" onclick="showContainer('home')">Home</button>
    <button class="nav-button" onclick="showContainer('appointments')">Appointments</button>
    <button class="nav-button" onclick="showContainer('contact')">contact</button>
    <form action="logout.php">
    <button class="logout-button">Logout</button>
    </form>
    </div>
    
  <div class="home-container" id="home-container" >
    

    <p>home</p>
  </div>
 
  <div class="appointments-container" id="appointments-container" style="display: none;" >
    <p>appointments</p>
  </div>
  <div class="contact-container" id="contact-container" style="display: none;">
    

    <p>contacct</p>
  </div>
  
</body>
<script>function showContainer(container) {
  // Store the selected container in localStorage
  localStorage.setItem('activeContainer', container);

  const homeContainer = document.getElementById('home-container');
  const appointmentContainer = document.getElementById('appointments-container');
  const contactContainer = document.getElementById('contact-container');
  
  if (container === 'home') {
    homeContainer.style.display = 'block';
    appointmentContainer.style.display = 'none';
    contactContainer.style.display = 'none';
  } else if (container === 'appointments') {
    homeContainer.style.display = 'none';
    appointmentContainer.style.display = 'block';
    contactContainer.style.display = 'none';
  } else if (container === 'contact') {
    homeContainer.style.display = 'none';
    appointmentContainer.style.display = 'none';
    contactContainer.style.display = 'block';
  }
}
document.addEventListener('DOMContentLoaded', () => {
  const activeContainer = localStorage.getItem('activeContainer') || 'home';
  showContainer(activeContainer);

  // Show the first table by default if in 'home' container
  if (activeContainer === 'home') {
    const firstTable = document.querySelector('.table-container');
    if (firstTable) firstTable.style.display = 'block';
  }
});

</script>
</html> 
