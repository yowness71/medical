<link rel="stylesheet" href="home.css">
<?php
// Include the config file for database connection
include('config.php');

// Check if a search query exists
if (isset($_GET['search_query']) && !empty(trim($_GET['search_query']))) {
    $search_query = trim($_GET['search_query']);
    $query_parts = explode(' ', $search_query); // Split search query by space

    // Build SQL based on whether it's one or two parts
    if (count($query_parts) === 1) {
        $sql = "SELECT * FROM users WHERE first_name LIKE ? OR last_name LIKE ?";
        $params = ["%$query_parts[0]%", "%$query_parts[0]%"];
    } else {
        $sql = "SELECT * FROM users WHERE (first_name LIKE ? AND last_name LIKE ?) OR (first_name LIKE ? AND last_name LIKE ?)";
        $params = ["%$query_parts[0]%", "%$query_parts[1]%", "%$query_parts[1]%", "%$query_parts[0]%"];
    }

    // Prepare and execute the query
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, str_repeat("s", count($params)), ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Display the results in a table
        if ($result && mysqli_num_rows($result) > 0) {
            ?> <table class="user-table"> 
            <thead><tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th></tr></thead><tbody>

            <?php  while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                echo '<td>' . htmlspecialchars($row['first_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['last_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
        } else {
            echo '<p>No results found for "' . htmlspecialchars($search_query) . '".</p>';
        }

        mysqli_stmt_close($stmt);
    } else {
        echo '<p>Error preparing the statement: ' . mysqli_error($conn) . '</p>';
    }
}
?>
