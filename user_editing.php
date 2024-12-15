<!DOCTYPE html>
<?php
include('config.php');

// Initialize error messages
$email_error = "";
$name_error = "";
$success_message = "";
if (isset($_GET['id'])) {
    $edit_id = $_GET['id'];


    $edit_query = "SELECT * FROM users WHERE id = $edit_id";
    $result = mysqli_query($conn, $edit_query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $gender = $row['gender'];
        $age = $row['age'];
        $email = $row['email'];
        $mobile = $row['mobile'];
        $type = $row['type'];
        $hashed_password= $row['password'];
        if ($type=="doctor") {
            $edit_query2 = "SELECT * FROM doc WHERE iduser = $edit_id";
            $result2 = mysqli_query($conn, $edit_query2);
            $row2 = mysqli_fetch_assoc($result2);
            $speciality = $row2['speciality'];
        }
    } else {
        echo "User not found.";
        exit;
    }
}
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
    $password = isset($_POST['password']) ? mysqli_real_escape_string($conn, $_POST['password']) : null;
    
    $edit_id = $_POST['edit_id'] ;

    if ($password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    }
    
    $update_query = "UPDATE users SET 
        first_name = '$first_name', 
        last_name = '$last_name', 
        gender = '$gender', 
        age = '$age', 
        email = '$email', 
        mobile = '$mobile', 
        password = '$hashed_password', 
        type = '$type' 
        WHERE id = '$edit_id'";
    

        if (mysqli_query($conn, $update_query)) {
            if ($type === 'doctor') {
                $speciality_query = "REPLACE INTO doc (iduser, speciality) VALUES ('$edit_id', '$speciality')";
                if (mysqli_query($conn, $speciality_query)) {
                    $success_message = "doctor updated successfully.";
                    
                }else{
                    echo "Error updating speciality: " . $conn->error;
                }
            }else{
                $success_message = "user updated successfully.";
            }
        } else {
            echo "Error updating record: " . $conn->error;
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
     <?php elseif ($success_message): ?>
        <div class="success">
            <?php echo $success_message ?>
        </div>
    <?php endif; ?>
    <div class="signup-container">
        <h1 class="title">Create a new User</h1>
        <div id="error-message" class="error-message" style="display: none;"></div>
        <form id="signup-form" class="signup-form" method="POST" novalidate onsubmit="return validateAndSubmit()" onkeydown="preventEnterSubmit(event)">
        <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">

            <!-- Page 1 -->
            <div class="form-page" id="page-1">
                <label for="first-name">First Name:</label>
                <input type="text" id="first-name" name="first_name" value="<?php echo $first_name ?? '' ; ?>" required>

                <label for="last-name">Last Name:</label>
                <input type="text" id="last-name" name="last_name" value="<?php echo $last_name ?? ''; ?>" required>

                <label>Gender:</label>
                <div class="radio-group">
                    <label><input type="radio" name="gender" value="male" <?php echo isset($gender) && $gender === 'male' ? 'checked' : ''; ?>> Male</label>
                    <label><input type="radio" name="gender" value="female" <?php echo isset($gender) && $gender === 'female' ? 'checked' : ''; ?>> Female</label>
                </div>

                <label for="age">Age:</label>
                <input type="number" id="age" name="age" value="<?php echo $age ?? ''; ?>" min="1" max="120" required>

                <div class="btns">
                <button class="cancel-btn" type="button" onclick="window.location.href='admin_home.php';" >Cancel</button>
                <button class="submit-button" type="button" onclick="nextPage()">Next</button>
                </div>
                
            </div>

            <!-- Page 2 -->
            <div class="form-page" id="page-2" style="display: none;">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $email ?? ''; ?>" required>

                <label for="mobile">Mobile Number:</label>
                <input type="tel" id="mobile" name="mobile" value="<?php echo $mobile ?? ''; ?>" pattern="[0-9]{10}" required>

                <label>Type:</label>
                <div class="radio-group">
                    <label><input type="radio" name="type" value="admin" <?php echo isset($type) && $type === 'admin' ? 'checked' : ''; ?>> Admin</label>
                    <label><input type="radio" name="type" value="client" <?php echo isset($type) && $type === 'client' ? 'checked' : ''; ?>> Client</label>
                    <label><input type="radio" name="type" value="doctor" <?php echo isset($type) && $type === 'doctor' ? 'checked' : ''; ?> onchange="toggleSpecialityField()"> Doctor</label>
                </div>

                <div id="speciality-field" style="display: <?php echo isset($type) && $type === 'doctor' ? 'block' : 'none'; ?>;">
                    <label for="speciality">Speciality:</label>
                    <input type="text" id="speciality" name="speciality" value="<?php echo $speciality ?? ''; ?>">
                </div>

                <label for="password">Password:</label>
                <div class="password-group">
                    <input type="password" id="password" name="password" placeholder="Enter your password" >
                    <button type="button" class="toggle-password" id="toggleButton" onclick="togglePasswordVisibility('password', 'toggleButton')">👁️</button>
                </div>

                <label for="confirm-password">Confirm Password:</label>
                <div class="password-group">
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="Re-enter your password" >
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
