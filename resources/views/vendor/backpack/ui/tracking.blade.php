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
        position: absolute !important;
        bottom: 20px !important;
        left: 20px !important;
        z-index: 1000 !important;
        background-color: #FF7E3F !important;
        color: white !important;
        border: none !important;
        border-radius: 4px !important;
        padding: 8px 15px !important;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 600 !important;
        cursor: pointer !important;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
    }
    
    .legend-toggle:hover {
        background-color: #E56E33;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    .legend-toggle i {
        margin-right: 5px;
    }
    
    /* Legend Container */
    .legends-container {
        position: absolute !important;
        bottom: 70px !important;
        left: 20px !important;
        z-index: 1001 !important;
        background-color: white !important;
        border-radius: 6px !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2) !important;
        max-width: 350px !important;
        max-height: 60vh !important;
        overflow-y: auto !important;
        padding: 15px !important;
        font-family: 'Poppins', sans-serif !important;
        display: none;
    }
    
    /* Tab styling */
    .tabs-container {
        margin-bottom: 15px;
    }
    
    .legend-tabs {
        display: flex !important;
        border-bottom: 1px solid #eee !important;
        margin-bottom: 10px !important;
    }
    
    .tab-btn {
        background: none !important;
        border: none !important;
        padding: 8px 12px !important;
        font-size: 13px !important;
        cursor: pointer !important;
        color: #555 !important;
        flex-grow: 1 !important;
        text-align: center !important;
        transition: all 0.2s ease;
    }
    
    .tab-btn:hover {
        background-color: #f5f5f5;
    }
    
    .tab-btn.active {
        color: #FF7E3F !important;
        border-bottom: 2px solid #FF7E3F !important;
        font-weight: 600 !important;
    }
    
    .legends-container h4 {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-top: 0;
        margin-bottom: 10px;
        border-bottom: 1px solid #eee;
        padding-bottom: 8px;
    }
    
    .legends-container h5 {
        font-size: 14px;
        font-weight: 500;
        color: #555;
        margin: 12px 0 8px;
    }
    
    .legend-item {
        display: flex !important;
        align-items: center !important;
        margin-bottom: 10px !important;
    }
    
    .legend-item span {
        font-size: 13px;
        color: #444;
    }
    
    .legend-section {
        margin-bottom: 15px;
    }
    
    .legend-section:last-child {
        margin-bottom: 0px;
    }
    
    .section-divider {
        height: 1px;
        background-color: #eee;
        margin: 15px 0;
    }
    
    /* Warning Zone Markers */
    .legend-marker {
        width: 24px;
        height: 24px;
        margin-right: 10px;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        flex-shrink: 0;
    }
    
    .legend-circle {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        margin-right: 10px;
        flex-shrink: 0;
    }
    
    .legend-polygon {
        width: 20px;
        height: 20px;
        margin-right: 10px;
        flex-shrink: 0;
    }
    
    /* Check-in Markers - Using original icons */
    .legend-marker.first-checkin {
        background-image: url('{{ asset('images/marker-icon-2x-green.png') }}');
    }
    
    .legend-marker.intermediate-checkin {
        background-image: url('{{ asset('images/marker-icon-2x-blue.png') }}');
    }
    
    .legend-marker.last-checkin {
        background-image: url('{{ asset('images/marker-icon-2x-red.png') }}');
    }
    
    /* Warning Zone Markers - Ensure original appearance */
    .legend-marker.danger-zone {
        background-image: url('{{ asset('images/warning-danger.png') }}');
    }
    .legend-marker.high-risk {
        background-image: url('{{ asset('images/warning-high-risk.png') }}');
    }
    .legend-marker.flood-area {
        background-image: url('{{ asset('images/warning-flood.png') }}');
    }
    .legend-marker.security-concern {
        background-image: url('{{ asset('images/warning-security.png') }}');
    }
    .legend-marker.other-warning {
        background-image: url('{{ asset('images/warning-other.png') }}');
    }
    
    .legend-path {
        width: 30px;
        height: 2px;
        background: linear-gradient(to right, #0066FF 50%, transparent 50%);
        background-size: 6px 100%;
        margin-right: 10px;
        flex-shrink: 0;
    }
    
    /* Map overlay adjustment */
    .leaflet-overlay-pane {
        z-index: 400 !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .legends-container {
            bottom: 10px !important;
            left: 10px !important;
            width: calc(100% - 20px) !important;
            max-height: 50vh !important;
        }
        .legend-toggle {
            bottom: 10px !important;
            left: 10px !important;
        }
    }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
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
            <button id="legend-toggle" class="legend-toggle">
                <i class="fa fa-map-signs"></i> Legend
            </button>

            <!-- Combined legends container -->
            <div id="legends-container" class="legends-container">
                <div class="tabs-container">
                    <div class="legend-tabs">
                        <button class="tab-btn active" data-tab="all">All</button>
                        <button class="tab-btn" data-tab="warning">Warning Zones</button>
                        <button class="tab-btn" data-tab="checkin">Check-ins</button>
                    </div>
                </div>
                
                <div class="legend-section warning-zones-legend" id="warning-tab">
                    <h4>Warning Zones</h4>
                    
                    <h5>Markers</h5>
                    <div class="legend-item">
                        <div class="legend-marker danger-zone"></div>
                        <span>Danger Zone - Red Marker</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker high-risk"></div>
                        <span>High Risk Area - Orange Marker</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker flood-area"></div>
                        <span>Flood Area - Blue Marker</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker security-concern"></div>
                        <span>Security Concern - Dark Red Marker</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker other-warning"></div>
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
                    
                    <h5>Polygons</h5>
                    <div class="legend-item">
                        <div class="legend-polygon" style="border: 2px solid #000; background-color: rgba(0, 0, 0, 0.2);"></div>
                        <span>Warning Zone Polygon</span>
                    </div>
                </div>
                
                <div class="section-divider"></div>
                
                <div class="legend-section checkin-legend" id="checkin-tab">
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
                                    <label class="radio-container">
                                        <input type="radio" name="drawing-mode" value="polygon">
                                        <span class="radio-label">Polygon</span>
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
                                <p>After pressing "Go to Map", draw the selected shape on the map. For polygons, click multiple points and double-click to finish.</p>
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
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger delete-warning-btn">Delete Warning</button>
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
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
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

// Add tile layer to the map
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Create layer groups for check-ins and warnings
const warningLayer = L.layerGroup().addTo(map);
const checkinLayer = L.layerGroup().addTo(map);

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
    }),
    'high-risk': L.icon({
        iconUrl: '{{ asset('images/warning-high-risk.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    }),
    'flood': L.icon({
        iconUrl: '{{ asset('images/warning-flood.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    }),
    'security': L.icon({
        iconUrl: '{{ asset('images/warning-security.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    }),
    'other': L.icon({
        iconUrl: '{{ asset('images/warning-other.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    })
};

// Circle and polygon styles based on warning type
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
        if (error) {
            console.error('Supabase query error:', error);
            throw new Error(`Failed to fetch check-ins: ${error.message}`);
        }
        console.log('Fetched check-ins:', data);
        return data;
    } catch (error) {
        console.error('Error fetching check-ins:', error);
        throw error;
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
    checkinLayer.clearLayers(); // Clear only check-in layers

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
        if (!latitude || !longitude) {
            console.warn(`Invalid coordinates for check-in at ${name}: latitude=${latitude}, longitude=${longitude}`);
            return;
        }

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
            .addTo(checkinLayer)
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
        }).addTo(checkinLayer);
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
    checkinLayer.clearLayers();
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
            try {
                const checkins = await fetchCheckins(touristId, filter);
                if (checkins && checkins.length > 0) {
                    plotCheckins(checkins);
                    displaySummary(checkins, touristId);
                } else {
                    showNoData('No check-ins found for this tourist ID.');
                }
            } catch (error) {
                showErrorModal(`Error fetching check-ins: ${error.message}`);
                showNoData('Failed to load check-ins. Please try again.');
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

    // Improved legend toggle functionality
    const legendToggle = document.getElementById('legend-toggle');
    const legendsContainer = document.getElementById('legends-container');
    
    legendToggle.addEventListener('click', function() {
        if (legendsContainer.style.display === 'block') {
            legendsContainer.style.display = 'none';
            legendToggle.innerHTML = '<i class="fa fa-map-signs"></i> Legend';
        } else {
            legendsContainer.style.display = 'block';
            // Reset to "All" tab when opening
            const allTabBtn = document.querySelector('.tab-btn[data-tab="all"]');
            if (allTabBtn) {
                document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                allTabBtn.classList.add('active');
                document.getElementById('warning-tab').style.display = 'block';
                document.getElementById('checkin-tab').style.display = 'block';
                document.querySelector('.section-divider').style.display = 'block';
            }
            legendToggle.innerHTML = '<i class="fa fa-map-signs"></i> Hide Legend';
        }
    });

    // Setup legend tabs
    setTimeout(setupLegendTabs, 500);

    // Load existing warning zones
    try {
        await loadWarningZones();
    } catch (error) {
        console.error('Failed to load warning zones on initialization:', error);
        showErrorModal(`Error loading warning zones: ${error.message}`);
    }
});

// Function to set up legend tabs
function setupLegendTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const warningTab = document.getElementById('warning-tab');
    const checkinTab = document.getElementById('checkin-tab');
    const divider = document.querySelector('.section-divider');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const tabName = this.getAttribute('data-tab');
            if (tabName === 'all') {
                warningTab.style.display = 'block';
                checkinTab.style.display = 'block';
                divider.style.display = 'block';
            } else if (tabName === 'warning') {
                warningTab.style.display = 'block';
                checkinTab.style.display = 'none';
                divider.style.display = 'none';
            } else if (tabName === 'checkin') {
                warningTab.style.display = 'none';
                checkinTab.style.display = 'block';
                divider.style.display = 'none';
            }
        });
    });
}

// Initialize warning elements
let drawHandler;
let selectedShape = null;
let currentDrawingType = 'marker';

// Add event listener for the "Add Warning Zone" button
document.getElementById('add-warning-btn').addEventListener('click', function() {
    resetWarningForm();
    $('#warningModal').modal('show');
});

// Add event listener for the "Go to Map" button
document.getElementById('go-to-map-btn').addEventListener('click', function() {
    const drawingType = document.querySelector('input[name="drawing-mode"]:checked').value;
    currentDrawingType = drawingType;
    if (drawingType === 'marker') {
        drawHandler = new L.Draw.Marker(map);
    } else if (drawingType === 'circle') {
        drawHandler = new L.Draw.Circle(map);
    } else if (drawingType === 'polygon') {
        drawHandler = new L.Draw.Polygon(map);
    }
    drawHandler.enable();
    $('#warningModal').modal('hide');
});

// Handle drawn shape
map.on('draw:created', function(e) {
    if (selectedShape) {
        map.removeLayer(selectedShape);
    }
    selectedShape = e.layer;
    map.addLayer(selectedShape);
    drawHandler.disable();
    document.getElementById('save-warning-btn').disabled = false;
    $('#warningModal').modal('show');
});

// Handle drawing cancellation
map.on('draw:canceled', function() {
    if (selectedShape) {
        map.removeLayer(selectedShape);
        selectedShape = null;
    }
    drawHandler.disable();
    $('#warningModal').modal('show');
});

// Handle save warning button click
document.getElementById('save-warning-btn').addEventListener('click', async function() {
    if (!selectedShape) {
        showErrorModal('Please draw a shape on the map.');
        return;
    }
    
    const warningType = document.getElementById('warning-type').value;
    const warningTitle = document.getElementById('warning-title').value.trim();
    const warningDescription = document.getElementById('warning-description').value.trim();
    
    if (!warningTitle) {
        showErrorModal('Please enter a title for the warning');
        return;
    }
    
    let warningData;
    if (currentDrawingType === 'marker') {
        const latlng = selectedShape.getLatLng();
        warningData = {
            type: warningType,
            title: warningTitle,
            description: warningDescription,
            latitude: latlng.lat,
            longitude: latlng.lng,
            shape_type: 'marker',
            created_at: new Date().toISOString()
        };
    } else if (currentDrawingType === 'circle') {
        const center = selectedShape.getLatLng();
        const radius = selectedShape.getRadius();
        warningData = {
            type: warningType,
            title: warningTitle,
            description: warningDescription,
            latitude: center.lat,
            longitude: center.lng,
            radius: radius,
            shape_type: 'circle',
            created_at: new Date().toISOString()
        };
    } else if (currentDrawingType === 'polygon') {
        const latlngs = selectedShape.getLatLngs()[0]; // Assuming simple polygon
        const polygonCoords = latlngs.map(point => [point.lat, point.lng]);
        warningData = {
            type: warningType,
            title: warningTitle,
            description: warningDescription,
            polygon_coords: polygonCoords,
            shape_type: 'polygon',
            created_at: new Date().toISOString()
        };
    }
    
    try {
        const { data, error } = await supabase.from('warning_zones').insert([warningData]).select();
        
        if (error) {
            console.error('Supabase error on insert:', error);
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
        if (error) {
            console.error('Supabase error on fetch warning zones:', error);
            throw new Error(`Failed to load warning zones: ${error.message}`);
        }
        
        console.log('Fetched warning zones:', data);
        if (data && data.length > 0) {
            warningLayer.clearLayers(); // Clear existing warnings to avoid duplicates
            data.forEach(warning => {
                addWarningToMap(warning);
            });
        } else {
            console.log('No warning zones found in the database.');
        }
    } catch (error) {
        console.error('Error loading warning zones:', error);
        throw error;
    }
}

// Add a warning to the map
function addWarningToMap(warning) {
    let warningElement;
    
    if (warning.shape_type === 'marker') {
        if (!warning.latitude || !warning.longitude) {
            console.warn(`Invalid coordinates for warning zone ${warning.title}: latitude=${warning.latitude}, longitude=${warning.longitude}`);
            return;
        }
        warningElement = L.marker([warning.latitude, warning.longitude], { icon: warningIcons[warning.type] || defaultWarningIcon });
    } else if (warning.shape_type === 'circle') {
        if (!warning.latitude || !warning.longitude || !warning.radius) {
            console.warn(`Invalid circle data for warning zone ${warning.title}: latitude=${warning.latitude}, longitude=${warning.longitude}, radius=${warning.radius}`);
            return;
        }
        warningElement = L.circle([warning.latitude, warning.longitude], {
            radius: warning.radius,
            ...circleStyles[warning.type]
        });
    } else if (warning.shape_type === 'polygon') {
        if (!warning.polygon_coords || !Array.isArray(warning.polygon_coords)) {
            console.warn(`Invalid polygon coordinates for warning zone ${warning.title}:`, warning.polygon_coords);
            return;
        }
        const latlngs = warning.polygon_coords.map(coord => {
            if (!coord || coord.length !== 2) {
                console.warn(`Invalid coordinate in polygon for warning zone ${warning.title}:`, coord);
                return null;
            }
            return [coord[0], coord[1]];
        }).filter(coord => coord !== null);
        if (latlngs.length < 3) {
            console.warn(`Not enough valid coordinates to form a polygon for warning zone ${warning.title}`);
            return;
        }
        warningElement = L.polygon(latlngs, {
            color: circleStyles[warning.type].color,
            fillColor: circleStyles[warning.type].fillColor,
            fillOpacity: circleStyles[warning.type].fillOpacity
        });
    } else {
        console.warn(`Unsupported shape type for warning zone ${warning.title}: ${warning.shape_type}`);
        return;
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
        <p><strong>Shape Type:</strong> ${warning.shape_type}</p>
        <p><strong>Created:</strong> ${new Date(warning.created_at).toLocaleString()}</p>
        ${warning.shape_type === 'marker' || warning.shape_type === 'circle' ? `<p><strong>Location:</strong> Lat: ${warning.latitude ? warning.latitude.toFixed(6) : 'N/A'}, Lng: ${warning.longitude ? warning.longitude.toFixed(6) : 'N/A'}</p>` : ''}
        ${warning.shape_type === 'circle' ? `<p><strong>Radius:</strong> ${warning.radius}m</p>` : ''}
    `;
    
    document.querySelector('.delete-warning-btn').setAttribute('data-id', warning.zone_id);
    $('#warningDetailsModal').modal('show');
}

// Handle warning deletion
document.querySelector('.delete-warning-btn').addEventListener('click', function() {
    const warningId = this.getAttribute('data-id');
    
    let warningToDelete = null;
    warningLayer.eachLayer(layer => {
        if (layer.warningData && layer.warningData.zone_id == warningId) {
            warningToDelete = layer.warningData;
        }
    });
    
    if (warningToDelete) {
        currentWarningToDelete = warningId;
        const confirmModal = document.getElementById('deleteWarningModal');
        confirmModal.querySelector('.warning-title').textContent = warningToDelete.title;
        confirmModal.querySelector('.warning-type').textContent = `Type: ${warningToDelete.type.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())}`;
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
    
    if (selectedShape) {
        map.removeLayer(selectedShape);
        selectedShape = null;
    }
    if (drawHandler) {
        drawHandler.disable();
    }
    currentDrawingType = 'marker';
    document.getElementById('save-warning-btn').disabled = true;
}

// Handle delete confirmation
document.getElementById('confirm-delete-btn').addEventListener('click', async function() {
    const warningId = currentWarningToDelete;
    try {
        const { error } = await supabase.from('warning_zones').delete().eq('zone_id', warningId);
        if (error) {
            console.error('Supabase error on delete:', error);
            throw new Error(`Failed to delete warning zone: ${error.message}`);
        }
        
        warningLayer.eachLayer(layer => {
            if (layer.warningData && layer.warningData.zone_id == warningId) {
                warningLayer.removeLayer(layer);
            }
        });
        
        $('#deleteWarningModal').modal('hide');
        showSuccessModal('Warning zone deleted successfully!');
    } catch (error) {
        console.error('Error deleting warning zone:', error);
        $('#deleteWarningModal').modal('hide');
        showErrorModal(`Failed to delete warning zone: ${error.message}`);
    }
});
</script>
@endsection