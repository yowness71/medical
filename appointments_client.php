<?php
include('config.php');  // Include the database connection

// Check if the client is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "client") {
    header("Location: login.php");
    exit();
}

$client_id = $_SESSION['user_id'];

// Fetch client's appointments
$stmt = $conn->prepare("SELECT  r.date_rdv, d.speciality, u.first_name, u.last_name
                        FROM rendezvous r
                        JOIN doc d ON r.doctor_id = d.iddoc
                        JOIN users u ON d.iduser = u.id
                        WHERE r.client_id = ? AND r.status = 'comming'
                        ORDER BY r.date_rdv ASC");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Appointments</title>
    <link rel="stylesheet" href="appointments.css"> <!-- Add your CSS link here -->
</head>
<body>
    <div class="appointments-container">
        <h2 class="title">Your Upcoming Appointments</h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="appointments-table">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Specialty</th>
                        <th>Date</th>

                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                            <td><?php echo $row['speciality']; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($row['date_rdv'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no upcoming appointments.</p>
        <?php endif; ?>
    </div>
</body>
</html>
