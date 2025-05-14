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
    
    /* Check-in Markers */
    .legend-marker.first-checkin {
        background-image: url('{{ asset('images/marker-icon-2x-green.png') }}');
    }
    
    .legend-marker.intermediate-checkin {
        background-image: url('{{ asset('images/marker-icon-2x-blue.png') }}');
    }
    
    .legend-marker.last-checkin {
        background-image: url('{{ asset('images/marker-icon-2x-red.png') }}');
    }
    
    .legend-marker.current-location {
        background-image: url('{{ asset('images/marker-icon-2x-yellow.png') }}');
    }
    
    /* Warning Zone Markers */
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
    
    /* User Zone Markers */
    .legend-marker.road-blocked {
        background-image: url('{{ asset('images/road-blocked.png') }}');
    }
    .legend-marker.flooded {
        background-image: url('{{ asset('images/flooded.png') }}');
    }
    .legend-marker.landslide {
        background-image: url('{{ asset('images/landslide.png') }}');
    }
    .legend-marker.fire {
        background-image: url('{{ asset('images/fire.png') }}');
    }
    .legend-marker.others {
        background-image: url('{{ asset('images/others.png') }}');
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
    
    /* Change Year Button Styling */
    #edit-year-btn {
        background-color: #FF7E3F;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 5px 10px;
        font-family: 'Poppins', sans-serif;
        font-size: 12px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    
    #edit-year-btn:hover {
        background-color: #E56E33;
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

    /* Custom Button Styles for Warning Modals */
    .modal-footer .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        transition: none;
    }
    .modal-footer .btn-secondary:hover,
    .modal-footer .btn-secondary:focus,
    .modal-footer .btn-secondary:active {
        background-color: #6c757d;
        border-color: #6c757d;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    .modal-footer .btn-warning {
        background-color: #FF7E3F !important;
        border-color: #FF7E3F;
        transition: none;
    }
    .modal-footer .btn-warning:hover,
    .modal-footer .btn-warning:focus,
    .modal-footer .btn-warning:active {
        background-color: #FF7E3F;
        border-color: #FF7E3F;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
    }
    .custom-cancel-btn {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        transition: none !important;
        color: white !important;
    }
    .custom-cancel-btn:hover,
    .custom-cancel-btn:focus,
    .custom-cancel-btn:active {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
    }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
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
                        <button class="tab-btn" data-tab="user-zones">User Zones</button>
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
                
                <!-- Updated User Zones Legend Tab with Clustering Symbol -->
                <div class="legend-section user-zones-legend" id="user-zones-tab">
                    <h4>User Zones</h4>
                    <h5>Markers</h5>
                    <div class="legend-item">
                        <div class="legend-marker road-blocked"></div>
                        <span>Road Blocked</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker flooded"></div>
                        <span>Flooded</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker landslide"></div>
                        <span>Landslide</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker fire"></div>
                        <span>Fire</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-marker others"></div>
                        <span>Others</span>
                    </div>
                    <!-- Clustering Symbol -->
                    <h5>Clusters</h5>
                    <div class="legend-item">
                        <div class="legend-cluster" style="width: 24px; height: 24px; background-color: green; border-radius: 50%; text-align: center; line-height: 24px; color: white; font-size: 12px; margin-right: 10px;">?</div>
                        <span>Clustered User Zones (Number of Total Votes)</span>
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
                    <div class="legend-item">
                        <div class="legend-marker current-location"></div>
                        <span>Current Location - Yellow Marker</span>
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
                                <label for="warning-zone-tag">Zone Tag</label>
                                <input type="text" id="warning-zone-tag" class="form-control" placeholder="e.g., Flood Warning">
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
                            <button type="button" class="btn btn-secondary custom-cancel-btn" id="cancel-warning-btn" data-dismiss="modal">Cancel</button>
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
                            <button type="button" class="btn btn-secondary custom-cancel-btn" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-warning deactivate-warning-btn">Deactivate Zone</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deactivate Confirmation Modal -->
            <div class="modal fade" id="deactivateWarningModal" tabindex="-1" role="dialog" aria-labelledby="deactivateWarningModalLabel" aria-hidden="true" data-backdrop="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deactivateWarningModalLabel">Confirm Deactivation</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to deactivate this warning zone?</p>
                            <p class="warning-title font-weight-bold"></p>
                            <p class="warning-type"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary custom-cancel-btn" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-warning" id="confirm-deactivate-btn">Deactivate Zone</button>
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
                            <button type="button" class="btn btn-secondary custom-cancel-btn" data-dismiss="modal">Close</button>
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
                            <button type="button" class="btn btn-secondary custom-cancel-btn" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal for PIN Input with Cancel Button -->
            <div class="modal fade" id="locationPinModal" tabindex="-1" role="dialog" aria-labelledby="locationPinModalLabel" aria-hidden="true" data-backdrop="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #0BC8CA; color: #fff;">
                            <h5 class="modal-title" id="locationPinModalLabel">Enter PIN to View Current Location</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="password" id="pinInput" class="form-control mb-3" placeholder="••••••" maxlength="6">
                            <div id="pinError" class="text-danger" style="display:none;">Incorrect PIN.</div>
                            <div class="mt-3">
                                <button id="changePinBtn" class="btn btn-warning">Change PIN</button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary custom-cancel-btn" id="cancelPinModal" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="unlockLocationBtn" style="background-color: #FF7E3F;">Unlock</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal for Change PIN -->
            <div class="modal fade" id="changePinModal" tabindex="-1" role="dialog" aria-labelledby="changePinModalLabel" aria-hidden="true" data-backdrop="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #FF7E3F; color: #fff;">
                            <h5 class="modal-title" id="changePinModalLabel">Change Your PIN</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="password" id="newPinInput" class="form-control mb-3" placeholder="Enter New PIN" maxlength="6">
                            <div id="pinChangeError" class="text-danger" style="display:none;">Failed to update PIN.</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary custom-cancel-btn" id="cancelPinChange" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" id="saveNewPinBtn">Save New PIN</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom Year Modal -->
            <div class="modal fade" id="customYearModal" tabindex="-1" role="dialog" aria-labelledby="customYearModalLabel" aria-hidden="true" data-backdrop="false">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="customYearModalLabel">Select Custom Year</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="customYearForm">
                                <div class="form-group">
                                    <label for="yearInput">Enter Year</label>
                                    <input type="number" class="form-control" id="yearInput" placeholder="e.g., 2023" min="1900" max="2100">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary custom-cancel-btn" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="applyYearBtn">Apply</button>
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
                            <option value="custom_year">Custom Year</option>
                            <option value="this_year">This Year</option>
                        </select>
                    </div>
                    <button id="edit-year-btn" style="display:none; margin-top: 10px; margin-left:252px;">Change Year</button>
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
                <button type="button" class="btn btn-secondary custom-cancel-btn" id="close-checkin-btn" data-dismiss="modal">Close</button>
            </div>
        </div>
    </ optiodiv>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
<script>
// Define Supabase configuration
const supabaseUrl = '{{ config('services.supabase.url') }}';
const supabaseKey = '{{ config('services.supabase.key') }}';

// Validate Supabase configuration
if (!supabaseUrl || !supabaseKey) {
    console.error('Supabase configuration is missing. Please check your .env file.');
    document.body.innerHTML = '<p>Error: Supabase configuration is missing. Please contact the administrator.</p>';
    throw new Error('Supabase configuration missing');
}

// Initialize Supabase client
const supabase = window.supabase.createClient(supabaseUrl, supabaseKey);

// Initialize Leaflet map
const map = L.map('map', {
    zoomControl: true,
    attributionControl: true,
}).setView([7.0767, 125.8259], 13);

// Add tile layer to the map
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Create layer groups for check-ins, warnings, and user zones
const warningLayer = L.layerGroup().addTo(map);
const checkinLayer = L.layerGroup().addTo(map);
const userZonesLayer = L.markerClusterGroup({
    spiderfyOnMaxZoom: true,
    showCoverageOnHover: false,
    zoomToBoundsOnClick: true,
    iconCreateFunction: function(cluster) {
        const markers = cluster.getAllChildMarkers();
        const totalVotes = markers.reduce((sum, marker) => sum + (marker.zoneData.total_weight || 0), 0);
        return L.divIcon({
            html: `<div style="background-color: green; border-radius: 50%; width: 30px; height: 30px; text-align: center; line-height: 30px; color: white;">${totalVotes}</div>`,
            className: 'custom-cluster-icon',
            iconSize: [30, 30]
        });
    }
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

// Current Location Icon
const currentLocationIcon = L.icon({
    iconUrl: '{{ asset('images/marker-icon-2x-yellow.png') }}',
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

// User zone icons
const userZoneIcons = {
    'Road Blocked': L.icon({
        iconUrl: '{{ asset('images/road-blocked.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    }),
    'Flooded': L.icon({
        iconUrl: '{{ asset('images/flooded.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    }),
    'Landslide': L.icon({
        iconUrl: '{{ asset('images/landslide.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    }),
    'Fire': L.icon({
        iconUrl: '{{ asset('images/fire.png') }}',
        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
        shadowSize: [41, 41]
    }),
    'Others': L.icon({
        iconUrl: '{{ asset('images/others.png') }}',
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

// Variable to store the current tourist ID
let currentTouristId;

// Variable to store the interval ID for location updates
let locationUpdateInterval = null;

// Variables to store the current location marker and data
let currentLocationMarker = null;
let currentLocationData = null;

// State to track if location is visible
let isLocationVisible = false;

// Variables to manage filter state
let currentFilter = 'all_time';
let selectedCustomYear = null;

// Map to keep track of markers by zone_id
const markerMap = new Map();

// Function to check if tourist ID is valid (user_type = 'user')
async function isValidTourist(touristId) {
    try {
        const { data, error } = await supabase
            .from('users')
            .select('user_id')
            .eq('user_id', touristId)
            .eq('user_type', 'user');
        
        if (error) {
            throw error;
        }
        return data.length > 0;
    } catch (error) {
        console.error('Error checking tourist validity:', error);
        return false;
    }
}

// Fetch latest location from Supabase
async function fetchLatestLocation(touristId) {
    try {
        const { data, error } = await supabase
            .from('live_locations')
            .select('latitude, longitude, updated_at')
            .eq('user_id', touristId)
            .order('updated_at', { ascending: false })
            .limit(1);
        
        if (error) {
            throw error;
        }
        if (data.length === 0) {
            return null;
        }
        return data[0];
    } catch (error) {
        console.error('Error fetching latest location:', error);
        throw error;
    }
}

// Fetch check-ins from Supabase
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
        } else if (filter === 'this_year') {
            const startOfYear = new Date(now.getFullYear(), 0, 1).toISOString();
            query = query.gte('timestamp', startOfYear);
        } else if (filter === 'custom_year') {
            if (!selectedCustomYear) {
                throw new Error('Custom year not selected');
            }
            const startOfYear = new Date(selectedCustomYear, 0, 1).toISOString();
            const endOfYear = new Date(selectedCustomYear, 11, 31, 23, 59, 59).toISOString();
            query = query.gte('timestamp', startOfYear).lte('timestamp', endOfYear);
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
    checkinLayer.clearLayers();

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

    if (pathCoordinates.length > 0 && !isLocationVisible) {
        const bounds = L.latLngBounds(pathCoordinates);
        map.fitBounds(bounds);
    }
}

// Display summary with table view and current location buttons
async function displaySummary(checkins, touristId) {
    const firstCheckin = checkins.length > 0 ? new Date(checkins[0].timestamp).toLocaleString() : 'N/A';
    const lastCheckin = checkins.length > 0 ? new Date(checkins[checkins.length - 1].timestamp).toLocaleString() : 'N/A';
    const lastLocation = checkins.length > 0 ? checkins[checkins.length - 1].tourist_spots.name : 'N/A';

    let locationButtonHtml = '';
    const locationExists = await fetchLatestLocation(touristId);
    if (locationExists) {
        locationButtonHtml = isLocationVisible 
            ? `<button class="view-table-button" id="hide-current-location-btn">Hide Current Location</button>`
            : `<button class="view-table-button" id="view-current-location-btn">View Current Location</button>`;
    }

    const summaryHtml = `
        <p><strong>Tourist ID:</strong> ${touristId} ${locationButtonHtml}</p>
        <p><strong>Number of Check-ins:</strong> ${checkins.length} ${checkins.length > 0 ? '<button class="view-table-button" data-toggle="modal" data-target="#checkinModal">View Table</button>' : ''}</p>
        ${checkins.length === 0 ? '<p>No check-ins found for this tourist.</p>' : ''}
        <p><strong>First Check-in:</strong> ${firstCheckin}</p>
        <p><strong>Last Check-in:</strong> ${lastCheckin}</p>
        <p><strong>Last Location:</strong> ${lastLocation}</p>
    `;
    document.querySelector('.tourist-cards').innerHTML = summaryHtml;

    window.checkinsData = checkins;

    if (checkins.length > 0) {
        document.querySelector('[data-target="#checkinModal"]').addEventListener('click', () => {
            displayTableWithPagination(checkins, 1);
        });
    }

    const locationBtn = document.getElementById('view-current-location-btn') || document.getElementById('hide-current-location-btn');
    if (locationBtn) {
        locationBtn.addEventListener('click', async () => {
            if (isLocationVisible) {
                if (locationUpdateInterval) {
                    clearInterval(locationUpdateInterval);
                    locationUpdateInterval = null;
                }
                if (currentLocationMarker) {
                    checkinLayer.removeLayer(currentLocationMarker);
                    currentLocationMarker = null;
                }
                isLocationVisible = false;
                displaySummary(checkins, touristId);
            } else {
                currentTouristId = touristId;
                $('#locationPinModal').modal('show');
            }
        });
    }
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

// Function to refresh user zones
async function refreshUserZones() {
    try {
        const { data, error } = await supabase.from('user_zones').select('*').neq('status', 'inactive');
        if (error) throw error;

        const newZoneIds = new Set(data.map(zone => zone.zone_id));

        // Remove markers that are no longer in the data
        markerMap.forEach((marker, zoneId) => {
            if (!newZoneIds.has(zoneId)) {
                userZonesLayer.removeLayer(marker);
                markerMap.delete(zoneId);
            }
        });

        data.forEach(zone => {
            if (zone.latitude && zone.longitude) {
                if (markerMap.has(zone.zone_id)) {
                    const marker = markerMap.get(zone.zone_id);
                    // Update marker data if changed
                    if (JSON.stringify(marker.zoneData) !== JSON.stringify(zone)) {
                        marker.zoneData = zone;
                        marker.setPopupContent(getPopupContent(zone));
                        // If position changed, update position
                        if (marker.getLatLng().lat !== zone.latitude || marker.getLatLng().lng !== zone.longitude) {
                            marker.setLatLng([zone.latitude, zone.longitude]);
                        }
                    }
                } else {
                    // Add new marker
                    const markerIcon = userZoneIcons[zone.type] || L.icon({
                        iconUrl: '{{ asset('images/others.png') }}',
                        shadowUrl: '{{ asset('images/marker-shadow.png') }}',
                        iconSize: [32, 32],
                        iconAnchor: [16, 32],
                        popupAnchor: [0, -32],
                        shadowSize: [41, 41]
                    });
                    const marker = L.marker([zone.latitude, zone.longitude], { icon: markerIcon });
                    marker.zoneData = zone;
                    marker.addTo(userZonesLayer).bindPopup(getPopupContent(zone));
                    markerMap.set(zone.zone_id, marker);
                }
            }
        });

        // Refresh cluster icons
        userZonesLayer.refreshClusters();
    } catch (error) {
        console.error('Error refreshing user zones:', error);
        showErrorModal('Failed to refresh user zones: ' + error.message);
    }
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// Debounced refresh function
const debouncedRefreshUserZones = debounce(refreshUserZones, 1000);

// Load and plot user zones initially with real-time subscription
async function loadUserZones() {
    try {
        await refreshUserZones();

        // Subscribe to real-time updates for user zones
        supabase
            .channel('user_zones_channel')
            .on('postgres_changes', { event: '*', schema: 'public', table: 'user_zones' }, payload => {
                console.log('Received real-time event:', payload);
                debouncedRefreshUserZones();
            })
            .subscribe();

        // Periodic refresh every 5 seconds
        setInterval(refreshUserZones, 5000);
    } catch (error) {
        console.error('Error loading user zones:', error);
        showErrorModal('Failed to load user zones: ' + error.message);
    }
}

// Helper function to generate popup content
function getPopupContent(zone) {
    let content = `
        <b>Type:</b> ${zone.type}<br>
        <b>Description:</b> ${zone.description || 'N/A'}<br>
        <b>Reported by:</b> ${zone.user_id}<br>
        <b>Votes:</b> ${zone.total_weight || 0}<br>
        <b>Status:</b> ${zone.status}<br>
        <b>Reported on:</b> ${new Date(zone.created_at).toLocaleString()}
    `;
    if (zone.status === 'pending') {
        content += `
            <div class="admin-controls" style="margin-top: 10px;">
                <button class="btn btn-success btn-sm" onclick="verifyZone('${zone.zone_id}')">Verify</button>
                <button class="btn btn-danger btn-sm" onclick="removeZone('${zone.zone_id}')">Remove</button>
            </div>
        `;
    } else if (zone.status === 'verified') {
        content += `
            <div class="admin-controls" style="margin-top: 10px;">
                <button class="btn btn-secondary btn-sm" onclick="cancelAction()">Cancel</button>
                <button class="btn btn-sm custom-deactivate-btn" onclick="deactivateZone('${zone.zone_id}')">Deactivate Zone</button>
            </div>
        `;
    }
    return content;
}

// Verify user zone
async function verifyZone(zoneId) {
    try {
        const { error } = await supabase.from('user_zones').update({ status: 'verified' }).eq('zone_id', zoneId);
        if (error) throw error;
        showSuccessModal('Zone verified successfully');
    } catch (error) {
        console.error('Error verifying zone:', error);
        showErrorModal('Failed to verify zone: ' + error.message);
    }
}

// Remove user zone
async function removeZone(zoneId) {
    try {
        const { error } = await supabase.from('user_zones').update({ status: 'removed' }).eq('zone_id', zoneId);
        if (error) throw error;
        showSuccessModal('Zone removed successfully');
    } catch (error) {
        console.error('Error removing zone:', error);
        showErrorModal('Failed to remove zone: ' + error.message);
    }
}

// Cancel action for user zone
function cancelAction() {
    map.closePopup();
}

// Deactivate user zone
// Replace the existing deactivateZone function
async function deactivateZone(zoneId) {
    try {
        const { error } = await supabase.from('user_zones').update({ status: 'inactive' }).eq('zone_id', zoneId);
        if (error) throw error;

        
        const marker = markerMap.get(zoneId);
        if (marker) {
            marker.setIcon(L.divIcon({
                html: '<div class="deactivated-zone">X</div>',
                className: 'deactivated-zone-icon',
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            }));
            marker.zoneData.status = 'inactive';
            marker.bindPopup(getPopupContent(marker.zoneData)); 
        }

        showSuccessModal('Zone deactivated successfully');
    } catch (error) {
        console.error('Error deactivating zone:', error);
        showErrorModal('Failed to deactivate zone: ' + error.message);
    }
}

// Event listeners for search, filter, legend toggle, and PIN modals
document.addEventListener('DOMContentLoaded', async () => {
    const searchInput = document.querySelector('.search-input');
    const filterSelect = document.querySelector('.filter-select');

    function updateChangeYearButtonVisibility() {
        if (currentFilter === 'custom_year' && selectedCustomYear) {
            document.getElementById('edit-year-btn').style.display = 'block';
        } else {
            document.getElementById('edit-year-btn').style.display = 'none';
        }
    }

    async function fetchAndDisplayData() {
        const touristIdInput = searchInput.value.trim();
        const filter = currentFilter;
        if (!touristIdInput) {
            showNoData('Please enter a tourist ID.');
            checkinLayer.clearLayers();
            return;
        }
        const touristId = parseInt(touristIdInput, 10);
        if (isNaN(touristId)) {
            showNoData('Invalid tourist ID.');
            checkinLayer.clearLayers();
            return;
        }
        if (locationUpdateInterval) {
            clearInterval(locationUpdateInterval);
            locationUpdateInterval = null;
        }
        if (currentLocationMarker) {
            checkinLayer.removeLayer(currentLocationMarker);
            currentLocationMarker = null;
        }
        currentLocationData = null;
        isLocationVisible = false;
        checkinLayer.clearLayers();
        showLoading();
        try {
            const isValid = await isValidTourist(touristId);
            if (!isValid) {
                showNoData('The tourist does not exist.');
                return;
            }
            const checkins = await fetchCheckins(touristId, filter);
            displaySummary(checkins, touristId);
            if (checkins && checkins.length > 0) {
                plotCheckins(checkins);
            }
        } catch (error) {
            showErrorModal(`Error fetching data: ${error.message}`);
            showNoData('Failed to load data. Please try again.');
        }
    }

    searchInput.addEventListener('change', fetchAndDisplayData);

    filterSelect.addEventListener('change', function() {
        const selectedValue = this.value;
        if (selectedValue === 'custom_year') {
            const tempFilter = currentFilter;
            $('#customYearModal').modal('show');
            $('#customYearModal').one('hidden.bs.modal', function() {
                if (!selectedCustomYear) {
                    filterSelect.value = tempFilter;
                    currentFilter = tempFilter;
                    updateChangeYearButtonVisibility();
                }
            });
        } else {
            currentFilter = selectedValue;
            selectedCustomYear = null;
            updateChangeYearButtonVisibility();
            fetchAndDisplayData();
        }
    });

    document.getElementById('edit-year-btn').addEventListener('click', function() {
        $('#customYearModal').modal('show');
    });

    $('#customYearModal').on('show.bs.modal', function() {
        document.getElementById('yearInput').value = selectedCustomYear || '';
    });

    document.getElementById('applyYearBtn').addEventListener('click', function() {
        const year = document.getElementById('yearInput').value;
        if (year && /^\d{4}$/.test(year)) {
            selectedCustomYear = year;
            currentFilter = 'custom_year';
            filterSelect.value = 'custom_year';
            updateChangeYearButtonVisibility();
            $('#customYearModal').modal('hide');
            fetchAndDisplayData();
        } else {
            alert('Please enter a valid four-digit year.');
        }
    });

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

    $('#warningDetailsModal').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });

    document.querySelectorAll('.modal .close, .modal .btn[data-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.closest('.modal').id;
            $(`#${modalId}`).modal('hide');
        });
    });

    const legendToggle = document.getElementById('legend-toggle');
    const legendsContainer = document.getElementById('legends-container');
    
    legendToggle.addEventListener('click', function() {
        if (legendsContainer.style.display === 'block') {
            legendsContainer.style.display = 'none';
            legendToggle.innerHTML = '<i class="fa fa-map-signs"></i> Legend';
        } else {
            legendsContainer.style.display = 'block';
            const allTabBtn = document.querySelector('.tab-btn[data-tab="all"]');
            if (allTabBtn) {
                document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                allTabBtn.classList.add('active');
                document.getElementById('warning-tab').style.display = 'block';
                document.getElementById('user-zones-tab').style.display = 'block';
                document.getElementById('checkin-tab').style.display = 'block';
                document.querySelectorAll('.section-divider').forEach(divider => divider.style.display = 'block');
            }
            legendToggle.innerHTML = '<i class="fa fa-map-signs"></i> Hide Legend';
        }
    });

    setTimeout(setupLegendTabs, 500);

    try {
        await loadWarningZones();
        await loadUserZones();
    } catch (error) {
        console.error('Failed to load initial data:', error);
        showErrorModal(`Error loading data: ${error.message}`);
    }

    document.getElementById('unlockLocationBtn').addEventListener('click', async () => {
        const pin = document.getElementById('pinInput').value;
        if (!pin) {
            document.getElementById('pinError').textContent = 'Please enter your PIN';
            document.getElementById('pinError').style.display = 'block';
            return;
        }

        try {
            const response = await fetch('/admin/pin/verify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ pin })
            });
            const data = await response.json();
            if (data.message) {
                $('#locationPinModal').modal('hide');
                const location = await fetchLatestLocation(currentTouristId);
                if (location) {
                    currentLocationData = location;
                    const { latitude, longitude } = location;
                    if (!currentLocationMarker) {
                        currentLocationMarker = L.marker([latitude, longitude], { icon: currentLocationIcon })
                            .addTo(checkinLayer)
                            .bindPopup(function() {
                                return `<b>Current Location</b><br>Last Updated: ${new Date(currentLocationData.updated_at).toLocaleString()}`;
                            });
                    } else {
                        currentLocationMarker.setLatLng([latitude, longitude]);
                    }
                    isLocationVisible = true;
                    map.setView([latitude, longitude], 13);
                    displaySummary(window.checkinsData || [], currentTouristId);

                    locationUpdateInterval = setInterval(async () => {
                        try {
                            const updatedLocation = await fetchLatestLocation(currentTouristId);
                            if (updatedLocation) {
                                currentLocationData = updatedLocation;
                                const { latitude, longitude } = updatedLocation;
                                if (currentLocationMarker) {
                                    currentLocationMarker.setLatLng([latitude, longitude]);
                                }
                            } else {
                                if (currentLocationMarker) {
                                    checkinLayer.removeLayer(currentLocationMarker);
                                    currentLocationMarker = null;
                                }
                                isLocationVisible = false;
                                displaySummary(window.checkinsData || [], currentTouristId);
                            }
                        } catch (error) {
                            console.error('Error fetching live location:', error);
                            showErrorModal(`Error updating location: ${error.message}`);
                        }
                    }, 5000);
                } else {
                    showErrorModal('Tourist did not enable current location.');
                }
            } else {
                document.getElementById('pinError').textContent = 'Incorrect PIN.';
                document.getElementById('pinError').style.display = 'block';
            }
        } catch (error) {
            console.error('Error verifying PIN:', error);
            document.getElementById('pinError').textContent = 'Error verifying PIN.';
            document.getElementById('pinError').style.display = 'block';
        }
    });

    document.getElementById('cancelPinModal').addEventListener('click', () => {
        $('#locationPinModal').modal('hide');
    });

    document.getElementById('changePinBtn').addEventListener('click', async () => {
        const pin = document.getElementById('pinInput').value;
        if (!pin) {
            document.getElementById('pinError').textContent = 'Please enter your current PIN';
            document.getElementById('pinError').style.display = 'block';
            return;
        }

        try {
            const response = await fetch('/admin/pin/verify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ pin })
            });
            const data = await response.json();
            if (data.message) {
                $('#locationPinModal').modal('hide');
                $('#changePinModal').modal('show');
            } else {
                document.getElementById('pinError').textContent = 'Incorrect PIN.';
                document.getElementById('pinError').style.display = 'block';
            }
        } catch (error) {
            console.error('Error verifying PIN:', error);
            document.getElementById('pinError').textContent = 'Error verifying PIN.';
            document.getElementById('pinError').style.display = 'block';
        }
    });

    document.getElementById('saveNewPinBtn').addEventListener('click', async () => {
        const currentPin = document.getElementById('pinInput').value;
        const newPin = document.getElementById('newPinInput').value;

        if (!newPin) {
            document.getElementById('pinChangeError').textContent = 'Please enter a new PIN';
            document.getElementById('pinChangeError').style.display = 'block';
            return;
        }

        try {
            const response = await fetch('/admin/pin/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    current_pin: currentPin,
                    new_pin: newPin
                })
            });
            const data = await response.json();
            if (data.message) {
                $('#changePinModal').modal('hide');
                $('#changePinModal').on('hidden.bs.modal', function () {
                    showSuccessModal('PIN updated successfully');
                    $(this).off('hidden.bs.modal');
                });
            } else {
                document.getElementById('pinChangeError').textContent = data.error || 'Failed to update PIN';
                document.getElementById('pinChangeError').style.display = 'block';
            }
        } catch (error) {
            console.error('Error updating PIN:', error);
            document.getElementById('pinChangeError').textContent = 'Error updating PIN';
            document.getElementById('pinChangeError').style.display = 'block';
        }
    });

    document.getElementById('cancelPinChange').addEventListener('click', () => {
        $('#changePinModal').modal('hide');
        $('#locationPinModal').modal('show');
    });

    $('#locationPinModal').on('shown.bs.modal', function () {
        document.getElementById('pinInput').value = '';
        document.getElementById('pinError').style.display = 'none';
    });

    $('#changePinModal').on('shown.bs.modal', function () {
        document.getElementById('newPinInput').value = '';
        document.getElementById('pinChangeError').style.display = 'none';
    });

    window.addEventListener('beforeunload', () => {
        if (locationUpdateInterval) {
            clearInterval(locationUpdateInterval);
        }
    });
});

// Function to set up legend tabs
function setupLegendTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const warningTab = document.getElementById('warning-tab');
    const userZonesTab = document.getElementById('user-zones-tab');
    const checkinTab = document.getElementById('checkin-tab');
    const dividers = document.querySelectorAll('.section-divider');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const tabName = this.getAttribute('data-tab');
            if (tabName === 'all') {
                warningTab.style.display = 'block';
                userZonesTab.style.display = 'block';
                checkinTab.style.display = 'block';
                dividers.forEach(divider => divider.style.display = 'block');
            } else if (tabName === 'warning') {
                warningTab.style.display = 'block';
                userZonesTab.style.display = 'none';
                checkinTab.style.display = 'none';
                dividers.forEach(divider => divider.style.display = 'none');
            } else if (tabName === 'user-zones') {
                warningTab.style.display = 'none';
                userZonesTab.style.display = 'block';
                checkinTab.style.display = 'none';
                dividers.forEach(divider => divider.style.display = 'none');
            } else if (tabName === 'checkin') {
                warningTab.style.display = 'none';
                userZonesTab.style.display = 'none';
                checkinTab.style.display = 'block';
                dividers.forEach(divider => divider.style.display = 'none');
            }
        });
    });
}

// Initialize warning elements
let drawHandler;
let selectedShape = null;
let currentDrawingType = 'marker';

document.getElementById('add-warning-btn').addEventListener('click', function() {
    resetWarningForm();
    $('#warningModal').modal('show');
});

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

map.on('draw:canceled', function() {
    if (selectedShape) {
        map.removeLayer(selectedShape);
        selectedShape = null;
    }
    drawHandler.disable();
    $('#warningModal').modal('show');
});

document.getElementById('save-warning-btn').addEventListener('click', async function() {
    if (!selectedShape) {
        showErrorModal('Please draw a shape on the map.');
        return;
    }
    
    const warningType = document.getElementById('warning-type').value;
    const warningZoneTag = document.getElementById('warning-zone-tag').value.trim();
    const warningDescription = document.getElementById('warning-description').value.trim();
    
    if (!warningZoneTag) {
        showErrorModal('Please enter a zone tag for the warning');
        return;
    }
    
    let warningData;
    if (currentDrawingType === 'marker') {
        const latlng = selectedShape.getLatLng();
        warningData = {
            type: warningType,
            zone_tag: warningZoneTag,
            description: warningDescription,
            latitude: latlng.lat,
            longitude: latlng.lng,
            shape_type: 'marker',
            created_at: new Date().toISOString(),
            status: 'active'
        };
    } else if (currentDrawingType === 'circle') {
        const center = selectedShape.getLatLng();
        const radius = selectedShape.getRadius();
        warningData = {
            type: warningType,
            zone_tag: warningZoneTag,
            description: warningDescription,
            latitude: center.lat,
            longitude: center.lng,
            radius: radius,
            shape_type: 'circle',
            created_at: new Date().toISOString(),
            status: 'active'
        };
    } else if (currentDrawingType === 'polygon') {
        const latlngs = selectedShape.getLatLngs()[0];
        const polygonCoords = latlngs.map(point => [point.lat, point.lng]);
        warningData = {
            type: warningType,
            zone_tag: warningZoneTag,
            description: warningDescription,
            polygon_coords: polygonCoords,
            shape_type: 'polygon',
            created_at: new Date().toISOString(),
            status: 'active'
        };
    }
    
    try {
        const { data, error } = await supabase.from('warning_zones').insert([warningData]).select();
        if (error) throw new Error(`Supabase error: ${error.message}`);
        console.log('Warning saved successfully:', data[0]);
        addWarningToMap(data[0]);
        resetWarningForm();
        $('#warningModal').modal('hide');
        showSuccessModal('Warning zone added successfully!');
    } catch (error) {
        console.error('Error saving warning zone:', error);
        showErrorModal(`Failed to save warning zone: ${error.message}`);
    }
});

async function loadWarningZones() {
    try {
        const { data, error } = await supabase.from('warning_zones').select('*').eq('status', 'active');
        if (error) {
            console.error('Supabase error on fetch warning zones:', error);
            throw new Error(`Failed to load warning zones: ${error.message}`);
        }
        
        console.log('Fetched warning zones:', data);
        if (data && data.length > 0) {
            warningLayer.clearLayers();
            data.forEach(warning => {
                addWarningToMap(warning);
            });
        } else {
            console.log('No active warning zones found in the database.');
        }
    } catch (error) {
        console.error('Error loading warning zones:', error);
        throw error;
    }
}

function addWarningToMap(warning) {
    let warningElement;
    if (warning.shape_type === 'marker') {
        if (!warning.latitude || !warning.longitude) return;
        warningElement = L.marker([warning.latitude, warning.longitude], { icon: warningIcons[warning.type] || defaultWarningIcon });
    } else if (warning.shape_type === 'circle') {
        if (!warning.latitude || !warning.longitude || !warning.radius) return;
        warningElement = L.circle([warning.latitude, warning.longitude], {
            radius: warning.radius,
            ...circleStyles[warning.type]
        });
    } else if (warning.shape_type === 'polygon') {
        if (!warning.polygon_coords || !Array.isArray(warning.polygon_coords)) return;
        const latlngs = warning.polygon_coords.map(coord => [coord[0], coord[1]]).filter(coord => coord !== null);
        if (latlngs.length < 3) return;
        warningElement = L.polygon(latlngs, {
            color: circleStyles[warning.type].color,
            fillColor: circleStyles[warning.type].fillColor,
            fillOpacity: circleStyles[warning.type].fillOpacity
        });
    } else {
        return;
    }
    
    warningElement.bindPopup(`<b>${warning.zone_tag}</b><br><button class="view-details-btn" data-id="${warning.zone_id}">View Details</button>`);
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

function showWarningDetails(warning) {
    const content = document.querySelector('.warning-details-content');
    content.innerHTML = `
        <h4>${warning.zone_tag}</h4>
        <p class="warning-type ${warning.type}"><strong>Type:</strong> ${warning.type.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
        <p><strong>Description:</strong> ${warning.description || 'No description provided'}</p>
        <p><strong>Shape Type:</strong> ${warning.shape_type}</p>
        <p><strong>Created:</strong> ${new Date(warning.created_at).toLocaleString()}</p>
        ${warning.shape_type === 'marker' || warning.shape_type === 'circle' ? `<p><strong>Location:</strong> Lat: ${warning.latitude ? warning.latitude.toFixed(6) : 'N/A'}, Lng: ${warning.longitude ? warning.longitude.toFixed(6) : 'N/A'}</p>` : ''}
        ${warning.shape_type === 'circle' ? `<p><strong>Radius:</strong> ${warning.radius}m</p>` : ''}
    `;
    
    document.querySelector('.deactivate-warning-btn').setAttribute('data-id', warning.zone_id);
    $('#warningDetailsModal').modal('show');
}

document.querySelector('.deactivate-warning-btn').addEventListener('click', function() {
    const warningId = this.getAttribute('data-id');
    
    let warningToDeactivate = null;
    warningLayer.eachLayer(layer => {
        if (layer.warningData && layer.warningData.zone_id == warningId) {
            warningToDeactivate = layer.warningData;
        }
    });
    
    if (warningToDeactivate) {
        currentWarningToDeactivate = warningId;
        const confirmModal = document.getElementById('deactivateWarningModal');
        confirmModal.querySelector('.warning-title').textContent = warningToDeactivate.zone_tag;
        confirmModal.querySelector('.warning-type').textContent = `Type: ${warningToDeactivate.type.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())}`;
        $('#warningDetailsModal').modal('hide');
        $('#deactivateWarningModal').modal('show');
    }
});

function resetWarningForm() {
    document.getElementById('warning-zone-tag').value = '';
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

document.getElementById('confirm-deactivate-btn').addEventListener('click', async function() {
    const warningId = currentWarningToDeactivate;
    try {
        const { error } = await supabase.from('warning_zones').update({ status: 'inactive' }).eq('zone_id', warningId);
        if (error) {
            console.error('Supabase error on deactivate:', error);
            throw new Error(`Failed to deactivate warning zone: ${error.message}`);
        }
        
        warningLayer.eachLayer(layer => {
            if (layer.warningData && layer.warningData.zone_id == warningId) {
                warningLayer.removeLayer(layer);
            }
        });
        
        $('#deactivateWarningModal').modal('hide');
        showSuccessModal('Warning zone deactivated successfully!');
    } catch (error) {
        console.error('Error deactivating warning zone:', error);
        $('#deactivateWarningModal').modal('hide');
        showErrorModal(`Failed to deactivate warning zone: ${error.message}`);
    }
});
</script>
@endsection