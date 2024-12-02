<?php
include 'includes/auth.php';
include 'includes/db.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Handle Profile Update Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $contact = $_POST['contact_details'];
    $address = $_POST['home_address'];

    // Handle Profile Picture Upload
    if (!empty($_FILES['profile_picture']['name'])) {
        $profile_picture = time() . '_' . $_FILES['profile_picture']['name'];
        $target_dir = "upload/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $target_file = $target_dir . $profile_picture;
        
        // Delete old profile picture if it exists
        if ($user['profile_picture'] && file_exists($target_dir . $user['profile_picture'])) {
            unlink($target_dir . $user['profile_picture']);
        }
        
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $sql = "UPDATE users SET contact_details=?, home_address=?, profile_picture=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $contact, $address, $profile_picture, $user_id);
        }
    } else {
        $sql = "UPDATE users SET contact_details=?, home_address=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $contact, $address, $user_id);
    }

    // Execute Query and Refresh Page
    if ($stmt->execute()) {
        header('Location: dashboard.php');
        exit();
    }
}

// Get updated user data after changes
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | LOOK</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo">
            <a href="dashboard.php"><img src="images/logoreal.png" alt="LOOK Logo"></a>
        </div>
        <div class="nav-links">
            <a href="jobsearch.php">Job Search</a>
            <a href="events.php">Events</a>
            <a href="dashboard.php" class="active">Dashboard</a>
        </div>
        <div class="dropdown">
            <button>
                <i class="fas fa-user-circle"></i>
                <?php echo htmlspecialchars($user['username']); ?>
            </button>
            <div class="dropdown-content">
                <a href="javascript:void(0);" onclick="openSidePanel()">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <section>
        <div class="container">
            <div class="header">
                <h1>Personal Profile</h1>
                <?php
                $profile_pic = $user['profile_picture'] ? 'upload/' . $user['profile_picture'] : 'images/default.png';
                if (file_exists($profile_pic)) {
                    echo '<img src="' . htmlspecialchars($profile_pic) . '" alt="Profile Picture" class="profile-picture">';
                } else {
                    echo '<img src="images/default.png" alt="Profile Picture" class="profile-picture">';
                }
                ?>
                <h2><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
            </div>

            <div class="info">
                <p>
                    <i class="fas fa-envelope"></i>
                    <strong>Email:</strong> 
                    <?php echo htmlspecialchars($user['email']); ?>
                </p>
                <p>
                    <i class="fas fa-id-card"></i>
                    <strong>Student Number:</strong> 
                    <?php echo htmlspecialchars($user['student_number']); ?>
                </p>
                <p>
                    <i class="fas fa-phone"></i>
                    <strong>Contact:</strong> 
                    <?php echo htmlspecialchars($user['contact_details'] ?: 'Not set'); ?>
                </p>
                <p>
                    <i class="fas fa-home"></i>
                    <strong>Address:</strong> 
                    <?php echo htmlspecialchars($user['home_address'] ?: 'Not set'); ?>
                </p>
            </div>
        </div>

        <!-- Modal Overlay -->
        <div class="modal-overlay" id="modalOverlay" onclick="closeSidePanel()"></div>

        <!-- Edit Profile Modal -->
        <div class="side-panel" id="editProfilePanel">
            <button class="close-btn" onclick="closeSidePanel()">
                <i class="fas fa-times"></i>
            </button>
            <h2>Edit Your Profile</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="profile_picture">
                        <i class="fas fa-camera"></i> Profile Picture
                    </label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label for="contact_details">
                        <i class="fas fa-phone"></i> Contact Details
                    </label>
                    <input type="text" id="contact_details" name="contact_details" 
                           value="<?php echo htmlspecialchars($user['contact_details']); ?>" 
                           placeholder="Enter your contact number">
                </div>
                
                <div class="form-group">
                    <label for="home_address">
                        <i class="fas fa-home"></i> Home Address
                    </label>
                    <textarea id="home_address" name="home_address" 
                              placeholder="Enter your complete address"><?php echo htmlspecialchars($user['home_address']); ?></textarea>
                </div>
                
                <button type="submit">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footerlook">
        <div class="footer-content">
            <div class="left-contents">
                <p class="p-foot">
                    FIND YOUR<br>BEST INTEREST!
                </p>
                <a href="mailto:look.official.sender@gmail.com" class="gmail-look">
                    <i class="fas fa-envelope"></i>
                    look.official.sender@gmail.com
                </a>
            </div>
            <div class="right-contents">
                <div class="soc-med-icons">
                    <a href="#" class="soc-med-icon">
                        <img src="images/facebook-app-round-white-icon.png" class="soc-icons" alt="Facebook">
                    </a>
                    <a href="#" class="soc-med-icon">
                        <img src="images/icons8-instagram-50.png" class="soc-icons" alt="Instagram">
                    </a>
                    <a href="#" class="soc-med-icon">
                        <img src="images/x-social-media-white-icon.png" class="soc-icons" alt="Twitter">
                    </a>
                    <a href="#" class="soc-med-icon">
                        <img src="images/whatsapp-white-icon.png" class="soc-icons" alt="WhatsApp">
                    </a>
                </div>
                <div class="footer-links">
                    <a href="#" class="footer-contents">About us</a>
                    <a href="#" class="footer-contents">Terms and conditions</a>
                    <a href="#" class="footer-contents">Privacy policy</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function openSidePanel() {
            document.getElementById("editProfilePanel").classList.add("open");
            document.getElementById("modalOverlay").classList.add("open");
        }

        function closeSidePanel() {
            document.getElementById("editProfilePanel").classList.remove("open");
            document.getElementById("modalOverlay").classList.remove("open");
        }

        // Toggle dropdown menu on hover
        const dropdown = document.querySelector('.dropdown');
        const dropdownContent = document.querySelector('.dropdown-content');

        dropdown.addEventListener('mouseenter', () => {
            dropdownContent.style.display = 'block';
        });

        dropdown.addEventListener('mouseleave', () => {
            dropdownContent.style.display = 'none';
        });

        // Preview image before upload
        document.getElementById('profile_picture').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-picture').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>