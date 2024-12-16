<?php
session_start();
include('config.php');  // Include the database connection

$doctor_id = $_SESSION['doctor_id'];  // Assuming the doctor is logged in
if (!$doctor_id) {
    header('Location: login.php');  // Redirect to login if doctor is not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rdv_id = $_POST['rdv_id'];  // Appointment ID for cancellation

    // Update the status of the appointment to "canceled"
    $stmt = $conn->prepare("UPDATE rendezvous SET status = 'canceled' WHERE rdv_id = ?");
    $stmt->bind_param("i", $rdv_id);
    $stmt->execute();

    echo "Appointment canceled successfully!";
}

// Fetch the upcoming appointments for the doctor
$stmt = $conn->prepare("SELECT r.rdv_id, u.first_name, u.last_name, r.date_rdv, r.status 
                       FROM rendezvous r 
                       JOIN users u ON r.client_id = u.id 
                       WHERE r.doctor_id = ? AND r.status = 'comming'");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h3>Upcoming Appointments</h3>
<table>
    <tr>
        <th>Client Name</th>
        <th>Appointment Date</th>
        <th>Cancel</th>
    </tr>
    <?php while ($appointment = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?></td>
        <td><?php echo $appointment['date_rdv']; ?></td>
        <td>
            <form method="POST" action="">
                <input type="hidden" name="rdv_id" value="<?php echo $appointment['rdv_id']; ?>">
                <button type="submit">Cancel</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
