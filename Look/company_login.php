<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM companies WHERE company_username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $company = $result->fetch_assoc();
        if (password_verify($password, $company['password'])) {
            session_start();
            $_SESSION['company_id'] = $company['company_id'];
            header('Location: company_dashboard.php');
            exit();
        } else {
            $error_message = "Incorrect Password.";
        }
    } else {
        $error_message = "Company not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Login | LOOK</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/company_login.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <img src="images/logoreal.png" alt="LOOK Logo">
        </div>
        <div class="navbar-text">
            Company Portal
        </div>
    </nav>

    <div class="background-image">
        <div class="login-container">
            <h2>Company Login</h2>
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="text" name="username" placeholder="Company Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
                <p>Don't have an account? <a href="company_signup.php">Register Company</a></p>
                <p>Are you a student? <a href="index.php">Student Portal</a></p>
            </form>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2024 LOOK. All Rights Reserved.</p>
    </footer>
</body>
</html>