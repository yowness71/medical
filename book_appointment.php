<?php
session_start();
include('config.php');  // Include the database connection

// Initialize error and success messages
$appointment_error = "";
$appointment_success = "";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "client") {
    header("Location: login.php");
    exit();
}

$client_id = $_SESSION['user_id'];

// Fetch client's existing specialties
$stmt = $conn->prepare("SELECT DISTINCT d.speciality FROM rendezvous r JOIN doc d ON r.doctor_id = d.iddoc WHERE r.client_id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
$client_specialties = [];
while ($row = $result->fetch_assoc()) {
    $client_specialties[] = $row['speciality']; // Store specialties
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $doctor_id = $_POST['doctor_id'];  // Doctor selection
    $appointment_date = $_POST['appointment_date'];  // Selected date

    // Check if the client already has an appointment on the selected date
    $stmt = $conn->prepare("SELECT * FROM rendezvous WHERE client_id = ? AND date_rdv = ?");
    $stmt->bind_param("is", $client_id, $appointment_date);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $appointment_error = "You already have an appointment on this day.";
    } else {
        // Check if the client already has an appointment with the selected doctor
        $stmt = $conn->prepare("SELECT * FROM rendezvous WHERE client_id = ? AND doctor_id = ?");
        $stmt->bind_param("is", $client_id, $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $appointment_error = "You already have an appointment with this doctor.";
        } else {
            // Check if the doctor is available on the selected date
            $stmt = $conn->prepare("SELECT * FROM rendezvous WHERE doctor_id = ? AND date_rdv = ?");
            $stmt->bind_param("is", $doctor_id, $appointment_date);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $appointment_error = "The doctor is already booked on this day.";
            } else {
                // Insert the appointment into the database
                $stmt = $conn->prepare("INSERT INTO rendezvous (client_id, doctor_id, date_rdv, status) VALUES (?, ?, ?, 'comming')");
                $stmt->bind_param("iis", $client_id, $doctor_id, $appointment_date);
                $stmt->execute();
                $appointment_success = "Appointment booked successfully!";
            }
        }
    }
}
?>

<!-- HTML form for appointment booking -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="booking.css"> <!-- Link to your external CSS file -->
</head>
<body>

    <div class="signup-container">

        <!-- Display error or success messages -->
        <?php if ($appointment_error): ?>
            <div class="error-message">
                <?php echo $appointment_error; ?>
            </div>
        <?php elseif ($appointment_success): ?>
            <div class="success-message">
                <?php echo $appointment_success; ?>
            </div>
        <?php endif; ?>

        <!-- Appointment Booking Form -->
        <form method="POST" action="book_appointment.php" class="signup-form">
            <h2 class="title">Book an Appointment</h2>

            <label for="doctor_id">Select Doctor:</label>
            <select name="doctor_id" required>
            <?php
            // Check if $client_specialties is not empty
            if (count($client_specialties) > 0) {
                // Prepare the SQL query to get available doctors excluding the client's existing specialties
                $placeholders = implode(',', array_fill(0, count($client_specialties), '?')); // Bind dynamic parameters
                $stmt = $conn->prepare("SELECT u.first_name, u.last_name, d.speciality, d.iddoc 
                                        FROM doc d
                                        JOIN users u ON d.iduser = u.id 
                                        WHERE u.type = 'doctor' AND d.speciality NOT IN ($placeholders)");
                $stmt->bind_param(str_repeat('s', count($client_specialties)), ...$client_specialties);
            } else {
                // If $client_specialties is empty, get all doctors
                $stmt = $conn->prepare("SELECT u.first_name, u.last_name, d.speciality, d.iddoc 
                                        FROM doc d
                                        JOIN users u ON d.iduser = u.id 
                                        WHERE u.type = 'doctor'");
            }

            $stmt->execute();
            $result = $stmt->get_result();

            // Display available doctors
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['iddoc'] . "'>" . $row['first_name'] . " " . $row['last_name'] . " - " . $row['speciality'] . "</option>";
            }
            ?>
            </select><br>

            <label for="appointment_date">Select Date:</label>
            <input type="date" name="appointment_date" required><br>

            <button type="submit">Book Appointment</button>
        </form>
        
    </div>

</body>
</html>


