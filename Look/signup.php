<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $student_number = $_POST['student_number'];

    // Validate student number format (00-0000)
    if (!preg_match('/^\d{2}-\d{4}$/', $student_number)) {
        $error_message = "Invalid student number format. It must be in the format 00-0000.";
    } elseif (strlen($password) < 8) {
        // Validate password length
        $error_message = "Password must have at least 8 characters or numbers.";
    } elseif ($password !== $confirm_password) {
        // Validate matching passwords
        $error_message = "Passwords do not match.";
    } else {
        // Check if the email, username, or student number is already registered
        $check_query = "SELECT * FROM users WHERE email = '$email' OR username = '$username' OR student_number = '$student_number'";
        $result = $conn->query($check_query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($row['email'] == $email) {
                    $error_message = "Email address is already registered to an existing account.";
                }
                if ($row['username'] == $username) {
                    $error_message = "Username is already in use. Please choose another.";
                }
                if ($row['student_number'] == $student_number) {
                    $error_message = "Student number is already registered.";
                }
            }
        } else {
            // Insert user data into the database
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO users (first_name, last_name, email, username, password, student_number)
                    VALUES ('$first_name', '$last_name', '$email', '$username', '$hashed_password', '$student_number')";

            if ($conn->query($sql)) {
                header('Location: index.php');
            } else {
                $error_message = "Error: " . $conn->error;
            }
        }
    }
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="signup.css">
    </head>
    <body>
        <div class="navbar">
            <div class="logo">
                <img src="images/logoreal.png" alt="Logo"> 
            </div>
            <div class="navbar-text">
                Exclusive for Quezon City University Students
            </div>
        </div>

        <div class="background-image">
            <div class="signup-container">
                <form method="POST" action="">
                    <h2>Sign Up</h2>
                    <?php if (isset($error_message)): ?>
                        <p class="error-message"><?php echo $error_message; ?></p>
                    <?php endif; ?>
                    <input type="text" name="first_name" placeholder="First Name" required>
                    <input type="text" name="last_name" placeholder="Last Name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password (min. 8 characters)" required>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <input type="text" name="student_number" placeholder="QCU Student Number (00-0000)" required>
                    <button type="submit">Sign Up</button>
                    <p>Already have an account? <a href="index.php">Sign In</a></p>
                    <p>Are you a company? <a href="company_signup.php">Register Company</a></p>
                </form>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2024 Quezon City University. All Rights Reserved.</p>
        </div>
    </body>
</html>