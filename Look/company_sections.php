<?php
include 'includes/company_auth.php';
include 'includes/db.php';

$company_id = $_SESSION['company_id'];

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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

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
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

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
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Sections | LOOK</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/company_sections.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="sections-nav">
        <a href="#background" class="section-link active">Company Background</a>
        <a href="#culture" class="section-link">Life and Culture</a>
        <a href="#jobs" class="section-link">Offered Jobs</a>
    </div>

    <section id="background" class="section">
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
    </section>

    <section id="culture" class="section">
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
    </section>

    <section id="jobs" class="section">
        <h2>Offered Jobs</h2>
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
                    <button class="view-details-btn">View Details</button>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Edit Background Modal -->
    <div id="editBackgroundModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Company Background</h2>
            <form method="POST" action="">
                <textarea name="background_text" placeholder="Enter company background..."><?php 
                    echo htmlspecialchars($background['background_text'] ?? ''); 
                ?></textarea>
                <button type="submit" name="update_background">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Edit Culture Modal -->
    <div id="editCultureModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Company Culture & Benefits</h2>
            <form method="POST" action="">
                <h3>Company Culture</h3>
                <textarea name="culture_text" placeholder="Describe your company culture..."><?php 
                    echo htmlspecialchars($culture['culture_text'] ?? ''); 
                ?></textarea>
                <h3>Benefits and Perks</h3>
                <textarea name="benefits_text" placeholder="List company benefits and perks..."><?php 
                    echo htmlspecialchars($culture['benefits_text'] ?? ''); 
                ?></textarea>
                <button type="submit" name="update_culture">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Upload Images Modal -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Upload Images</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="image_type" id="imageType" value="">
                <input type="file" name="gallery_images[]" multiple accept="image/*" required>
                <button type="submit">Upload Images</button>
            </form>
        </div>
    </div>

    <script>
        // Section navigation
        const sectionLinks = document.querySelectorAll('.section-link');
        const sections = document.querySelectorAll('.section');

        sectionLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('href').substring(1);
                
                sections.forEach(section => {
                    section.style.display = section.id === targetId ? 'block' : 'none';
                });

                sectionLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');
            });
        });

        // Show only the first section initially
        sections.forEach((section, index) => {
            section.style.display = index === 0 ? 'block' : 'none';
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
    </script>
</body>
</html>