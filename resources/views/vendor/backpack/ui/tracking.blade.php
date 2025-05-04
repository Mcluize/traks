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
    /* Custom Modal Styles */
    .modal-header.success {
        background-color: #0BC8CA;
        color: white;
    }
    .modal-header.error {
        background-color: #FF7E3F;
        color: white;
    }
    /* Legend Toggle Button */
    .legend-toggle {
        position: absolute;
        bottom: 30px;
        left: 30px;
        z-index: 999;
        background-color: #FF7E3F;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 8px 15px;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }
    .legend-toggle:hover {
        background-color: #E56E33;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    /* Hide legends by default */
    .legend-section, .map-legend {
        display: none;
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
            <!-- Legend Toggle Button -->
            <button id="legend-toggle" class="legend-toggle">Legend</button>
            <!-- Warning Zones Legend -->
            <div class="legend-section warning-zones-legend">
                <h4>Warning Zones</h4>
                <h5>Markers</h5>
                <div class="legend-item">
                    <div class="legend-marker danger-zone" style="background-image: url('{{ asset('images/warning-danger.png') }}');"></div>
                    <span>Danger Zone - Red Marker</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker high-risk" style="background-image: url('{{ asset('images/warning-high-risk.png') }}');"></div>
                    <span>High Risk Area - Orange Marker</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker flood-area" style="background-image: url('{{ asset('images/warning-flood.png') }}');"></div>
                    <span>Flood Area - Blue Marker</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker security-concern" style="background-image: url('{{ asset('images/warning-security.png') }}');"></div>
                    <span>Security Concern - Dark Red Marker</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker other-warning" style="background-image: url('{{ asset('images/warning-other.png') }}');"></div>
                    <span>Other Warning - Purple Marker</span>
                </div>
                <h5>Circles</h5>
                <div class="legend-item">
                    <div class="legend-circle" style="border: 2px solid #FF0000; background-color: rgba(255, 0, 0, 0.2);"></div>
                    <span>Danger Zone - Red Circle</span>
                </div>
                <div class="legend-item">
                    <div class="legend-circle" style="border: 2px solid #FF6600; background-color: rgba(255, 102, 0, 0.2);"></div>
                    <span>High Risk Area - Orange Circle</span>
                </div>
                <div class="legend-item">
                    <div class="legend-circle" style="border: 2px solid #0066FF; background-color: rgba(0, 102, 255, 0.2);"></div>
                    <span>Flood Area - Blue Circle</span>
                </div>
                <div class="legend-item">
                    <div class="legend-circle" style="border: 2px solid #990000; background-color: rgba(153, 0, 0, 0.2);"></div>
                    <span>Security Concern - Dark Red Circle</span>
                </div>
                <div class="legend-item">
                    <div class="legend-circle" style="border: 2px solid #CC00CC; background-color: rgba(204, 0, 204, 0.2);"></div>
                    <span>Other Warning - Purple Circle</span>
                </div>
            </div>
            <!-- Check-in Legend -->
            <div class="map-legend">
                <h4>Check-in Legend</h4>
                <h5>Markers</h5>
                <div class="legend-item">
                    <div class="legend-marker first-checkin"></div>
                    <span>First Check-in - Green Marker</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker intermediate-checkin"></div>
                    <span>Intermediate Check-in - Blue Marker</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker last-checkin"></div>
                    <span>Last Check-in - Red Marker</span>
                </div>
                <h5>Paths</h5>
                <div class="legend-item">
                    <div class="legend-path"></div>
                    <span>Check-in Path - Blue Dashed Line</span>
                </div>
            </div>
            <!-- Warning Control -->
            <div class="warning-control">
                <button id="add-warning-btn" class="warning-btn">Add Warning Zone</button>
            </div>

            <!-- Warning Modal -->
            <div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="warningModalLabel" aria-hidden="true" data-backdrop="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="warningModalLabel">Add Warning Zone</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="warning-type">Warning Type</label>
                                <select id="warning-type" class="form-control">
                                    <option value="danger">Danger Zone</option>
                                    <option value="high-risk">High Risk Area</option>
                                    <option value="flood">Flood Area</option>
                                    <option value="security">Security Concern</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="warning-title">Title</label>
                                <input type="text" id="warning-title" class="form-control" placeholder="e.g., Flood Warning">
                            </div>
                            <div class="form-group">
                                <label for="warning-description">Description</label>
                                <textarea id="warning-description" class="form-control" rows="3" placeholder="Describe the warning or danger..."></textarea>
                            </div>
                            <div class="form-group">
                                <label>Drawing Mode</label>
                                <div class="drawing-options">
                                    <label class="radio-container">
                                        <input type="radio" name="drawing-mode" value="marker" checked>
                                        <span class="radio-label">Marker</span>
                                    </label>
                                    <label class="radio-container">
                                        <input type="radio" name="drawing-mode" value="circle">
                                        <span class="radio-label">Circle</span>
                                    </label>
                                </div>
                            </div>
                            <div id="circle-radius-container" style="display:none;">
                                <label for="circle-radius">Radius (meters)</label>
                                <input type="range" id="circle-radius" min="50" max="2000" value="200" step="50">
                                <span id="radius-value">200m</span>
                            </div>
                            <button id="go-to-map-btn" class="btn btn-info">Go to Map</button>
                            <div class="instructions">
                                <p>Click on the map to place your warning marker or circle after pressing "Go to Map".</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="cancel-warning-btn" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="save-warning-btn" disabled>Save Warning</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Warning Details Modal -->
            <div class="modal fade" id="warningDetailsModal" tabindex="-1" role="dialog" aria-labelledby="warningDetailsModalLabel" aria-hidden="true" data-backdrop="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="warningDetailsModalLabel">Warning Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body warning-details-content">
                            <!-- Content will be populated dynamically -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger delete-warning-btn">Delete Warning</button>
                            <button type="button" class="btn btn-secondary" id="close-warning-details-btn" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteWarningModal" tabindex="-1" role="dialog" aria-labelledby="deleteWarningModalLabel" aria-hidden="true" data-backdrop="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteWarningModalLabel">Confirm Deletion</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this warning zone?</p>
                            <p class="warning-title font-weight-bold"></p>
                            <p class="warning-type"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirm-delete-btn">Delete Warning</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Modal -->
            <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true" data-backdrop="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header success">
                            <h5 class="modal-title" id="successModalLabel">Success</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body" id="successMessage">
                            <!-- Success message will be populated dynamically -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Modal -->
            <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true" data-backdrop="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header error">
                            <h5 class="modal-title" id="errorModalLabel">Error</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body" id="errorMessage">
                            <!-- Error message will be populated dynamically -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
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

<!-- Modal for Table View -->
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
                <button type="button" class="btn btn-secondary" id="close-checkin-btn" data-dismiss="modal">Close</button>
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

// Define marker icons with fallbacks
const greenIcon = L.icon({
    iconUrl: '{{ asset('images/marker-icon-2x-green.png') }}',
    shadowUrl: '{{ asset('images/marker-shadow.png') }}',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const redIcon = L.icon({
    iconUrl: '{{ asset('images/marker-icon-2x-red.png') }}',
    shadowUrl: '{{ asset('images/marker-shadow.png') }}',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const blueIcon = L.icon({
    iconUrl: '{{ asset('images/marker-icon-2x-blue.png') }}',
    shadowUrl: '{{ asset('images/marker-shadow.png') }}',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

// Warning zone icons with fallbacks
const defaultWarningIcon = L.icon({
    iconUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png',
    shadowUrl: '{{ asset('images/marker-shadow.png') }}',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const warningIcons = {
    'danger': L.icon({
        iconUrl: '{{ asset('images/warning-danger.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    }).options.iconUrl ? L.icon({
        iconUrl: '{{ asset('images/warning-danger.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    }) : defaultWarningIcon,
    'high-risk': L.icon({
        iconUrl: '{{ asset('images/warning-high-risk.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    }).options.iconUrl ? L.icon({
        iconUrl: '{{ asset('images/warning-high-risk.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    }) : defaultWarningIcon,
    'flood': L.icon({
        iconUrl: '{{ asset('images/warning-flood.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    }).options.iconUrl ? L.icon({
        iconUrl: '{{ asset('images/warning-flood.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    }) : defaultWarningIcon,
    'security': L.icon({
        iconUrl: '{{ asset('images/warning-security.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    }).options.iconUrl ? L.icon({
        iconUrl: '{{ asset('images/warning-security.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    }) : defaultWarningIcon,
    'other': L.icon({
        iconUrl: '{{ asset('images/warning-other.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    }).options.iconUrl ? L.icon({
        iconUrl: '{{ asset('images/warning-other.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    }) : defaultWarningIcon
};

// Circle style based on warning type
const circleStyles = {
    'danger': { color: '#FF0000', fillColor: '#FF0000', fillOpacity: 0.2 },
    'high-risk': { color: '#FF6600', fillColor: '#FF6600', fillOpacity: 0.2 },
    'flood': { color: '#0066FF', fillColor: '#0066FF', fillOpacity: 0.2 },
    'security': { color: '#990000', fillColor: '#990000', fillOpacity: 0.2 },
    'other': { color: '#CC00CC', fillColor: '#CC00CC', fillOpacity: 0.2 }
};

// Ensure map fills container
setTimeout(function() {
    map.invalidateSize();
}, 100);

// Fetch check-ins from Supabase with corrected query
async function fetchCheckins(touristId, filter) {
    try {
        let query = supabase
            .from('checkins')
            .select(`
                timestamp,
                tourist_spots:spot_id (
                    spot_id,
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
            const dayOfWeek = now.getDay();
            const startOfWeek = new Date(now);
            startOfWeek.setDate(now.getDate() - (dayOfWeek === 0 ? 6 : dayOfWeek - 1));
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

// Create curved path using quadratic Bezier curve
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

// Plot check-ins on the map
function plotCheckins(checkins) {
    map.eachLayer(layer => {
        if (layer instanceof L.Marker || layer instanceof L.Polyline) {
            map.removeLayer(layer);
        }
    });

    const spotCounts = {};
    checkins.forEach(checkin => {
        const spotId = checkin.tourist_spots.spot_id;
        if (!spotCounts[spotId]) {
            spotCounts[spotId] = 0;
        }
        spotCounts[spotId]++;
    });

    const pathCoordinates = [];
    checkins.forEach((checkin, index) => {
        const { latitude, longitude, name, spot_id } = checkin.tourist_spots;
        let markerIcon;

        if (index === 0) {
            markerIcon = greenIcon;
        } else if (index === checkins.length - 1) {
            markerIcon = redIcon;
        } else {
            markerIcon = blueIcon;
        }

        const count = spotCounts[spot_id];
        const marker = L.marker([latitude, longitude], { icon: markerIcon })
            .addTo(map)
            .bindPopup(`<b>${name}</b><br>Check-in Time: ${new Date(checkin.timestamp).toLocaleString()}<br>Total Check-ins at this spot: ${count}`);

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

// Functions to show modals
function showSuccessModal(message) {
    document.getElementById('successMessage').textContent = message;
    $('#successModal').modal('show');
}

function showErrorModal(message) {
    document.getElementById('errorMessage').textContent = message;
    $('#errorModal').modal('show');
}

// Event listeners for search, filter, and legend toggle
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

    window.addEventListener('resize', () => {
        map.invalidateSize();
    });

    setTimeout(() => map.invalidateSize(), 100);

    $('#checkinModal').on('shown.bs.modal', function() {
        $(this).css({
            'display': 'flex',
            'align-items': 'center',
            'justify-content': 'center'
        });
    });

    // Ensure proper modal cleanup on hide
    $('#warningDetailsModal').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });

    // Explicitly handle close button clicks for all modals
    document.querySelectorAll('.modal .close, .modal .btn[data-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.closest('.modal').id;
            $(`#${modalId}`).modal('hide');
        });
    });

    // Legend toggle functionality
    const legendToggle = document.getElementById('legend-toggle');
    const warningLegend = document.querySelector('.warning-zones-legend');
    const checkinLegend = document.querySelector('.map-legend');

    legendToggle.addEventListener('click', function() {
        if (warningLegend.style.display === 'none' && checkinLegend.style.display === 'none') {
            warningLegend.style.display = 'block';
            checkinLegend.style.display = 'block';
        } else {
            warningLegend.style.display = 'none';
            checkinLegend.style.display = 'none';
        }
    });
});

// Initialize warning elements
let drawingMode = false;
let currentDrawingType = 'marker';
let tempMarker = null;
let tempCircle = null;
let selectedLocation = null;
let selectedRadius = 200;
let warningLayer = L.layerGroup().addTo(map);

// Add event listener for the "Add Warning Zone" button
document.getElementById('add-warning-btn').addEventListener('click', function() {
    $('#warningModal').modal('show');
});

// Add event listener for the "Go to Map" button
document.getElementById('go-to-map-btn').addEventListener('click', function() {
    drawingMode = true;
    currentDrawingType = document.querySelector('input[name="drawing-mode"]:checked').value;
    if (currentDrawingType === 'circle') {
        selectedRadius = parseInt(document.getElementById('circle-radius').value);
    }
    $('#warningModal').modal('hide');
});

// Handle radius input change
document.getElementById('circle-radius').addEventListener('input', function() {
    const radius = parseInt(this.value);
    document.getElementById('radius-value').textContent = radius + 'm';
    selectedRadius = radius;
    
    if (tempCircle && selectedLocation) {
        tempCircle.setRadius(radius);
    }
});

// Handle drawing mode selection
document.querySelectorAll('input[name="drawing-mode"]').forEach(input => {
    input.addEventListener('change', function() {
        currentDrawingType = this.value;
        if (currentDrawingType === 'circle') {
            document.getElementById('circle-radius-container').style.display = 'block';
        } else {
            document.getElementById('circle-radius-container').style.display = 'none';
        }
        
        // Reset temp markers/circles
        if (tempMarker) {
            map.removeLayer(tempMarker);
            tempMarker = null;
        }
        if (tempCircle) {
            map.removeLayer(tempCircle);
            tempCircle = null;
        }
        selectedLocation = null;
        document.getElementById('save-warning-btn').disabled = true;
    });
});

// Handle map click events for placing warnings
map.on('click', function(e) {
    if (!drawingMode) return;
    
    console.log('Map clicked at:', e.latlng);
    selectedLocation = e.latlng;
    
    if (tempMarker) {
        map.removeLayer(tempMarker);
        tempMarker = null;
    }
    if (tempCircle) {
        map.removeLayer(tempCircle);
        tempCircle = null;
    }
    
    const warningType = document.getElementById('warning-type').value;
    
    try {
        if (currentDrawingType === 'marker') {
            tempMarker = L.marker(e.latlng, { icon: warningIcons[warningType] }).addTo(map);
            console.log('Marker added with icon:', warningIcons[warningType].options.iconUrl);
        } else if (currentDrawingType === 'circle') {
            tempCircle = L.circle(e.latlng, {
                radius: selectedRadius,
                ...circleStyles[warningType]
            }).addTo(map);
            console.log('Circle added with radius:', selectedRadius);
        }
    } catch (error) {
        console.error('Error adding marker/circle:', error);
    }
    
    document.getElementById('save-warning-btn').disabled = false;
    $('#warningModal').modal('show');
    drawingMode = false;
});

// Handle save warning button click
document.getElementById('save-warning-btn').addEventListener('click', async function() {
    if (!selectedLocation) {
        showErrorModal('Please select a location on the map.');
        return;
    }
    
    const warningType = document.getElementById('warning-type').value;
    const warningTitle = document.getElementById('warning-title').value.trim();
    const warningDescription = document.getElementById('warning-description').value.trim();
    
    if (!warningTitle) {
        showErrorModal('Please enter a title for the warning');
        return;
    }
    
    const warningData = {
        type: warningType,
        title: warningTitle,
        description: warningDescription,
        latitude: selectedLocation.lat,
        longitude: selectedLocation.lng,
        radius: currentDrawingType === 'circle' ? selectedRadius : null,
        shape_type: currentDrawingType,
        created_at: new Date().toISOString()
    };
    
    console.log('Saving warning with data:', warningData);
    
    try {
        const { data, error } = await supabase.from('warning_zones').insert([warningData]).select();
        
        if (error) {
            console.error('Supabase error:', error.message, error.details, error.hint);
            throw new Error(`Supabase error: ${error.message}`);
        }
        
        console.log('Warning saved successfully:', data[0]);
        addWarningToMap(data[0]);
        
        resetWarningForm();
        $('#warningModal').modal('hide');
        showSuccessModal('Warning zone added successfully!');
    } catch (error) {
        console.error('Error saving warning zone:', error);
        showErrorModal(`Failed to save warning zone: ${error.message}. Check console for details.`);
    }
});

// Load existing warning zones when map initializes
async function loadWarningZones() {
    try {
        const { data, error } = await supabase.from('warning_zones').select('*');
        if (error) throw error;
        
        if (data && data.length > 0) {
            data.forEach(warning => {
                addWarningToMap(warning);
            });
        }
    } catch (error) {
        console.error('Error loading warning zones:', error);
    }
}

// Add a warning to the map
function addWarningToMap(warning) {
    let warningElement;
    
    if (warning.shape_type === 'marker') {
        warningElement = L.marker([warning.latitude, warning.longitude], { icon: warningIcons[warning.type] });
    } else if (warning.shape_type === 'circle') {
        warningElement = L.circle([warning.latitude, warning.longitude], {
            radius: warning.radius,
            ...circleStyles[warning.type]
        });
    }
    
    warningElement.bindPopup(`<b>${warning.title}</b><br><button class="view-details-btn" data-id="${warning.zone_id}">View Details</button>`);
    warningElement.addTo(warningLayer);
    warningElement.warningData = warning;
    
    warningElement.on('popupopen', function() {
        setTimeout(() => {
            document.querySelectorAll('.view-details-btn').forEach(btn => {
                if (btn.getAttribute('data-id') == warning.zone_id) {
                    btn.addEventListener('click', function() {
                        showWarningDetails(warning);
                    });
                }
            });
        }, 10);
    });
}

// Show warning details in modal
function showWarningDetails(warning) {
    const content = document.querySelector('.warning-details-content');
    content.innerHTML = `
        <h4>${warning.title}</h4>
        <p class="warning-type ${warning.type}"><strong>Type:</strong> ${warning.type.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
        <p><strong>Description:</strong> ${warning.description || 'No description provided'}</p>
        <p><strong>Created:</strong> ${new Date(warning.created_at).toLocaleString()}</p>
        <p><strong>Location:</strong> Lat: ${warning.latitude.toFixed(6)}, Lng: ${warning.longitude.toFixed(6)}</p>
        ${warning.shape_type === 'circle' ? `<p><strong>Radius:</strong> ${warning.radius}m</p>` : ''}
    `;
    
    document.querySelector('.delete-warning-btn').setAttribute('data-id', warning.zone_id);
    $('#warningDetailsModal').modal('show');
}

// Handle warning deletion
document.querySelector('.delete-warning-btn').addEventListener('click', function() {
    const warningId = this.getAttribute('data-id');
    
    // Find the warning data
    let warningToDelete = null;
    warningLayer.eachLayer(layer => {
        if (layer.warningData && layer.warningData.zone_id == warningId) {
            warningToDelete = layer.warningData;
        }
    });
    
    if (warningToDelete) {
        // Store the warning ID for later use
        currentWarningToDelete = warningId;
        
        // Populate the confirmation modal with warning details
        const confirmModal = document.getElementById('deleteWarningModal');
        confirmModal.querySelector('.warning-title').textContent = warningToDelete.title;
        confirmModal.querySelector('.warning-type').textContent = `Type: ${warningToDelete.type.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())}`;
        
        // Hide the details modal and show the confirmation modal
        $('#warningDetailsModal').modal('hide');
        $('#deleteWarningModal').modal('show');
    }
});

// Reset the warning form
function resetWarningForm() {
    document.getElementById('warning-title').value = '';
    document.getElementById('warning-description').value = '';
    document.getElementById('warning-type').value = 'danger';
    document.querySelector('input[name="drawing-mode"][value="marker"]').checked = true;
    document.getElementById('circle-radius-container').style.display = 'none';
    document.getElementById('circle-radius').value = 200;
    document.getElementById('radius-value').textContent = '200m';
    
    if (tempMarker) {
        map.removeLayer(tempMarker);
        tempMarker = null;
    }
    if (tempCircle) {
        map.removeLayer(tempCircle);
        tempCircle = null;
    }
    selectedLocation = null;
    drawingMode = false;
    currentDrawingType = 'marker';
    document.getElementById('save-warning-btn').disabled = true;
}

// Handle Cancel button functionality for Warning Modal
document.getElementById('cancel-warning-btn').addEventListener('click', function() {
    resetWarningForm();
    $('#warningModal').modal('hide');
});

// Modal close events for additional cleanup
$('#warningModal').on('hidden.bs.modal', function () {
    resetWarningForm();
});

// Initialize warning zones on page load
document.addEventListener('DOMContentLoaded', function() {
    loadWarningZones();
});

// Handle warning deletion - Updated code
let currentWarningToDelete = null;

document.getElementById('confirm-delete-btn').addEventListener('click', async function() {
    if (!currentWarningToDelete) return;
    
    try {
        const { error } = await supabase.from('warning_zones').delete().eq('zone_id', currentWarningToDelete);
        if (error) throw error;
        
        warningLayer.eachLayer(layer => {
            if (layer.warningData && layer.warningData.zone_id == currentWarningToDelete) {
                warningLayer.removeLayer(layer);
            }
        });
        
        $('#deleteWarningModal').modal('hide');
        currentWarningToDelete = null;
        showSuccessModal('Warning deleted successfully!');
    } catch (error) {
        console.error('Error deleting warning:', error);
        showErrorModal('Failed to delete warning. Please try again.');
    }
});
</script>
@endsection