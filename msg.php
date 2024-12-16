<?php
session_start();
include 'config.php'; // Database connection

// Fetch doctors based on specialty
$doctors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['specialty'])) {
    $specialty = $_POST['specialty'];

    $query = "SELECT d.iddoc, u.iduser, u.first_name, u.last_name 
              FROM doc d 
              JOIN user u ON d.iduser = u.iduser 
              WHERE d.specialty = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $specialty);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id'])) {
    $sender_id = $_SESSION['iduser']; // Assume user session is active
    $receiver_id = $_POST['receiver_id'];
    $message_text = $_POST['message_text'];
    $file_name = null;
    $file_type = null;
    $file_data = null;

    // Handle file upload
    if (!empty($_FILES['file']['name'])) {
        $file_name = $_FILES['file']['name'];
        $file_type = $_FILES['file']['type'];
        $file_data = file_get_contents($_FILES['file']['tmp_name']);
    }

    $query = "INSERT INTO messages (sender_id, receiver_id, message_text, file_name, file_type, file_data) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iisssb', $sender_id, $receiver_id, $message_text, $file_name, $file_type, $file_data);
    $stmt->execute();

    echo "<script>alert('Message sent successfully!');</script>";
}

// Fetch messages for the current user
$messages = [];
if (isset($_SESSION['iduser'])) {
    $user_id = $_SESSION['iduser'];
    $query = "SELECT m.*, u.first_name, u.last_name 
              FROM messages m
              JOIN user u ON m.sender_id = u.iduser
              WHERE m.receiver_id = ?
              ORDER BY m.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaging System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Message Your Doctor</h1>

        <!-- Select Doctor Form -->
        <form id="doctor-form" method="POST" action="index.php" enctype="multipart/form-data">
            <label for="specialty">Select Specialty:</label>
            <select id="specialty" name="specialty" onchange="this.form.submit()" required>
                <option value="">--Select--</option>
                <option value="Cardiology" <?= isset($_POST['specialty']) && $_POST['specialty'] === 'Cardiology' ? 'selected' : '' ?>>Cardiology</option>
                <option value="Dermatology" <?= isset($_POST['specialty']) && $_POST['specialty'] === 'Dermatology' ? 'selected' : '' ?>>Dermatology</option>
                <option value="Pediatrics" <?= isset($_POST['specialty']) && $_POST['specialty'] === 'Pediatrics' ? 'selected' : '' ?>>Pediatrics</option>
                <!-- Add more specialties -->
            </select>

            <label for="receiver_id">Select Doctor:</label>
            <select id="receiver_id" name="receiver_id" required>
                <option value="">--Select--</option>
                <?php foreach ($doctors as $doctor): ?>
                    <option value="<?= $doctor['iduser'] ?>"><?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="message_text">Message:</label>
            <textarea id="message_text" name="message_text" rows="5" required></textarea>

            <label for="file">Attach File (optional):</label>
            <input type="file" id="file" name="file" accept="image/*,application/pdf">

            <button type="submit">Send Message</button>
        </form>

        <!-- Messages Section -->
        <div class="messages">
            <h2>Your Messages</h2>
            <div id="message-list">
                <?php foreach ($messages as $message): ?>
                    <div class="message">
                        <p>From: <?= htmlspecialchars($message['first_name'] . ' ' . $message['last_name']) ?></p>
                        <p><?= htmlspecialchars($message['message_text']) ?></p>
                        <?php if ($message['file_data']): ?>
                            <p><a href="download.php?id=<?= $message['idmessage'] ?>">Download <?= htmlspecialchars($message['file_name']) ?></a></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
