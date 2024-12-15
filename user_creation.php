<!DOCTYPE html>
<?php
include('config.php');

// Initialize error messages
$email_error = "";
$name_error = "";
$success_creation = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $speciality = mysqli_real_escape_string($conn, $_POST['speciality'] ?? null);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    $email_check_query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $email_check_query);
    $user = mysqli_fetch_assoc($result);

    $name_check_query = "SELECT * FROM users WHERE first_name = '$first_name' AND last_name = '$last_name' LIMIT 1";
    $name_result = mysqli_query($conn, $name_check_query);
    $name_user = mysqli_fetch_assoc($name_result);

    if ($user) {
        $email_error = "The email address is already registered. Please use a different email.";
        if ($name_user) {
            $name_error = "User already exists with this name. Please use a different name.";
        }
    } elseif ($name_user) {
        $name_error = "User already exists with this name. Please use a different name.";
        if ($user) {
            $email_error = "The email address is already registered. Please use a different email.";
        }
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (first_name, last_name, gender, age, email, mobile, type,  password) 
                VALUES ('$first_name', '$last_name', '$gender', '$age', '$email', '$mobile', '$type', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            
          $success_creation= "New record created successfully";
            // Retrieve the last inserted ID
            $last_user_id = mysqli_insert_id($conn);

            // Insert speciality into the `doc` table if provided
            if (!empty($speciality)) {
                $doc_sql = "INSERT INTO doc (iduser, speciality) VALUES ('$last_user_id', '$speciality')";
                if ($conn->query($doc_sql) === TRUE) {
 
                } else {
                    echo "Error inserting speciality: " . $conn->error;
                }
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    $conn->close();
}
?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Signup Form</title>
    <link rel="stylesheet" href="signup.css">
</head>
<body>
    <?php if ($email_error && $name_error): ?>
        <div class="db-error">
            <?php echo "$email_error <br> $name_error"; ?>
        </div>
    <?php elseif ($email_error || $name_error): ?>
        <div class="db-error">
            <?php echo $email_error ?: $name_error; ?>
        </div>
     <?php elseif ($success_creation): ?>
        <div class="success">
            <?php echo $success_creation ?>
        </div>
    <?php endif; ?>
    <div class="signup-container">
        <h1 class="title">Create a new User</h1>
        <div id="error-message" class="error-message" style="display: none;"></div>
        <form id="signup-form" class="signup-form" method="POST" novalidate onsubmit="return validateAndSubmit()" onkeydown="preventEnterSubmit(event)">
            <!-- Page 1 -->
            <div class="form-page" id="page-1">
                <label for="first-name">First Name:</label>
                <input type="text" id="first-name" name="first_name" placeholder="Enter your first name" required>

                <label for="last-name">Last Name:</label>
                <input type="text" id="last-name" name="last_name" placeholder="Enter your last name" required>

                <label>Gender:</label>
                <div class="radio-group">
                    <label><input type="radio" name="gender" value="male" required> Male</label>
                    <label><input type="radio" name="gender" value="female" required> Female</label>
                </div>

                <label for="age">Age:</label>
                <input type="number" id="age" name="age" placeholder="Enter your age" min="1" required>

                <button class="submit-button" type="button" onclick="nextPage()">Next</button>
            </div>

            <!-- Page 2 -->
            <div class="form-page" id="page-2" style="display: none;">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>

                <label for="mobile">Mobile Number:</label>
                <input type="tel" id="mobile" name="mobile" placeholder="Enter your mobile number" pattern="[0-9]{10}" required>

                <label>Type:</label>
                <div class="radio-group">
                    <label><input type="radio" name="type" value="admin" required> Admin</label>
                    <label><input type="radio" name="type" value="client" required> Client</label>
                    <label><input type="radio" name="type" value="doctor" required onchange="toggleSpecialityField()"> Doctor</label>
                </div>

                <div id="speciality-field" style="display: none;">
                    <label for="speciality">Speciality:</label>
                    <input type="text" id="speciality" name="speciality" placeholder="Enter your speciality">
                </div>

                <label for="password">Password:</label>
                <div class="password-group">
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <button type="button" class="toggle-password" id="toggleButton" onclick="togglePasswordVisibility('password', 'toggleButton')">👁️</button>
                </div>

                <label for="confirm-password">Confirm Password:</label>
                <div class="password-group">
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="Re-enter your password" required>
                    <button type="button" class="toggle-password" id="toggleButton2" onclick="togglePasswordVisibility('confirm-password', 'toggleButton2')">👁️</button>
                </div>

                <div class="btns"> 
                
                <button class="cancel-btn" type="button" onclick="previousPage()" >Back</button>
                <button class="submit-button" type="submit">Create</button>
                </div>
            </div>
        </form>
    </div>

    <script>
      function preventEnterSubmit(event) {
  // Check if the key pressed is Enter (key code 13)
  if (event.key === "Enter") {
    event.preventDefault(); // Prevent form submission
  }
}

      let currentPage = 1;

      function nextPage() {
        const page = document.getElementById(`page-${currentPage}`);
        const inputs = page.querySelectorAll("input");
        const errorMessageDiv = document.getElementById("error-message");

        let allValid = true;
        let firstInvalidInput = null;

        // Validate fields on the current page
        inputs.forEach((input) => {
          if (!input.checkValidity()) {
            allValid = false;
            if (!firstInvalidInput) {
              firstInvalidInput = input;
            }
          }
        });

        if (!allValid) {
          // Display the first validation error message
          errorMessageDiv.textContent = firstInvalidInput.validationMessage;
          errorMessageDiv.style.display = "block";
          firstInvalidInput.focus();
        } else {
          // Hide the error message and move to the next page
          errorMessageDiv.style.display = "none";
          page.style.display = "none";
          currentPage++;
          document.getElementById(`page-${currentPage}`).style.display =
            "block";
        }
      }
      function previousPage() {
        console.log('sdqsdqsdqsd');

        const page = document.getElementById(`page-${currentPage}`);

          page.style.display = "none";
          currentPage--;
          document.getElementById(`page-${currentPage}`).style.display =
            "block";
        }
      function togglePasswordVisibility(id , btn) {
        const passwordField = document.getElementById(id);
        const toggleButton = document.getElementById(btn);
        if (passwordField.type === "password") {
          passwordField.type = "text";
          toggleButton.textContent = '🙈';
        } else {
          passwordField.type = "password";
          toggleButton.textContent = '👁️';
        }
      }

      function validateAndSubmit() {
        const page = document.getElementById(`page-${currentPage}`);
        const inputs = page.querySelectorAll("input");
        const errorMessageDiv = document.getElementById("error-message");

        let allValid = true;
        let firstInvalidInput = null;

        // Validate all inputs on the second page
        inputs.forEach((input) => {
          if (!input.checkValidity()) {
            allValid = false;
            if (!firstInvalidInput) {
              firstInvalidInput = input;
            }
          }
        });

        // Check if passwords match
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("confirm-password").value;

        if (allValid && password !== confirmPassword) {
          allValid = false;
          errorMessageDiv.textContent = "Passwords do not match.";
          errorMessageDiv.style.display = "block";
          return false; // Prevent form submission
        }

        if (!allValid) {
          // Display the first validation error message
          errorMessageDiv.textContent = firstInvalidInput
            ? firstInvalidInput.validationMessage
            : "Please fill all required fields.";
          errorMessageDiv.style.display = "block";
          firstInvalidInput.focus();
          return false; // Prevent form submission
        } else {
          // Form is valid, hide error message and submit form
          errorMessageDiv.style.display = "none";
          return true; // Allow form submission
        }
      }
function toggleSpecialityField() {
            const doctorType = document.querySelector("input[name='type'][value='doctor']");
            const specialityField = document.getElementById("speciality-field");
            specialityField.style.display = doctorType.checked ? "block" : "none";
        } 
    </script>
</body>
</html>
