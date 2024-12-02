<?php
include 'includes/auth.php';
include 'includes/db.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Fetch all active jobs with company information
$jobs_sql = "SELECT j.*, c.company_name, c.profile_picture 
             FROM company_jobs j 
             JOIN companies c ON j.company_id = c.company_id 
             WHERE j.status = 'active' 
             ORDER BY j.created_at DESC";
$jobs_result = $conn->query($jobs_sql);
$jobs = [];
while ($row = $jobs_result->fetch_assoc()) {
    $jobs[] = $row;
}

// Get unique locations and employment types for filters
$locations = array_unique(array_column($jobs, 'location'));
$employment_types = array_unique(array_column($jobs, 'employment_type'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Search | LOOK</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/jobsearch.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <a href="dashboard.php"><img src="images/logoreal.png" alt="LOOK Logo"></a>
        </div>
        <div class="nav-links">
            <a href="jobsearch.php" class="active">Job Search</a>
            <a href="events.php">Events</a>
            <a href="dashboard.php">Dashboard</a>
        </div>
        <div class="dropdown">
            <button>
                <i class="fas fa-user-circle"></i>
                <?php echo htmlspecialchars($user['username']); ?>
            </button>
            <div class="dropdown-content">
                <a href="dashboard.php">
                    <i class="fas fa-user"></i> Profile
                </a>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <h1>Available Job Opportunities</h1>
        
        <div class="filters">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="searchJobs" placeholder="Search for jobs...">
            </div>
            
            <div class="filter-selects">
                <select id="locationFilter" class="filter-select">
                    <option value="">All Locations</option>
                    <?php foreach ($locations as $location): ?>
                        <option value="<?php echo htmlspecialchars($location); ?>">
                            <?php echo htmlspecialchars($location); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select id="typeFilter" class="filter-select">
                    <option value="">All Types</option>
                    <?php foreach ($employment_types as $type): ?>
                        <option value="<?php echo htmlspecialchars($type); ?>">
                            <?php echo htmlspecialchars($type); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="jobs-grid">
            <?php foreach ($jobs as $job): ?>
                <div class="job-card" 
                     data-location="<?php echo htmlspecialchars($job['location']); ?>"
                     data-type="<?php echo htmlspecialchars($job['employment_type']); ?>">
                    <div class="company-info">
                        <img src="<?php echo $job['profile_picture'] ? 'upload/company_logos/' . htmlspecialchars($job['profile_picture']) : 'images/default-company.png'; ?>" 
                             alt="<?php echo htmlspecialchars($job['company_name']); ?>" 
                             class="company-logo">
                        <h3><?php echo htmlspecialchars($job['company_name']); ?></h3>
                    </div>
                    <div class="job-info">
                        <h4><?php echo htmlspecialchars($job['job_title']); ?></h4>
                        <div class="job-meta">
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                            <span><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($job['employment_type']); ?></span>
                            <span><i class="fas fa-money-bill-wave"></i> <?php echo htmlspecialchars($job['salary_range']); ?></span>
                        </div>
                    </div>
                    <button class="view-details-btn" onclick="viewJobDetails(<?php echo htmlspecialchars(json_encode($job)); ?>)">
                        <i class="fas fa-eye"></i> View Details
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Job Details Modal -->
    <div id="jobModal" class="modal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeJobModal()">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-header">
                <img id="modalCompanyLogo" src="" alt="Company Logo">
                <h2 id="modalCompanyName"></h2>
            </div>
            <div class="modal-body">
                <h3 id="modalJobTitle" class="modal-title"></h3>
                <div class="modal-info">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span id="modalLocation"></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-briefcase"></i>
                        <span id="modalType"></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-money-bill-wave"></i>
                        <span id="modalSalary"></span>
                    </div>
                </div>
                <div class="modal-section">
                    <h4>Job Description</h4>
                    <p id="modalDescription"></p>
                </div>
                <div class="modal-section">
                    <h4>Requirements</h4>
                    <p id="modalRequirements"></p>
                </div>
                <button class="apply-button">
                    <i class="fas fa-paper-plane"></i>
                    Apply Now
                </button>
            </div>
        </div>
    </div>

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
        const searchInput = document.getElementById('searchJobs');
        const locationFilter = document.getElementById('locationFilter');
        const typeFilter = document.getElementById('typeFilter');
        const jobCards = document.querySelectorAll('.job-card');
        const modal = document.getElementById('jobModal');

        function filterJobs() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedLocation = locationFilter.value.toLowerCase();
            const selectedType = typeFilter.value.toLowerCase();

            jobCards.forEach(card => {
                const title = card.querySelector('h4').textContent.toLowerCase();
                const company = card.querySelector('h3').textContent.toLowerCase();
                const location = card.dataset.location.toLowerCase();
                const type = card.dataset.type.toLowerCase();

                const matchesSearch = title.includes(searchTerm) || company.includes(searchTerm);
                const matchesLocation = !selectedLocation || location === selectedLocation;
                const matchesType = !selectedType || type === selectedType;

                card.style.display = (matchesSearch && matchesLocation && matchesType) ? 'block' : 'none';
            });
        }

        function viewJobDetails(job) {
            document.getElementById('modalCompanyLogo').src = job.profile_picture ? 
                `upload/company_logos/${job.profile_picture}` : 'images/default-company.png';
            document.getElementById('modalCompanyName').textContent = job.company_name;
            document.getElementById('modalJobTitle').textContent = job.job_title;
            document.getElementById('modalLocation').textContent = job.location;
            document.getElementById('modalType').textContent = job.employment_type;
            document.getElementById('modalSalary').textContent = job.salary_range;
            document.getElementById('modalDescription').textContent = job.job_description;
            document.getElementById('modalRequirements').textContent = job.requirements;
            
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeJobModal() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Event listeners
        searchInput.addEventListener('input', filterJobs);
        locationFilter.addEventListener('change', filterJobs);
        typeFilter.addEventListener('change', filterJobs);

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target === modal) {
                closeJobModal();
            }
        }

        // Toggle dropdown menu
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