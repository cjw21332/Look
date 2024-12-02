<?php
include 'includes/company_auth.php';
include 'includes/db.php';

$company_id = $_SESSION['company_id'];

// Get company data
$sql = "SELECT * FROM companies WHERE company_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
$company = $result->fetch_assoc();

// Get company background
$bg_sql = "SELECT * FROM company_background WHERE company_id = ?";
$bg_stmt = $conn->prepare($bg_sql);
$bg_stmt->bind_param("i", $company_id);
$bg_stmt->execute();
$background = $bg_stmt->get_result()->fetch_assoc();

// Get company culture
$culture_sql = "SELECT * FROM company_culture WHERE company_id = ?";
$culture_stmt = $conn->prepare($culture_sql);
$culture_stmt->bind_param("i", $company_id);
$culture_stmt->execute();
$culture = $culture_stmt->get_result()->fetch_assoc();

// Get company gallery images
$gallery_sql = "SELECT * FROM company_gallery WHERE company_id = ? ORDER BY created_at DESC";
$gallery_stmt = $conn->prepare($gallery_sql);
$gallery_stmt->bind_param("i", $company_id);
$gallery_stmt->execute();
$gallery_result = $gallery_stmt->get_result();
$gallery_images = [];
while ($row = $gallery_result->fetch_assoc()) {
    $gallery_images[$row['image_type']][] = $row;
}

// Get company jobs
$jobs_sql = "SELECT * FROM company_jobs WHERE company_id = ? AND status = 'active' ORDER BY created_at DESC";
$jobs_stmt = $conn->prepare($jobs_sql);
$jobs_stmt->bind_param("i", $company_id);
$jobs_stmt->execute();
$jobs_result = $jobs_stmt->get_result();
$active_jobs = [];
while ($row = $jobs_result->fetch_assoc()) {
    $active_jobs[] = $row;
}

// Handle Profile Update Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $company_number = $_POST['company_number'];
        $company_address = $_POST['company_address'];
        $company_facebook = $_POST['company_facebook'];
        $company_email = $_POST['company_email'];

        // Handle Company Logo Upload
        if (!empty($_FILES['company_logo']['name'])) {
            $profile_picture = time() . '_' . $_FILES['company_logo']['name'];
            $target_dir = "upload/company_logos/";
            
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $target_file = $target_dir . $profile_picture;
            
            if ($company['profile_picture'] && file_exists($target_dir . $company['profile_picture'])) {
                unlink($target_dir . $company['profile_picture']);
            }
            
            if (move_uploaded_file($_FILES['company_logo']['tmp_name'], $target_file)) {
                $sql = "UPDATE companies SET company_number=?, company_address=?, company_facebook=?, company_email=?, profile_picture=? WHERE company_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssi", $company_number, $company_address, $company_facebook, $company_email, $profile_picture, $company_id);
            }
        } else {
            $sql = "UPDATE companies SET company_number=?, company_address=?, company_facebook=?, company_email=? WHERE company_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $company_number, $company_address, $company_facebook, $company_email, $company_id);
        }

        if ($stmt->execute()) {
            header('Location: company_dashboard.php');
            exit();
        }
    }

    // Handle Background Update
    if (isset($_POST['update_background'])) {
        $background_text = $_POST['background_text'];
        if ($background) {
            $update_sql = "UPDATE company_background SET background_text = ? WHERE company_id = ?";
        } else {
            $update_sql = "INSERT INTO company_background (background_text, company_id) VALUES (?, ?)";
        }
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $background_text, $company_id);
        $update_stmt->execute();
        header('Location: company_dashboard.php?section=background');
        exit();
    }

    // Handle Culture Update
    if (isset($_POST['update_culture'])) {
        $culture_text = $_POST['culture_text'];
        $benefits_text = $_POST['benefits_text'];
        if ($culture) {
            $update_sql = "UPDATE company_culture SET culture_text = ?, benefits_text = ? WHERE company_id = ?";
        } else {
            $update_sql = "INSERT INTO company_culture (culture_text, benefits_text, company_id) VALUES (?, ?, ?)";
        }
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $culture_text, $benefits_text, $company_id);
        $update_stmt->execute();
        header('Location: company_dashboard.php?section=culture');
        exit();
    }

    // Handle Job Addition
    if (isset($_POST['add_job'])) {
        $job_title = $_POST['job_title'];
        $job_description = $_POST['job_description'];
        $requirements = $_POST['requirements'];
        $salary_range = $_POST['salary_range'];
        $location = $_POST['location'];
        $employment_type = $_POST['employment_type'];

        $insert_sql = "INSERT INTO company_jobs (company_id, job_title, job_description, requirements, salary_range, location, employment_type) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("issssss", $company_id, $job_title, $job_description, $requirements, $salary_range, $location, $employment_type);
        
        if ($insert_stmt->execute()) {
            header('Location: company_dashboard.php?section=jobs');
            exit();
        }
    }

    // Handle Gallery Upload
    if (isset($_FILES['gallery_images'])) {
        $image_type = $_POST['image_type'];
        $target_dir = "upload/company_gallery/";
        
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
            $file_name = time() . '_' . $_FILES['gallery_images']['name'][$key];
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($tmp_name, $target_file)) {
                $insert_sql = "INSERT INTO company_gallery (company_id, image_path, image_type) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("iss", $company_id, $file_name, $image_type);
                $insert_stmt->execute();
            }
        }
        header('Location: company_dashboard.php?section=' . $image_type);
        exit();
    }
}

// Get current section from URL parameter
$current_section = isset($_GET['section']) ? $_GET['section'] : 'profile';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Dashboard | LOOK</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/company_dashboard.css">
    <link rel="stylesheet" href="css/company_sections.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo">
            <a href="company_dashboard.php"><img src="images/logoreal.png" alt="LOOK Logo"></a>
        </div>
        <div class="nav-links">
            <a href="company_dashboard.php" class="<?php echo $current_section === 'profile' ? 'active' : ''; ?>">Dashboard</a>
            <a href="company_dashboard.php?section=background" class="<?php echo $current_section === 'background' ? 'active' : ''; ?>">Company Info</a>
            <a href="applicants.php">Applicants</a>
        </div>
        <div class="dropdown">
            <button>
                <i class="fas fa-building"></i>
                <?php echo htmlspecialchars($company['company_name']); ?>
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

    <?php if ($current_section === 'profile'): ?>
        <!-- Profile Section -->
        <section>
            <div class="container">
                <div class="header">
                    <h1>Company Profile</h1>
                    <?php
                    $profile_pic = $company['profile_picture'] ? 'upload/company_logos/' . $company['profile_picture'] : 'images/default-company.png';
                    if (file_exists($profile_pic)) {
                        echo '<img src="' . htmlspecialchars($profile_pic) . '" alt="Company Logo" class="profile-picture">';
                    } else {
                        echo '<img src="images/default-company.png" alt="Company Logo" class="profile-picture">';
                    }
                    ?>
                    <h2><?php echo htmlspecialchars($company['company_name']); ?></h2>
                </div>

                <div class="info">
                    <p>
                        <i class="fas fa-envelope"></i>
                        <strong>Email:</strong> 
                        <a href="mailto:<?php echo htmlspecialchars($company['company_email']); ?>" target="_blank">
                            <?php echo htmlspecialchars($company['company_email']); ?>
                        </a>
                    </p>
                    <p>
                        <i class="fas fa-phone"></i>
                        <strong>Company Number:</strong> 
                        <?php echo htmlspecialchars($company['company_number'] ?: 'Not set'); ?>
                    </p>
                    <p>
                        <i class="fas fa-map-marker-alt"></i>
                        <strong>Company Address:</strong> 
                        <?php echo htmlspecialchars($company['company_address'] ?: 'Not set'); ?>
                    </p>
                    <p>
                        <i class="fab fa-facebook"></i>
                        <strong>Facebook Page:</strong> 
                        <?php if ($company['company_facebook']): ?>
                            <a href="<?php echo htmlspecialchars($company['company_facebook']); ?>" target="_blank">
                                Visit Facebook Page
                            </a>
                        <?php else: ?>
                            Not set
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </section>
    <?php else: ?>
        <!-- Company Info Sections -->
        <section>
            <div class="container">
                <div class="sections-nav">
                    <a href="#background" class="section-link <?php echo $current_section === 'background' ? 'active' : ''; ?>">Company Background</a>
                    <a href="#culture" class="section-link <?php echo $current_section === 'culture' ? 'active' : ''; ?>">Life and Culture</a>
                    <a href="#jobs" class="section-link <?php echo $current_section === 'jobs' ? 'active' : ''; ?>">Offered Jobs</a>
                </div>

                <!-- Background Section -->
                <div id="background" class="section" style="display: <?php echo $current_section === 'background' ? 'block' : 'none'; ?>">
                    <h2>Company Background</h2>
                    <div class="section-content">
                        <div class="text-content">
                            <?php if ($background): ?>
                                <p><?php echo nl2br(htmlspecialchars($background['background_text'])); ?></p>
                            <?php else: ?>
                                <p>No background information available.</p>
                            <?php endif; ?>
                            <button onclick="openEditModal('background')" class="edit-btn">
                                <i class="fas fa-edit"></i> Edit Background
                            </button>
                        </div>

                        <div class="gallery">
                            <h3>Company Gallery</h3>
                            <div class="gallery-grid">
                                <?php if (isset($gallery_images['background'])): ?>
                                    <?php foreach ($gallery_images['background'] as $image): ?>
                                        <div class="gallery-item">
                                            <img src="upload/company_gallery/<?php echo htmlspecialchars($image['image_path']); ?>" 
                                                alt="Company Image">
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <button onclick="openUploadModal('background')" class="upload-btn">
                                <i class="fas fa-upload"></i> Upload Images
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Culture Section -->
                <div id="culture" class="section" style="display: <?php echo $current_section === 'culture' ? 'block' : 'none'; ?>">
                    <h2>Life and Culture</h2>
                    <div class="section-content">
                        <div class="text-content">
                            <?php if ($culture): ?>
                                <h3>Company Culture</h3>
                                <p><?php echo nl2br(htmlspecialchars($culture['culture_text'])); ?></p>
                                <h3>Benefits and Perks</h3>
                                <p><?php echo nl2br(htmlspecialchars($culture['benefits_text'])); ?></p>
                            <?php else: ?>
                                <p>No culture information available.</p>
                            <?php endif; ?>
                            <button onclick="openEditModal('culture')" class="edit-btn">
                                <i class="fas fa-edit"></i> Edit Culture & Benefits
                            </button>
                        </div>

                        <div class="gallery">
                            <h3>Culture Gallery</h3>
                            <div class="gallery-grid">
                                <?php if (isset($gallery_images['culture'])): ?>
                                    <?php foreach ($gallery_images['culture'] as $image): ?>
                                        <div class="gallery-item">
                                            <img src="upload/company_gallery/<?php echo htmlspecialchars($image['image_path']); ?>" 
                                                alt="Culture Image">
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <button onclick="openUploadModal('culture')" class="upload-btn">
                                <i class="fas fa-upload"></i> Upload Images
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Jobs Section -->
                <div id="jobs" class="section" style="display: <?php echo $current_section === 'jobs' ? 'block' : 'none'; ?>">
                    <h2>Offered Jobs</h2>
                    <button onclick="openJobModal()" class="add-job-btn">
                        <i class="fas fa-plus"></i> Add New Job
                    </button>
                    <div class="jobs-grid">
                        <?php foreach ($active_jobs as $job): ?>
                            <div class="job-card">
                                <h3><?php echo htmlspecialchars($job['job_title']); ?></h3>
                                <p class="job-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($job['location']); ?>
                                </p>
                                <p class="job-type">
                                    <i class="fas fa-briefcase"></i>
                                    <?php echo htmlspecialchars($job['employment_type']); ?>
                                </p>
                                <p class="job-salary">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <?php echo htmlspecialchars($job['salary_range']); ?>
                                </p>
                                <button class="view-details-btn" onclick="viewJobDetails(<?php echo $job['job_id']; ?>)">
                                    View Details
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Edit Profile Panel -->
    <div class="modal-overlay" id="modalOverlay" onclick="closeSidePanel()"></div>
    <div class="side-panel" id="editProfilePanel">
        <button class="close-btn" onclick="closeSidePanel()">
            <i class="fas fa-times"></i>
        </button>
        <h2>Edit Company Profile</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="company_logo">
                    <i class="fas fa-image"></i> Company Logo
                </label>
                <input type="file" id="company_logo" name="company_logo" accept="image/*">
            </div>
            
            <div class="form-group">
                <label for="company_number">
                    <i class="fas fa-phone"></i> Company Number
                </label>
                <input type="text" id="company_number" name="company_number" 
                    value="<?php echo htmlspecialchars($company['company_number']); ?>" 
                    placeholder="Enter company contact number">
            </div>
            
            <div class="form-group">
                <label for="company_address">
                    <i class="fas fa-map-marker-alt"></i> Company Address
                </label>
                <textarea id="company_address" name="company_address" 
                        placeholder="Enter company address"><?php echo htmlspecialchars($company['company_address']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="company_facebook">
                    <i class="fab fa-facebook"></i> Facebook Page URL
                </label>
                <input type="url" id="company_facebook" name="company_facebook" 
                    value="<?php echo htmlspecialchars($company['company_facebook']); ?>" 
                    placeholder="Enter Facebook page URL">
            </div>

            <div class="form-group">
                <label for="company_email">
                    <i class="fas fa-envelope"></i> Company Email
                </label>
                <input type="email" id="company_email" name="company_email" 
                    value="<?php echo htmlspecialchars($company['company_email']); ?>" 
                    placeholder="Enter company email">
            </div>
            
            <button type="submit" name="update_profile">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </form>
    </div>

    <!-- Modals -->
    <?php include 'includes/company_modals.php'; ?>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script>
        // Section navigation
        const sectionLinks = document.querySelectorAll('.section-link');
        const sections = document.querySelectorAll('.section');

        sectionLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('href').substring(1);
                const section = targetId.replace('#', '');
                window.location.href = `company_dashboard.php?section=${section}`;
            });
        });

        // Modal functionality
        function openEditModal(type) {
            const modal = document.getElementById(`edit${type.charAt(0).toUpperCase() + type.slice(1)}Modal`);
            modal.style.display = 'block';
        }

        function openUploadModal(type) {
            const modal = document.getElementById('uploadModal');
            document.getElementById('imageType').value = type;
            modal.style.display = 'block';
        }

        function openJobModal() {
            const modal = document.getElementById('jobModal');
            modal.style.display = 'block';
        }

        function viewJobDetails(jobId) {
            // Implement job details view
            console.log('Viewing job details for ID:', jobId);
        }

        // Side panel functionality
        function openSidePanel() {
            document.getElementById("editProfilePanel").classList.add("open");
            document.getElementById("modalOverlay").classList.add("open");
        }

        function closeSidePanel() {
            document.getElementById("editProfilePanel").classList.remove("open");
            document.getElementById("modalOverlay").classList.remove("open");
        }

        // Close modal when clicking the close button or outside the modal
        document.querySelectorAll('.close').forEach(closeBtn => {
            closeBtn.addEventListener('click', function() {
                this.closest('.modal').style.display = 'none';
            });
        });

        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        });

        // Profile picture preview
        document.getElementById('company_logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-picture').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Toggle dropdown menu on hover
        const dropdown = document.querySelector('.dropdown');
        const dropdownContent = document.querySelector('.dropdown-content');

        dropdown.addEventListener('mouseenter', () => {
            dropdownContent.style.display = 'block';
        });

        dropdown.addEventListener('mouseleave', () => {
            dropdownContent.style.display = 'none';
        });
    </script>
</body>
</html>