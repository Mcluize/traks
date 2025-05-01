@extends(backpack_view('blank'))

@section('header')
<div class="container-fluid p-0">
    <div class="justify-content-between align-items-left">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item"><a href="#">Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tracking</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@push('after_styles')
<style>
    .app-body .content-wrapper {
        padding: 0 !important;
        margin: 0 !important;
    }
    .container-fluid {
        padding: 0 !important;
    }
    .content-header {
        padding: 0 !important;
    }
    .content {
        padding: 0 !important;
    }
</style>
@endpush

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/tracking.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<div class="tracking-container">
    <div class="map-section">
        <div class="map-container">
            <div id="map"></div>
            <!-- Map Legend -->
            <div class="map-legend">
                <h4>Check-in Legend</h4>
                <div class="legend-item">
                    <div class="legend-marker first-checkin"></div>
                    <span>First Check-in</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker intermediate-checkin"></div>
                    <span>Intermediate Check-in</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker last-checkin"></div>
                    <span>Last Check-in</span>
                </div>
            </div>
            <div class="tourist-info-section">
                <div class="search-container">
                    <div class="search-header">Search ID</div>
                    <div class="search-filter-container">
                        <input type="text" class="search-input" placeholder="Enter tourist ID">
                        <select class="filter-select">
                            <option value="all_time">All Time</option>
                            <option value="today">Today</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month">This Month</option>
                        </select>
                    </div>
                </div>
                <div class="tourist-cards"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Table View - Centered with no backdrop and semi-transparent background -->
<div class="modal fade" id="checkinModal" tabindex="-1" role="dialog" aria-labelledby="checkinModalLabel" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkinModalLabel">Check-in History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-container"></div>
                <div class="pagination-container"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize Supabase client
const supabase = window.supabase.createClient(
    '{{ config('services.supabase.url') }}',
    '{{ config('services.supabase.key') }}'
);

// Initialize Leaflet map
const map = L.map('map', {
    zoomControl: true,
    attributionControl: true,
}).setView([7.0767, 125.8259], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Define marker icons
const greenIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const redIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const blueIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

// Ensure the map fills its container fully
setTimeout(function() {
    map.invalidateSize();
}, 100);

// Fetch check-ins from Supabase with filter
async function fetchCheckins(touristId, filter) {
    try {
        let query = supabase
            .from('checkins')
            .select(`
                timestamp,
                tourist_spots (
                    name,
                    latitude,
                    longitude
                )
            `)
            .eq('tourist_id', touristId)
            .order('timestamp', { ascending: true });

        const now = new Date();
        if (filter === 'today') {
            const startOfDay = new Date(now.getFullYear(), now.getMonth(), now.getDate()).toISOString();
            query = query.gte('timestamp', startOfDay);
        } else if (filter === 'this_week') {
            const dayOfWeek = now.getDay(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
            const startOfWeek = new Date(now);
            startOfWeek.setDate(now.getDate() - (dayOfWeek === 0 ? 6 : dayOfWeek - 1)); // Set to Monday
            startOfWeek.setHours(0, 0, 0, 0);
            query = query.gte('timestamp', startOfWeek.toISOString());
        } else if (filter === 'this_month') {
            const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1).toISOString();
            query = query.gte('timestamp', startOfMonth);
        }

        const { data, error } = await query;
        if (error) throw new Error(error.message);
        console.log('Fetched check-ins:', data);
        return data;
    } catch (error) {
        console.error('Error fetching check-ins:', error);
        return null;
    }
}

// Function to create a curved path using quadratic Bezier curve points
function createCurvedPath(coords) {
    const curvedCoords = [];
    const curveHeightFactor = 0.8;

    for (let i = 0; i < coords.length - 1; i++) {
        const start = coords[i];
        const end = coords[i + 1];
        const midLat = (start[0] + end[0]) / 2;
        const midLng = (start[1] + end[1]) / 2;

        const controlLat = midLat + curveHeightFactor * Math.abs(end[1] - start[1]);
        const controlLng = midLng;

        const numPoints = 10;
        for (let t = 0; t <= 1; t += 1 / numPoints) {
            const t2 = t * t;
            const mt = 1 - t;
            const mt2 = mt * mt;

            const lat = mt2 * start[0] + 2 * mt * t * controlLat + t2 * end[0];
            const lng = mt2 * start[1] + 2 * mt * t * controlLng + t2 * end[1];
            curvedCoords.push([lat, lng]);
        }
    }
    return curvedCoords;
}

// Plot check-ins on the map with curved, dashed lines
function plotCheckins(checkins) {
    map.eachLayer(layer => {
        if (layer instanceof L.Marker || layer instanceof L.Polyline) {
            map.removeLayer(layer);
        }
    });

    const pathCoordinates = [];
    checkins.forEach((checkin, index) => {
        const { latitude, longitude, name } = checkin.tourist_spots;
        let markerIcon;
        
        // Determine which icon to use based on position in the checkins array
        if (index === 0) {
            markerIcon = greenIcon; // First check-in
        } else if (index === checkins.length - 1) {
            markerIcon = redIcon; // Last check-in
        } else {
            markerIcon = blueIcon; // Intermediate check-ins
        }
        
        const marker = L.marker([latitude, longitude], { icon: markerIcon })
            .addTo(map)
            .bindPopup(`<b>${name}</b><br>Check-in: ${new Date(checkin.timestamp).toLocaleString()}`);
        
        pathCoordinates.push([latitude, longitude]);
    });

    if (pathCoordinates.length > 1) {
        const curvedPath = createCurvedPath(pathCoordinates);
        L.polyline(curvedPath, {
            color: 'blue',
            weight: 4,
            dashArray: '10, 10',
            dashOffset: '0'
        }).addTo(map);
    }

    if (pathCoordinates.length > 0) {
        const bounds = L.latLngBounds(pathCoordinates);
        map.fitBounds(bounds);
    }
}

// Display summary with table view button
function displaySummary(checkins, touristId) {
    const firstCheckin = checkins.length > 0 ? new Date(checkins[0].timestamp).toLocaleString() : 'N/A';
    const lastCheckin = checkins.length > 0 ? new Date(checkins[checkins.length - 1].timestamp).toLocaleString() : 'N/A';
    const lastLocation = checkins.length > 0 ? checkins[checkins.length - 1].tourist_spots.name : 'N/A';

    const summaryHtml = `
        <p><strong>Tourist ID:</strong> ${touristId}</p>
        <p><strong>Number of Check-ins:</strong> ${checkins.length} <button class="view-table-button" data-toggle="modal" data-target="#checkinModal">View Table</button></p>
        <p><strong>First Check-in:</strong> ${firstCheckin}</p>
        <p><strong>Last Check-in:</strong> ${lastCheckin}</p>
        <p><strong>Last Location:</strong> ${lastLocation}</p>
    `;
    document.querySelector('.tourist-cards').innerHTML = summaryHtml;

    window.checkinsData = checkins;

    document.querySelector('.view-table-button').addEventListener('click', () => {
        displayTableWithPagination(checkins, 1);
    });
}

// Display check-ins in a table with pagination
function displayTableWithPagination(checkins, currentPage) {
    const itemsPerPage = 5;
    const totalPages = Math.ceil(checkins.length / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, checkins.length);
    const currentCheckins = checkins.slice(startIndex, endIndex);

    const tableContainer = document.querySelector('.table-container');
    const tableHtml = `
        <table class="checkins-table">
            <thead>
                <tr>
                    <th>Tourist Spot</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                ${currentCheckins.map(checkin => `
                    <tr>
                        <td>${checkin.tourist_spots.name}</td>
                        <td>${new Date(checkin.timestamp).toLocaleString()}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
    tableContainer.innerHTML = tableHtml;

    const paginationContainer = document.querySelector('.pagination-container');
    let paginationHtml = '';

    if (totalPages > 1) {
        paginationHtml += `<button class="pagination-button" ${currentPage === 1 ? 'disabled' : ''} data-page="${currentPage - 1}">« Previous</button>`;
        
        if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) {
                paginationHtml += `<button class="pagination-button ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
            }
        } else {
            paginationHtml += `<button class="pagination-button ${currentPage === 1 ? 'active' : ''}" data-page="1">01</button>`;
            if (currentPage > 3) {
                paginationHtml += `<span class="pagination-number">...</span>`;
            } else {
                paginationHtml += `<button class="pagination-button ${currentPage === 2 ? 'active' : ''}" data-page="2">02</button>`;
            }
            if (currentPage > 2 && currentPage < totalPages - 1) {
                const pageStr = currentPage < 10 ? `0${currentPage}` : currentPage;
                paginationHtml += `<button class="pagination-button active" data-page="${currentPage}">${pageStr}</button>`;
            }
            if (currentPage < totalPages - 2) {
                paginationHtml += `<span class="pagination-number">...</span>`;
            } else {
                const pageStr = (totalPages - 1) < 10 ? `0${totalPages - 1}` : (totalPages - 1);
                paginationHtml += `<button class="pagination-button ${currentPage === totalPages - 1 ? 'active' : ''}" data-page="${totalPages - 1}">${pageStr}</button>`;
            }
            const lastPageStr = totalPages < 10 ? `0${totalPages}` : totalPages;
            paginationHtml += `<button class="pagination-button ${currentPage === totalPages ? 'active' : ''}" data-page="${totalPages}">${lastPageStr}</button>`;
        }
        paginationHtml += `<button class="pagination-button" ${currentPage === totalPages ? 'disabled' : ''} data-page="${currentPage + 1}">Next »</button>`;
    }

    paginationContainer.innerHTML = paginationHtml;

    document.querySelectorAll('.pagination-button').forEach(button => {
        button.addEventListener('click', function() {
            if (!this.disabled) {
                const page = parseInt(this.getAttribute('data-page'));
                displayTableWithPagination(checkins, page);
            }
        });
    });
}

// Handle no data or error scenario
function showNoData(message = 'No check-ins found for this tourist.') {
    document.querySelector('.tourist-cards').innerHTML = `<p>${message}</p>`;
    map.eachLayer(layer => {
        if (layer instanceof L.Marker || layer instanceof L.Polyline) {
            map.removeLayer(layer);
        }
    });
}

// Show loading state
function showLoading() {
    document.querySelector('.tourist-cards').innerHTML = '<p class="loading-message">Loading check-ins...</p>';
}

// Event listeners for search and filter
document.addEventListener('DOMContentLoaded', async () => {
    const searchInput = document.querySelector('.search-input');
    const filterSelect = document.querySelector('.filter-select');

    async function fetchAndDisplayData() {
        const touristId = searchInput.value.trim();
        const filter = filterSelect.value;
        if (touristId) {
            showLoading();
            const checkins = await fetchCheckins(touristId, filter);
            if (checkins && checkins.length > 0) {
                plotCheckins(checkins);
                displaySummary(checkins, touristId);
            } else {
                showNoData('No check-ins found');
            }
        } else {
            showNoData('Please enter a tourist ID.');
        }
    }

    searchInput.addEventListener('change', fetchAndDisplayData);
    filterSelect.addEventListener('change', fetchAndDisplayData);

    // Make sure map resizes correctly when the window resizes
    window.addEventListener('resize', () => {
        map.invalidateSize();
    });

    // Force map to render properly after DOM is fully loaded
    setTimeout(() => map.invalidateSize(), 100);

    $('#checkinModal').on('shown.bs.modal', function() {
        $(this).css({
            'display': 'flex',
            'align-items': 'center',
            'justify-content': 'center'
        });
    });
});
</script>
@endsection