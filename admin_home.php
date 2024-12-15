<?php
// Start the session to access session data
session_start();

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['user_id']) || $_SESSION['user_type']!="admin") {
    header("Location: login.php");
    exit();
}

// Retrieve the user's first and last name from the session
$user_first_name = $_SESSION['first_name'];
$user_last_name = $_SESSION['last_name'];
$user_id = $_SESSION['user_id'];  // Store the logged-in user's ID

// Include the config file for database connection
include('config.php');

// Retrieve all user data grouped by type, excluding the logged-in user
$query = "SELECT * FROM users WHERE id != ? ORDER BY type, id";
if ($stmt = mysqli_prepare($conn, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id); // Bind the logged-in user's ID to the query
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die("Error retrieving data: " . mysqli_error($conn));
    }

    // Group users by type
    $users_by_type = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users_by_type[$row['type']][] = $row;
    }

    mysqli_stmt_close($stmt);
}

// Delete user logic
if (isset($_GET['delete_user_id'])) {
    $delete_user_id = $_GET['delete_user_id'];

    // Ensure the logged-in user is not deleting themselves
   
        $delete_query = "DELETE FROM users WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $delete_query)) {
            mysqli_stmt_bind_param($stmt, "i", $delete_user_id);
            if (mysqli_stmt_execute($stmt)) {
                echo "User deleted successfully.";
                header("Location: admin_home.php"); // Redirect after deletion
                exit();
            } else {
                echo "Error deleting user: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing statement: " . mysqli_error($conn);
        }
  
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="home.css">
  <title>Home Page</title>
</head>
<body>

  <div class="navbar">
    <!-- Navigation Bar with buttons to switch between home and search -->
    <button class="nav-button" onclick="showContainer('home')">Home</button>
    <button class="nav-button" onclick="showContainer('search')">Search</button>
    <button class="nav-button" onclick="showContainer('add')">Add</button>
    <form action="logout.php">
    <button class="logout-button" >Logout</button>
    </form>
  </div>

  <div class="home-container" id="home-container">
    <h1 class="title">Welcome, <?php echo htmlspecialchars($user_first_name . ' ' . $user_last_name); ?> !</h1>

    <!-- Navigation Bar for user types -->
    <nav class="navbar">
      <?php foreach ($users_by_type as $type => $users): ?>
        <button class="nav-button" onclick="showTable('<?php echo $type; ?>')">
          <?php echo ucfirst($type); ?>s
        </button>
      <?php endforeach; ?>
    </nav>

    <!-- Tables -->
    <?php foreach ($users_by_type as $type => $users): ?>
      <div class="table-container" id="table-<?php echo $type; ?>" style="display: none;">
        <h2 class="type-title"><?php echo ucfirst($type); ?>s</h2>
        <table class="user-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Email</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
              <td><?php echo htmlspecialchars($user['id']); ?></td>
              <td><?php echo htmlspecialchars($user['first_name']); ?></td>
              <td><?php echo htmlspecialchars($user['last_name']); ?></td>
              <td><?php echo htmlspecialchars($user['email']); ?></td>
              <td>
                <!-- Edit button -->
                <form action="user_editing.php" method="GET" style="display:inline;">
                  <input type="hidden" name="id" value="<?php echo $user['id'];  ?>" />
                  <button type="submit" class="edit-button">Edit</button>
                </form>
                
                <!-- Delete button -->
                  <form action="" method="GET" style="display:inline;" onsubmit="return confirmDeletion();">
                    <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>" />
                    <button type="submit" class="delete-button">Delete</button>
                  </form>

              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Search Container -->
  
  <div class="search-container" id="search-container" style="display: none;">
    <h1 class="title">Search Users</h1>
    <form action="search_user.php" method="GET" target="search-results-iframe">
      <input type="text" name="search_query" placeholder="Search by name or email" />
      <button type="submit" class="search-button">Search</button>
    </form>

    <!-- Iframe to display search results -->
    <iframe id="search-results-iframe" name="search-results-iframe" style="width: 100%; height: 400px; border: none;"></iframe>
  </div>

  <!-- Add Container -->
  <div class="add-container" id="add-container" style="display: none;">
    <?php include('user_creation.php');
 ?>
  </div>
  

  </div>
 <script>
  // JavaScript to switch between containers and persist the state
function showContainer(container) {
  // Store the selected container in localStorage
  localStorage.setItem('activeContainer', container);

  const homeContainer = document.getElementById('home-container');
  const searchContainer = document.getElementById('search-container');
  const addContainer = document.getElementById('add-container');
  
  if (container === 'home') {
    homeContainer.style.display = 'block';
    searchContainer.style.display = 'none';
    addContainer.style.display = 'none';
  } else if (container === 'search') {
    homeContainer.style.display = 'none';
    searchContainer.style.display = 'block';
    addContainer.style.display = 'none';
  } else if (container === 'add') {
    homeContainer.style.display = 'none';
    searchContainer.style.display = 'none';
    addContainer.style.display = 'block';
  }
}
document.getElementById(`table-admin`).style.display = 'block';
// JavaScript to show/hide tables
function showTable(type) {
  const tables = document.querySelectorAll('.table-container');
  tables.forEach(table => table.style.display = 'none'); // Hide all tables
  document.getElementById(`table-${type}`).style.display = 'block'; // Show the selected table
}

// On page load, restore the last active container
document.addEventListener('DOMContentLoaded', () => {
  const activeContainer = localStorage.getItem('activeContainer') || 'home';
  showContainer(activeContainer);

  // Show the first table by default if in 'home' container
  if (activeContainer === 'home') {
    const firstTable = document.querySelector('.table-container');
    if (firstTable) firstTable.style.display = 'block';
  }
});
function confirmDeletion() {
  return confirm("Are you sure you want to delete this user?");
}

 </script>
</body>
</html>
