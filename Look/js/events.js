document.addEventListener('DOMContentLoaded', () => {
    initializeEvents();
});

async function initializeEvents() {
    try {
        const events = await fetchEvents();
        if (events.error) {
            showError('Failed to load events');
            return;
        }
        
        setupFilters(events);
        renderEvents(events);
        setupEventListeners();
    } catch (error) {
        showError('Failed to initialize events');
        console.error(error);
    }
}

async function fetchEvents() {
    try {
        const response = await fetch('includes/api/events.php');
        return await response.json();
    } catch (error) {
        console.error('Error fetching events:', error);
        return { error: true };
    }
}

function setupFilters(events) {
    const months = new Set();
    const categories = new Set();

    events.forEach(event => {
        const date = new Date(event.event_date);
        months.add(`${date.getMonth()}-${date.getFullYear()}`);
        event.tags.forEach(tag => categories.add(tag));
    });

    populateFilter('filterMonth', Array.from(months), formatMonth);
    populateFilter('filterCategory', Array.from(categories), tag => tag);
}

function formatMonth(monthYear) {
    const [month, year] = monthYear.split('-');
    const date = new Date(year, month);
    return date.toLocaleString('default', { month: 'long', year: 'numeric' });
}

function populateFilter(elementId, values, formatFn) {
    const select = document.getElementById(elementId);
    const sortedValues = values.sort();
    
    sortedValues.forEach(value => {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = formatFn(value);
        select.appendChild(option);
    });
}

function renderEvents(events) {
    const container = document.getElementById('eventsContainer');
    
    if (events.length === 0) {
        container.innerHTML = '<div class="no-events">No events found</div>';
        return;
    }

    container.innerHTML = events.map(event => createEventCard(event)).join('');
}

function createEventCard(event) {
    const date = new Date(event.event_date);
    
    return `
        <div class="event-card">
            <a href="${event.registration_url}" target="_blank" class="event-image">
                <img src="${event.image_url}" alt="${event.title}">
                <div class="event-overlay">
                    <p>${event.description}</p>
                </div>
            </a>
            <div class="event-content">
                <h3 class="event-title">${event.title}</h3>
                <div class="event-info">
                    <span>
                        <i class="fas fa-calendar"></i>
                        ${date.toLocaleDateString('en-US', { 
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })}
                    </span>
                    <span>
                        <i class="fas fa-map-marker-alt"></i>
                        ${event.venue}, ${event.location}
                    </span>
                </div>
                <div class="event-tags">
                    ${event.tags.map(tag => `<span class="event-tag">${tag}</span>`).join('')}
                </div>
            </div>
        </div>
    `;
}

function setupEventListeners() {
    const searchInput = document.getElementById('searchEvents');
    const monthFilter = document.getElementById('filterMonth');
    const categoryFilter = document.getElementById('filterCategory');

    [searchInput, monthFilter, categoryFilter].forEach(element => {
        element.addEventListener('change', filterEvents);
    });
    
    searchInput.addEventListener('input', filterEvents);
}

async function filterEvents() {
    const events = await fetchEvents();
    if (events.error) return;

    const searchTerm = document.getElementById('searchEvents').value.toLowerCase();
    const selectedMonth = document.getElementById('filterMonth').value;
    const selectedCategory = document.getElementById('filterCategory').value;

    const filteredEvents = events.filter(event => {
        const matchesSearch = event.title.toLowerCase().includes(searchTerm) ||
                            event.description.toLowerCase().includes(searchTerm) ||
                            event.location.toLowerCase().includes(searchTerm);
                            
        const matchesMonth = !selectedMonth || getEventMonthYear(event) === selectedMonth;
        const matchesCategory = !selectedCategory || event.tags.includes(selectedCategory);

        return matchesSearch && matchesMonth && matchesCategory;
    });

    renderEvents(filteredEvents);
}

function getEventMonthYear(event) {
    const date = new Date(event.event_date);
    return `${date.getMonth()}-${date.getFullYear()}`;
}

function showError(message) {
    const container = document.getElementById('eventsContainer');
    container.innerHTML = `<div class="error-message">${message}</div>`;
}