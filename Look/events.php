<?php
include 'includes/auth.php';
include 'includes/db.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Fetch events with their tags
$events_sql = "SELECT e.*, GROUP_CONCAT(et.tag_name) as tags 
               FROM events e 
               LEFT JOIN event_tags et ON e.event_id = et.event_id 
               GROUP BY e.event_id 
               ORDER BY e.event_date ASC";
$events_result = $conn->query($events_sql);
$events = [];
while ($row = $events_result->fetch_assoc()) {
    $row['tags'] = $row['tags'] ? explode(',', $row['tags']) : [];
    $events[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events | LOOK</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/events.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <a href="dashboard.php">
                <img src="images/logoreal.png" alt="LOOK Logo">
            </a>
        </div>
        <div class="nav-links">
            <a href="jobsearch.php">Job Search</a>
            <a href="events.php" class="active">Events</a>
            <a href="dashboard.php">Dashboard</a>
        </div>
        <div class="user-profile">
            <button class="profile-button">
                <i class="fas fa-user-circle"></i>
                <?php echo htmlspecialchars($user['username']); ?>
            </button>
            <div class="dropdown-menu">
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
        <h1>Tech Events & Conferences</h1>
        
        <div class="filters">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="searchEvents" placeholder="Search events...">
            </div>
            
            <div class="filter-selects">
                <select id="filterLocation" class="filter-select">
                    <option value="">All Locations</option>
                    <?php
                    $locations = array_unique(array_column($events, 'location'));
                    foreach ($locations as $location) {
                        echo "<option value='" . htmlspecialchars($location) . "'>" . htmlspecialchars($location) . "</option>";
                    }
                    ?>
                </select>
                
                <select id="filterMonth" class="filter-select">
                    <option value="">All Months</option>
                    <?php
                    $months = [];
                    foreach ($events as $event) {
                        $month = date('F Y', strtotime($event['event_date']));
                        if (!in_array($month, $months)) {
                            $months[] = $month;
                            echo "<option value='" . htmlspecialchars($month) . "'>" . htmlspecialchars($month) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="events-grid">
            <?php foreach ($events as $event): ?>
            <div class="event-card" data-location="<?php echo htmlspecialchars($event['location']); ?>" 
                data-month="<?php echo date('F Y', strtotime($event['event_date'])); ?>"
                onclick="openEventModal(<?php echo htmlspecialchars(json_encode($event)); ?>)">
                <div class="event-image">
                    <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                </div>
                <div class="event-content">
                    <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                    <div class="event-info">
                        <span>
                            <i class="fas fa-calendar"></i>
                            <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                        </span>
                        <span>
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($event['venue']); ?>, <?php echo htmlspecialchars($event['location']); ?>
                        </span>
                    </div>
                    <div class="event-tags">
                        <?php foreach ($event['tags'] as $tag): ?>
                            <span class="event-tag"><?php echo htmlspecialchars($tag); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Event Modal -->
    <div id="eventModal" class="event-modal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeEventModal()">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-header">
                <img id="modalImage" src="" alt="Event Image">
            </div>
            <div class="modal-body">
                <h2 id="modalTitle"></h2>
                <div class="event-info">
                    <p id="modalDate"></p>
                    <p id="modalLocation"></p>
                </div>
                <div class="event-description">
                    <p id="modalDescription"></p>
                </div>
                <div id="modalTags" class="event-tags"></div>
                <a id="modalRegisterLink" href="" target="_blank" class="register-button">
                    Event Details
                </a>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Search and filter functionality
        const searchInput = document.getElementById('searchEvents');
        const locationFilter = document.getElementById('filterLocation');
        const monthFilter = document.getElementById('filterMonth');
        const eventCards = document.querySelectorAll('.event-card');

        function filterEvents() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedLocation = locationFilter.value;
            const selectedMonth = monthFilter.value;

            eventCards.forEach(card => {
                const title = card.querySelector('.event-title').textContent.toLowerCase();
                const location = card.dataset.location;
                const month = card.dataset.month;
                const tags = Array.from(card.querySelectorAll('.event-tag'))
                    .map(tag => tag.textContent.toLowerCase());

                const matchesSearch = title.includes(searchTerm) || 
                                    tags.some(tag => tag.includes(searchTerm));
                const matchesLocation = !selectedLocation || location === selectedLocation;
                const matchesMonth = !selectedMonth || month === selectedMonth;

                card.style.display = (matchesSearch && matchesLocation && matchesMonth) ? 'block' : 'none';
            });
        }

        // Modal functionality
        function openEventModal(event) {
            const modal = document.getElementById('eventModal');
            document.getElementById('modalImage').src = event.image_url;
            document.getElementById('modalTitle').textContent = event.title;
            document.getElementById('modalDate').textContent = new Date(event.event_date).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('modalLocation').textContent = `${event.venue}, ${event.location}`;
            document.getElementById('modalDescription').textContent = event.description;
            document.getElementById('modalRegisterLink').href = event.registration_url;
            
            // Display tags
            const tagsContainer = document.getElementById('modalTags');
            tagsContainer.innerHTML = event.tags.map(tag => 
                `<span class="event-tag">${tag}</span>`
            ).join('');
            
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeEventModal() {
            const modal = document.getElementById('eventModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Event listeners
        searchInput.addEventListener('input', filterEvents);
        locationFilter.addEventListener('change', filterEvents);
        monthFilter.addEventListener('change', filterEvents);

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('eventModal');
            if (event.target === modal) {
                closeEventModal();
            }
        }
    </script>
</body>
</html>