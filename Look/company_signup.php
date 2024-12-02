<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_name = $_POST['company_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($password) < 8) {
        $error_message = "Password must have at least 8 characters.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if company already exists
        $check_query = "SELECT * FROM companies WHERE company_username = ? OR company_email = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($row['company_email'] == $email) {
                    $error_message = "Email address is already registered.";
                }
                if ($row['company_username'] == $username) {
                    $error_message = "Username is already taken.";
                }
            }
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $created_at = date('Y-m-d H:i:s');
            
            $sql = "INSERT INTO companies (company_name, company_username, company_email, password, created_at) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $company_name, $username, $email, $hashed_password, $created_at);

            if ($stmt->execute()) {
                header('Location: company_login.php');
                exit();
            } else {
                $error_message = "Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Registration | LOOK</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/company_signup.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <img src="images/logoreal.png" alt="LOOK Logo">
        </div>
        <div class="navbar-text">
            Company Registration
        </div>
    </nav>

    <div class="background-image">
        <div class="signup-container">
            <h2>Register Company</h2>
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="text" name="company_name" placeholder="Company Name" required>
                <input type="text" name="username" placeholder="Company Username" required>
                <input type="email" name="email" placeholder="Company Email" required>
                <input type="password" name="password" placeholder="Password (min. 8 characters)" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Register</button>
                <p>Already have an account? <a href="company_login.php">Login</a></p>
                <p>Are you a student? <a href="signup.php">Register Student</a></p>
            </form>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2024 LOOK. All Rights Reserved.</p>
    </footer>
</body>
</html>