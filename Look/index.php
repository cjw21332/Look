<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            header('Location: dashboard.php');
        } else {
            $error_message = "Incorrect Password.";
        }
    } else {
        $error_message = "User not found.";
    }
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="index.css" rel="stylesheet">
    </head>
    <body>
        <div class="navbar">
            <div class="logo">
                <img src="images/logoreal.png" alt="Logo"> <!-- Add your logo image here -->
            </div>
            <div class="navbar-text">
                Exclusive for Quezon City University Students
            </div>
        </div>

        <div class="background-image">
            <div class="login-container">
                <form method="POST" action="">
                    <h2>Login</h2>
                    <?php if (isset($error_message)): ?>
                        <p class="error-message"><?php echo $error_message; ?></p>
                    <?php endif; ?>
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Login</button>
                    <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
                    <p>Are you a company? <a href="company_login.php">Company Portal</a></p>
                </form>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2024 Quezon City University. All Rights Reserved.</p>
        </div>
    </body>
</html>
