@extends(backpack_view('blank'))

@section('header')
    <div class="container-fluid">
        <div class="justify-content-between align-items-left">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 ">
                        <li class="breadcrumb-item"><a href="#">Pages</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
            </div>
            <div>
                <h2 class="header-container">
                    <span1>Tracking Overview</span1> 
                    <small class="d-block">It's <span class="day-bold">{{ now('Asia/Manila')->format('l') }}</span>, {{ now('Asia/Manila')->format('F d Y') }}</small>
                </h2>
            </div>
        </div>
    </div>
    <div class="button-container">
        <div class="dropdown">
            <button class="export-button dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Export
            </button>
            <div class="dropdown-menu export-dropdown" aria-labelledby="exportDropdown">
                <h6 class="dropdown-header">SELECT CARDS TO EXPORT</h6>
                <div class="dropdown-item">
                    <input type="checkbox" id="exportTouristArrivals" checked>
                    <label for="exportTouristArrivals">Tourist Arrivals</label>
                </div>
                <div class="dropdown-item">
                    <input type="checkbox" id="exportIncidentReports" checked>
                    <label for="exportIncidentReports">Incident Reports</label>
                </div>
                <div class="dropdown-item">
                    <input type="checkbox" id="exportMapData" checked>
                    <label for="exportMapData">Navigate Map</label>
                </div>
                <div class="dropdown-item">
                    <input type="checkbox" id="exportAccountCounts" checked>
                    <label for="exportAccountCounts">Manage Accounts</label>
                </div>
                <div class="dropdown-item">
                    <input type="checkbox" id="exportPopularSpots" checked>
                    <label for="exportPopularSpots">Popular Tourist Spots</label>
                </div>
                
                <div class="dropdown-divider"></div>
                
                <h6 class="dropdown-header">EXPORT FORMAT</h6>
                <div class="export-format-section">
                    <div class="format-option">
                        <input type="radio" name="exportFormat" id="exportCSV" value="csv" checked>
                        <label for="exportCSV">
                            <i class="las la-file-csv format-icon"></i> CSV
                        </label>
                    </div>
                    <div class="format-option">
                        <input type="radio" name="exportFormat" id="exportPDF" value="pdf">
                        <label for="exportPDF">
                            <i class="las la-file-pdf format-icon"></i> PDF
                        </label>
                    </div>
                </div>
                
                <button class="export-btn" id="exportSelected">Export Selected</button>
            </div>
        </div>
    </div>
@endsection

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<div class="dashboard-container">
    <!-- Tourist Arrivals Card -->
    <div class="card stats-card tourist-arrivals">
        <div class="card-header">
            <div class="header-left">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_35_239)">
                    <path d="M5.24991 9.00018C5.25005 8.26539 5.46741 7.54704 5.87466 6.93543L2.13366 3.19443C0.756227 4.81529 -4.57764e-05 6.87309 -4.57764e-05 9.00018C-4.57764e-05 11.1273 0.756227 13.1851 2.13366 14.8059L5.87466 11.0649C5.46741 10.4533 5.25005 9.73497 5.24991 9.00018Z" fill="white"/>
                    <path d="M15.8662 3.19443L12.1252 6.93543C12.5326 7.54698 12.75 8.26537 12.75 9.00018C12.75 9.73498 12.5326 10.4534 12.1252 11.0649L15.8662 14.8059C17.2437 13.1851 17.9999 11.1273 17.9999 9.00018C17.9999 6.87309 17.2437 4.81529 15.8662 3.19443Z" fill="white"/>
                    <path d="M9.00001 12.7497C8.26522 12.7495 7.54687 12.5322 6.93526 12.1249L3.19426 15.8659C4.81512 17.2433 6.87292 17.9996 9.00001 17.9996C11.1271 17.9996 13.1849 17.2433 14.8058 15.8659L11.0648 12.1249C10.4531 12.5322 9.7348 12.7495 9.00001 12.7497Z" fill="white"/>
                    <path d="M9.00001 5.25026C9.7348 5.2504 10.4531 5.46776 11.0648 5.87501L14.8058 2.13401C13.1849 0.756578 11.1271 0.000305176 9.00001 0.000305176C6.87292 0.000305176 4.81512 0.756578 3.19426 2.13401L6.93526 5.87501C7.54687 5.46776 8.26522 5.2504 9.00001 5.25026Z" fill="white"/>
                    </g>
                    <defs>
                    <clipPath id="clip0_35_239">
                    <rect width="18" height="18" fill="white"/>
                    </clipPath>
                    </defs>
                </svg>
                Tourist<span>Arrivals</span>
            </div>
            <div class="header-right">
                <div class="dropdown">
                    <button class="btn btn-link" type="button" id="touristArrivalsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="las la-ellipsis-v" style="color: #FF7E3F;"></i>
                    </button>
                    <div class="dropdown-menu custom-dropdown" aria-labelledby="touristArrivalsDropdown">
                        <a class="dropdown-item" href="#" data-filter="today">Today</a>
                        <a class="dropdown-item" href="#" data-filter="this_week">This Week</a>
                        <a class="dropdown-item" href="#" data-filter="this_month">This Month</a>
                        <a class="dropdown-item" href="#" data-filter="this_year">This Year</a>
                        <a class="dropdown-item" href="#" data-filter="custom_year">Custom Year</a>
                        <a class="dropdown-item" href="#" data-filter="all_time">All Time</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="stats-number loading">Loading...</div>
        </div>
    </div>

    <!-- Incident Reports Card -->
    <div class="card incidents-card">
        <div class="card-header">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <rect width="18" height="18" fill="url(#pattern0_11_8)"/>
            <defs>
            <pattern id="pattern0_11_8" patternContentUnits="objectBoundingBox" width="1" height="1">
            <use xlink:href="#image0_11_8" transform="scale(0.00195312)"/>
            </pattern>
            <image id="image0_11_8" width="512" height="512" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
            </defs>
            </svg>
            Incident<span>Reports</span>
        </div>
        <div class="header-right">
            <div class="dropdown" style="display:inline-block; padding:0; margin:0;">
                <button class="btn btn-link" type="button" id="incidentDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding:0; margin:0;">
                    <i class="las la-ellipsis-v" style="color: #FF7E3F; padding:0; margin:0; margin-top: 12px;"></i>
                </button>
                <div class="dropdown-menu custom-dropdown" aria-labelledby="incidentDropdown" style="width:auto !important; min-width:0 !important; padding:0 !important;">
                    <a class="dropdown-item" href="#" data-filter="today">Today</a>
                    <a class="dropdown-item" href="#" data-filter="this_week">This Week</a>
                    <a class="dropdown-item" href="#" data-filter="this_month">This Month</a>
                    <a class="dropdown-item" href="#" data-filter="this_year">This Year</a>
                    <a class="dropdown-item" href="#" data-filter="custom_year">Custom Year</a>
                    <a class="dropdown-item" href="#" data-filter="all_time">All Time</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="stats-number loading" style="color:#FF7E3F;">Loading...</div>
        </div>
    </div>

    <!-- Navigate Map Card -->
    <div class="card map-card">
        <div class="card-header">
            <div class="header-left">
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.375 14.9336C4.2638 14.9147 4.15382 14.8893 4.04563 14.8574L2.2525 14.2949C1.60407 14.1044 1.03462 13.7093 0.629122 13.1687C0.223622 12.628 0.00381594 11.9707 0.00250244 11.2949V3.74988C0.00315233 3.23616 0.130441 2.73052 0.373102 2.27772C0.615762 1.82492 0.96631 1.43893 1.39371 1.15391C1.82112 0.868888 2.3122 0.693633 2.82349 0.643656C3.33477 0.593679 3.85049 0.670521 4.325 0.86738L4.375 0.890505V14.9336ZM12.925 0.729255L12.9119 0.72488L11.215 0.16238C11.023 0.100117 10.8254 0.0567861 10.625 0.0330048V13.9468L11.9088 14.3168C12.2766 14.4063 12.66 14.4111 13.0299 14.3309C13.3999 14.2506 13.7468 14.0874 14.0445 13.8535C14.3422 13.6196 14.5829 13.3212 14.7484 12.9807C14.9139 12.6402 15 12.2666 15 11.888V3.67238C14.9991 3.0279 14.7993 2.39942 14.4279 1.87269C14.0566 1.34596 13.5317 0.946653 12.925 0.729255ZM9.375 0.11863C9.375 0.11863 5.72313 1.16425 5.625 1.17488V14.9249C5.6875 14.9118 9.375 13.9024 9.375 13.9024V0.11863Z" fill="#374957"/>
                </svg>
                Navigate <span>Map</span>
            </div>
            <div class="header-right">
                <div class="dropdown" style="display:inline-block; padding:0; margin:0;">
                    <button class="btn btn-link" type="button" id="mapDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding:0; margin:0;">
                        <i class="las la-ellipsis-v" style="color: #FF7E3F; padding:0; margin:0;"></i>
                    </button>
                    <div class="dropdown-menu custom-dropdown" aria-labelledby="mapDropdown" style="width:auto !important; min-width:0 !important; padding:0 !important;">
                        <a class="dropdown-item" href="#" style="padding:8px 2px !important; text-align:center; margin:0;" data-filter="today">Today</a>
                        <a class="dropdown-item" href="#" style="padding:8px 2px !important; text-align:center; margin:0;" data-filter="this_week">This Week</a>
                        <a class="dropdown-item" href="#" style="padding:8px 2px !important; text-align:center; margin:0;" data-filter="this_month">This Month</a>
                        <a class="dropdown-item" href="#" style="padding:8px 2px !important; text-align:center; margin:0;" data-filter="this_year">This Year</a>
                        <a class="dropdown-item" href="#" style="padding:8px 2px !important; text-align:center; margin:0;" data-filter="custom_year">Custom Year</a>
                        <a class="dropdown-item" href="#" style="padding:8px 2px !important; text-align:center; margin:0;" data-filter="all_time">All Time</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="map" class="map-container"></div>
            <div class="map-loading-overlay" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 1000; display: flex; justify-content: center; align-items: center;">
                <div class="loading-spinner"></div>
            </div>
        </div>
    </div>

    <!-- Manage Accounts Card -->
    <div class="card manage-card">
        <div class="card-header">
            <div class="header-left">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <rect width="22" height="22" fill="url(#pattern0_10_4)"/>
                <defs>
                <pattern id="pattern0_10_4" patternContentUnits="objectBoundingBox" width="1" height="1">
                <use xlink:href="#image0_10_4" transform="scale(0.00195312)"/>
                </pattern>
                <image id="image0_10_4" width="512" height="512" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAgAAAAIACAYAAAD0eNT6AAAAAXNSR0IArs4c6QAAIABJREFUeAHt3Qe4NVdZL/A/gRRqAoRepEdAFOl4KSK9RECBCCIglyYIeLkUFZSmGAtVUUEQBBIFVFCkKE2QFgQCEqlB6SX0khAg4d79kv3hyZdT9tlnZu+ZNb95nvN85zt779kzv/Wutd5payUWAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQI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ABzht3mEv9tyrUcM+xhS2BmAcfcBtn7iAk+VBEgCxMAkYqCPUQQlABPvQOz++AUepQOYRAew26NG72/r7IEEYPxttT0g0IvA/ZJ8RyIgERADzcZAHzMbfnUE8VLtmoUAgR0ErpnkxBFUaEembR2ZKs/VlGcfQweP4ZHiT+3Q7nmZAIG5QM14VkObfl8i0OyRoA53NR3u0Jz7eCTwr0bQTrxY606AwO4EbpCkZh4bWiNme5SJGFguBvqYRbDaiaGXR03aZCFAYJcCByS5S5L3jqCSD70Rsn3D7yhaLqPTktx4l/V/0bf/5YDbh2MW3QnvI0Bgc4GzJbllkqpMRhDUkbXcUba2bzVj4BOSXHrzqt3JX+vmwjrNPjS7lyU5Vyd7aCUECPxA4HxJfiHJs5N8dICVfmiNkO0ZXsfQcpnUqHfvTPL4JNdLcvYVtVt1kHDnJG/racbDRcus7l06LsldZwa1TRYCBHoUODxJXQf837PG5omzSUhqYKG/mM9MVrOT+WEgBrqPgWNnw/s+K8nRs8d3f2M23fBRSWqOjz6e9d9t81FH3ZdNcrk1/Jx7txvr/QQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIEBiUwP8HNYhnId0FHxkAAAAASUVORK5CYII="/>
                </defs>
                </svg>
                Manage <span>Accounts</span>
            </div>
        </div>
        <div class="card-body">
            <div class="accounts-container">
                <div class="loading-overlay">
                    <div class="loading-spinner"></div>
                    <div class="loading-text">Loading accounts...</div>
                </div>
                <div class="accounts-row">
                    <div class="account-column tourist-column">
                        <h3>Total Tourist Accounts</h3>
                        <div class="account-count" id="touristCount">0</div>
                    </div>
                    <div class="account-column-divider"></div>
                    <div class="account-column admin-column">
                        <h3>Total Admin Accounts</h3>
                        <div class="account-count" id="adminCount">0</div>
                    </div>
                </div>
            </div>
            <div class="see-more-container">
                <button class="see-more-button" onclick="window.location.href='{{ backpack_url('manage-tourists') }}'">See More <i class="las la-arrow-right"></i></button>
            </div>
        </div>
    </div>

    <!-- Analytics Card (Popular Tourist Spots) -->
    <div class="card analytics-card">
        <div class="card-header">
            <span>Popular Tourist Spots</span>
            <div class="filter-container">
                <select class="form-select form-select-sm" id="spotsFilterDashboard">
                    <option value="today" selected>Today</option>
                    <option value="this_week">This Week</option>
                    <option value="this_month">This Month</option>
                    <option value="this_year">This Year</option>
                    <option value="custom_year">Custom Year</option>
                    <option value="all_time">All Time</option>
                </select>
                <button id="editYearBtn" class="edit-year-btn" style="display:none;">Edit Year</button>
            </div>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="popularSpotsDashboardChart" height="300"></canvas>
                <div id="chartLoading" class="loading-overlay" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8);">
                    <div class="loading-spinner" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Year Modal -->
<div class="modal fade" id="customYearModal" tabindex="-1" role="dialog" 
     aria-labelledby="customYearModalLabel" aria-hidden="true"
     data-backdrop="false" data-keyboard="false" style="display: none;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customYearModalLabel">Select Custom Year</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="font-size: 1.5rem;">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="customYearForm">
                    <div class="form-group">
                        <label for="yearInput">Enter Year</label>
                        <input type="number" class="form-control" id="yearInput" placeholder="e.g., 2023" min="1900" max="2100">
                    </div>
                    <div id="yearError" style="color: red; display: none;">Please enter a valid four-digit year.</div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="applyYearBtn">Apply</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let dashboardData = {
            touristArrivals: null,
            incidentReports: null,
            mapData: null,
            accountCounts: { touristCount: null, adminCount: null },
            popularSpots: null
        };

        let latestTouristRequestId = 0;
        let latestIncidentRequestId = 0;
        let latestMapRequestId = 0;
        let latestSpotsRequestId = 0;

        let touristInterval, incidentInterval, mapInterval, spotsInterval, accountInterval;

        // Simple debounce function to prevent overlapping requests
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        var map = L.map('map').setView([7.0767, 125.8259], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    
        var markerLayer = L.layerGroup().addTo(map);
        setTimeout(function() { map.invalidateSize(); }, 500);
    
        let currentMapFilter = 'today';
    
        function updateMap(filter, showLoading = false) {
            const requestId = ++latestMapRequestId;
            const mapLoadingOverlay = document.querySelector('.map-loading-overlay');
            if (showLoading) {
                mapLoadingOverlay.style.display = 'flex';
            }
            
            fetch(`/admin/api/checkins-by-spot/${filter}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (requestId === latestMapRequestId) {
                        markerLayer.clearLayers();
                        data.forEach(spot => {
                            L.marker([spot.latitude, spot.longitude]).addTo(markerLayer)
                                .bindPopup(`<b>${spot.name}</b><br>Check-ins: ${spot.count}`);
                        });
                        dashboardData.mapData = data;
                        mapLoadingOverlay.style.display = 'none';
                        if (!mapInterval) {
                            mapInterval = setInterval(updateMapRealtime, 5000);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching check-ins:', error);
                    if (requestId === latestMapRequestId) {
                        dashboardData.mapData = null;
                        mapLoadingOverlay.style.display = 'none';
                    }
                });
        }
        
        const mapDropdownItems = document.querySelectorAll('.dropdown-menu[aria-labelledby="mapDropdown"] .dropdown-item');
        mapDropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                let filter = this.getAttribute('data-filter');
                if (filter === 'custom_year') {
                    $('#customYearModal').data('card', 'map');
                    $('#customYearModal').modal('show');
                } else {
                    if (mapInterval) {
                        clearInterval(mapInterval);
                    }
                    currentMapFilter = filter;
                    updateMap(filter, true);
                }
            });
        });
        
        updateMap('today', true);

        var popularSpotsDashboardCtx = document.getElementById('popularSpotsDashboardChart').getContext('2d');
        var popularSpotsDashboardChart = new Chart(popularSpotsDashboardCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Visits',
                    data: [],
                    backgroundColor: '#4ECDC4'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, grid: { color: '#eee' }, ticks: { color: '#666' } },
                    x: { grid: { display: false }, ticks: { color: '#666' } }
                },
                plugins: { legend: { display: false }, tooltip: { enabled: true } }
            }
        });

        let currentSpotsFilter = 'today';
        const chartLoading = document.getElementById('chartLoading');

        function updatePopularSpotsChart(filter, showLoading = false) {
            const requestId = ++latestSpotsRequestId;
            if (showLoading) {
                chartLoading.style.display = 'flex';
            }

            $.get(`/admin/api/popular-spots/${filter}`, function(data) {
                if (requestId === latestSpotsRequestId) {
                    if (data.error) {
                        console.error('API Error:', data.error);
                        popularSpotsDashboardChart.data.labels = ['Error'];
                        popularSpotsDashboardChart.data.datasets[0].data = [0];
                        dashboardData.popularSpots = null;
                    } else {
                        var topSpots = data.sort((a, b) => b.visits - a.visits).slice(0, 4);
                        var labels = topSpots.map(item => item.spot);
                        var values = topSpots.map(item => item.visits);
                        popularSpotsDashboardChart.data.labels = labels;
                        popularSpotsDashboardChart.data.datasets[0].data = values;
                        dashboardData.popularSpots = data;
                    }
                    popularSpotsDashboardChart.update();
                    chartLoading.style.display = 'none';
                    if (!spotsInterval) {
                        spotsInterval = setInterval(updatePopularSpotsChartRealtime, 5000);
                    }
                }
            }).fail(function(xhr, status, error) {
                if (requestId === latestSpotsRequestId) {
                    console.error('Failed to fetch popular spots:', error);
                    popularSpotsDashboardChart.data.labels = ['Error'];
                    popularSpotsDashboardChart.data.datasets[0].data = [0];
                    dashboardData.popularSpots = null;
                    popularSpotsDashboardChart.update();
                    chartLoading.style.display = 'none';
                }
            });
        }

        function updateEditYearButtonVisibility() {
            if (currentSpotsFilter.startsWith('custom_year:')) {
                $('#editYearBtn').show();
            } else {
                $('#editYearBtn').hide();
            }
        }

        updatePopularSpotsChart(currentSpotsFilter, true);
        updateEditYearButtonVisibility();

        $('#spotsFilterDashboard').change(function() {
            let filter = $(this).val();
            if (filter === 'custom_year') {
                $(this).data('previousFilter', currentSpotsFilter);
                $('#customYearModal').data('card', 'popularSpots');
                $('#customYearModal').modal('show');
            } else {
                if (spotsInterval) {
                    clearInterval(spotsInterval);
                }
                currentSpotsFilter = filter;
                updatePopularSpotsChart(filter, true);
                updateEditYearButtonVisibility();
            }
        });

        $('#customYearModal').on('show.bs.modal', function() {
            const card = $(this).data('card');
            if (card === 'popularSpots' && currentSpotsFilter.startsWith('custom_year:')) {
                const year = currentSpotsFilter.split(':')[1];
                document.getElementById('yearInput').value = year;
            } else {
                document.getElementById('yearInput').value = '';
            }
            document.getElementById('yearError').style.display = 'none';
        });

        $('#editYearBtn').click(function() {
            $('#customYearModal').data('card', 'popularSpots');
            $('#customYearModal').modal('show');
        });

        $('#customYearModal').on('hidden.bs.modal', function() {
            const card = $(this).data('card');
            if (card === 'popularSpots') {
                const filter = currentSpotsFilter;
                if (filter.startsWith('custom_year:')) {
                    $('#spotsFilterDashboard').val('custom_year');
                } else {
                    $('#spotsFilterDashboard').val(filter);
                }
            }
        });

        const touristDropdownItems = document.querySelectorAll('.dropdown-menu[aria-labelledby="touristArrivalsDropdown"] .dropdown-item');
        const touristStatsNumber = document.querySelector('.tourist-arrivals .stats-number');
        let currentTouristFilter = 'today';
        
        touristDropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                let filter = this.getAttribute('data-filter');
                if (filter === 'custom_year') {
                    $('#customYearModal').data('card', 'touristArrivals');
                    $('#customYearModal').modal('show');
                } else {
                    if (touristInterval) {
                        clearInterval(touristInterval);
                    }
                    currentTouristFilter = filter;
                    updateTouristArrivals(filter, true);
                }
            });
        });
        
        function updateTouristArrivals(filter, showLoading = false) {
            const requestId = ++latestTouristRequestId;
            if (showLoading) {
                touristStatsNumber.textContent = 'Loading...';
                touristStatsNumber.classList.add('loading');
            }
            
            fetch(`/admin/api/tourist-arrivals/${filter}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (requestId === latestTouristRequestId) {
                        touristStatsNumber.classList.remove('loading');
                        touristStatsNumber.textContent = data.count;
                        dashboardData.touristArrivals = data;
                        if (!touristInterval) {
                            touristInterval = setInterval(updateTouristArrivalsRealtime, 5000);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching tourist arrivals:', error);
                    if (requestId === latestTouristRequestId) {
                        touristStatsNumber.classList.remove('loading');
                        touristStatsNumber.textContent = 'Error';
                        dashboardData.touristArrivals = null;
                    }
                });
        }
        
        updateTouristArrivals('today', true);
    
        const incidentDropdownItems = document.querySelectorAll('.dropdown-menu[aria-labelledby="incidentDropdown"] .dropdown-item');
        const incidentStatsNumber = document.querySelector('.incidents-card .stats-number');
        let currentIncidentFilter = 'today';
        
        incidentDropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                let filter = this.getAttribute('data-filter');
                if (filter === 'custom_year') {
                    $('#customYearModal').data('card', 'incidentReports');
                    $('#customYearModal').modal('show');
                } else {
                    if (incidentInterval) {
                        clearInterval(incidentInterval);
                    }
                    currentIncidentFilter = filter;
                    updateIncidentReports(filter, true);
                }
            });
        });
        
        function updateIncidentReports(filter, showLoading = false) {
            const requestId = ++latestIncidentRequestId;
            if (showLoading) {
                incidentStatsNumber.textContent = 'Loading...';
                incidentStatsNumber.classList.add('loading');
            }
            
            fetch(`/admin/api/incident-reports/${filter}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (requestId === latestIncidentRequestId) {
                        incidentStatsNumber.classList.remove('loading');
                        incidentStatsNumber.textContent = data.count;
                        dashboardData.incidentReports = data;
                        if (!incidentInterval) {
                            incidentInterval = setInterval(updateIncidentReportsRealtime, 5000);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching incident reports:', error);
                    if (requestId === latestIncidentRequestId) {
                        incidentStatsNumber.classList.remove('loading');
                        incidentStatsNumber.textContent = 'Error';
                        dashboardData.incidentReports = null;
                    }
                });
        }
        
        updateIncidentReports('today', true);
    
        const loadingOverlay = document.querySelector('.manage-card .loading-overlay');
        
        function updateAccountCounts(showLoading = false) {
            if (showLoading) {
                loadingOverlay.style.display = 'flex';
            }
            
            fetch('/admin/api/accounts/count')
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    document.getElementById('touristCount').textContent = data.touristCount || 0;
                    document.getElementById('adminCount').textContent = data.adminCount || 0;
                    loadingOverlay.style.display = 'none';
                    dashboardData.accountCounts.touristCount = data.touristCount || 0;
                    dashboardData.accountCounts.adminCount = data.adminCount || 0;
                    if (!accountInterval) {
                        accountInterval = setInterval(updateAccountCountsRealtime, 5000);
                    }
                })
                .catch(error => {
                    console.error('Error fetching account counts:', error);
                    document.getElementById('touristCount').textContent = 0;
                    document.getElementById('adminCount').textContent = 0;
                    loadingOverlay.style.display = 'none';
                    dashboardData.accountCounts.touristCount = 0;
                    dashboardData.accountCounts.adminCount = 0;
                });
        }
        
        updateAccountCounts(true);

        document.getElementById('applyYearBtn').addEventListener('click', function() {
            const year = document.getElementById('yearInput').value;
            if (year && /^\d{4}$/.test(year)) {
                const card = $('#customYearModal').data('card');
                const filter = `custom_year:${year}`;
                if (card === 'touristArrivals') {
                    if (touristInterval) {
                        clearInterval(touristInterval);
                    }
                    currentTouristFilter = filter;
                    updateTouristArrivals(filter, true);
                } else if (card === 'incidentReports') {
                    if (incidentInterval) {
                        clearInterval(incidentInterval);
                    }
                    currentIncidentFilter = filter;
                    updateIncidentReports(filter, true);
                } else if (card === 'map') {
                    if (mapInterval) {
                        clearInterval(mapInterval);
                    }
                    currentMapFilter = filter;
                    updateMap(filter, true);
                } else if (card === 'popularSpots') {
                    if (spotsInterval) {
                        clearInterval(spotsInterval);
                    }
                    currentSpotsFilter = filter;
                    updatePopularSpotsChart(filter, true);
                    updateEditYearButtonVisibility();
                }
                $('#customYearModal').modal('hide');
            } else {
                document.getElementById('yearError').style.display = 'block';
            }
        });
    
        document.getElementById('exportSelected').addEventListener('click', function() {
            const selectedCards = {
                touristArrivals: document.getElementById('exportTouristArrivals').checked,
                incidentReports: document.getElementById('exportIncidentReports').checked,
                mapData: document.getElementById('exportMapData').checked,
                accountCounts: document.getElementById('exportAccountCounts').checked,
                popularSpots: document.getElementById('exportPopularSpots').checked
            };

            const exportFormat = document.querySelector('input[name="exportFormat"]:checked').value;

            if (!Object.values(selectedCards).some(Boolean)) {
                alert('Please select at least one card to export.');
                return;
            }

            for (const [key, selected] of Object.entries(selectedCards)) {
                if (selected && dashboardData[key] === null) {
                    alert('Please wait for all selected data to load before exporting.');
                    return;
                }
            }

            if (exportFormat === 'csv') {
                exportToCSV(selectedCards);
            } else if (exportFormat === 'pdf') {
                exportToPDF(selectedCards);
            }
        });

        function exportToCSV(selectedCards) {
            const timestamp = new Date().toLocaleString();
            let csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "TRAKS - Tourism Dashboard Export\n";
            csvContent += "Generated on: " + timestamp + "\n\n";

            if (selectedCards.touristArrivals) {
                csvContent += "===== TOURIST ARRIVALS =====\n";
                csvContent += "Period: " + currentTouristFilter.replace('_', ' ') + "\n";
                csvContent += "Count: " + dashboardData.touristArrivals.count + "\n";
                dashboardData.touristArrivals.touristIds.forEach(id => {
                    csvContent += id + "\n";
                });
                csvContent += "\n";
            }
            
            if (selectedCards.incidentReports) {
                csvContent += "===== INCIDENT REPORTS =====\n";
                csvContent += "Period: " + currentIncidentFilter.replace('_', ' ') + "\n";
                csvContent += "Count: " + dashboardData.incidentReports.count + "\n";
                csvContent += "Incidents\n";
                csvContent += "Tourist ID,Latitude,Longitude,Timestamp,Status\n"; 
                dashboardData.incidentReports.incidents.forEach(incident => {
                    csvContent += `${incident.user_id},${incident.latitude},${incident.longitude},${incident.timestamp},${incident.status}\n`;
                });
                csvContent += "\n";
            }
            
            if (selectedCards.accountCounts) {
                csvContent += "===== ACCOUNT MANAGEMENT =====\n";
                csvContent += "Account Type,Count\n";
                csvContent += `Tourist Accounts,${dashboardData.accountCounts.touristCount}\n`;
                csvContent += `Admin Accounts,${dashboardData.accountCounts.adminCount}\n\n`;
            }
            
            if (selectedCards.mapData) {
                csvContent += "===== MAP DATA (Check-ins) =====\n";
                csvContent += "Period: " + currentMapFilter.replace('_', ' ') + "\n";
                csvContent += "Location Name,Latitude,Longitude,Check-ins\n";
                dashboardData.mapData.forEach(spot => {
                    csvContent += `"${spot.name}",${spot.latitude},${spot.longitude},${spot.count}\n`;
                });
                csvContent += "\n";
            }
            
            if (selectedCards.popularSpots) {
                csvContent += "===== POPULAR TOURIST SPOTS =====\n";
                csvContent += "Period: " + currentSpotsFilter.replace('_', ' ') + "\n";
                csvContent += "Spot Name,Visits\n";
                const sortedSpots = [...dashboardData.popularSpots].sort((a, b) => b.visits - a.visits);
                sortedSpots.forEach(spot => {
                    csvContent += `"${spot.spot}",${spot.visits}\n`;
                });
            }

            const dateStr = new Date().toISOString().slice(0,10);
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `TRAKS_Dashboard_Export_${dateStr}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function exportToPDF(selectedCards) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const pageWidth = doc.internal.pageSize.getWidth();
            const dateStr = new Date().toLocaleDateString();
            const timeStr = new Date().toLocaleTimeString();
            let yOffset = 15;
            
            doc.setFontSize(18);
            doc.setTextColor(55, 73, 87);
            doc.text("TRAKS - Tourism Dashboard Export", pageWidth / 2, yOffset, { align: "center" });
            
            yOffset += 8;
            doc.setFontSize(10);
            doc.setTextColor(100, 100, 100);
            doc.text(`Generated on: ${dateStr} at ${timeStr}`, pageWidth / 2, yOffset, { align: "center" });
            
            yOffset += 15;
            
            function addSectionHeader(title) {
                doc.setFillColor(255, 126, 63);
                doc.rect(10, yOffset - 5, pageWidth - 20, 8, 'F');
                doc.setFontSize(12);
                doc.setTextColor(255, 255, 255);
                doc.text(title, 12, yOffset);
                yOffset += 8;
                doc.setTextColor(0, 0, 0);
                doc.setFontSize(10);
            }
            
            function addTable(headers, rows, startY) {
                const cellPadding = 2;
                const lineHeight = 8;
                const fontSize = 9;
                const tableWidth = pageWidth - 20;
                const colWidth = tableWidth / headers.length;
                
                if (startY + (rows.length + 1) * lineHeight > doc.internal.pageSize.getHeight() - 20) {
                    doc.addPage();
                    startY = 20;
                }
                
                doc.setFillColor(240, 240, 240);
                doc.rect(10, startY, tableWidth, lineHeight, 'F');
                doc.setFontSize(fontSize);
                doc.setTextColor(80, 80, 80);
                doc.setFont(undefined, 'bold');
                
                headers.forEach((header, i) => {
                    doc.text(header, 10 + (i * colWidth) + cellPadding, startY + lineHeight - 2);
                });
                
                doc.setFont(undefined, 'normal');
                doc.setTextColor(0, 0, 0);
                
                rows.forEach((row, r) => {
                    if (r % 2 === 0) {
                        doc.setFillColor(250, 250, 250);
                        doc.rect(10, startY + (r + 1) * lineHeight, tableWidth, lineHeight, 'F');
                    }
                    
                    row.forEach((cell, c) => {
                        doc.text(String(cell), 10 + (c * colWidth) + cellPadding, startY + (r + 1) * lineHeight + lineHeight - 2);
                    });
                });
                
                return startY + (rows.length + 1) * lineHeight + 10;
            }
            
            if (selectedCards.touristArrivals) {
                addSectionHeader("TOURIST ARRIVALS");
                doc.text(`Period: ${currentTouristFilter.replace('_', ' ')}`, 12, yOffset + 5);
                doc.text(`Total Count: ${dashboardData.touristArrivals.count}`, 12, yOffset + 10);
                yOffset += 20;
            }
            
            if (selectedCards.incidentReports) {
                addSectionHeader("INCIDENT REPORTS");
                doc.text(`Period: ${currentIncidentFilter.replace('_', ' ')}`, 12, yOffset + 5);
                doc.text(`Total Count: ${dashboardData.incidentReports.count}`, 12, yOffset + 10);
                yOffset += 15;
                const incidentHeaders = ["Tourist ID", "Latitude", "Longitude", "Timestamp", "Status"];
                const incidentRows = dashboardData.incidentReports.incidents.map(incident => [
                    incident.user_id,
                    incident.latitude,
                    incident.longitude,
                    incident.timestamp,
                    incident.status
                ]); 
                yOffset = addTable(incidentHeaders, incidentRows, yOffset);
            }
            
            if (selectedCards.accountCounts) {
                addSectionHeader("ACCOUNT MANAGEMENT");
                yOffset = addTable(
                    ["Account Type", "Count"], 
                    [
                        ["Tourist Accounts", dashboardData.accountCounts.touristCount],
                        ["Admin Accounts", dashboardData.accountCounts.adminCount]
                    ],
                    yOffset + 5
                );
            }
            
            if (selectedCards.mapData) {
                addSectionHeader("MAP DATA (CHECK-INS)");
                doc.text(`Period: ${currentMapFilter.replace('_', ' ')}`, 12, yOffset + 5);
                yOffset += 10;
                const mapHeaders = ["Location Name", "Check-ins", "Coordinates"];
                const mapRows = dashboardData.mapData.map(spot => [
                    spot.name, 
                    spot.count, 
                    `${spot.latitude.toFixed(4)}, ${spot.longitude.toFixed(4)}`
                ]);
                yOffset = addTable(mapHeaders, mapRows, yOffset);
            }
            
            if (selectedCards.popularSpots) {
                addSectionHeader("POPULAR TOURIST SPOTS");
                doc.text(`Period: ${currentSpotsFilter.replace('_', ' ')}`, 12, yOffset + 5);
                yOffset += 10;
                const spotsHeaders = ["Rank", "Spot Name", "Visits"];
                const sortedSpots = [...dashboardData.popularSpots]
                    .sort((a, b) => b.visits - a.visits)
                    .map((spot, index) => [index + 1, spot.spot, spot.visits]);
                yOffset = addTable(spotsHeaders, sortedSpots, yOffset);
            }
            
            const pageCount = doc.internal.getNumberOfPages();
            for (let i = 1; i <= pageCount; i++) {
                doc.setPage(i);
                doc.setFontSize(8);
                doc.setTextColor(150, 150, 150);
                doc.text(`Page ${i} of ${pageCount}`, pageWidth - 20, doc.internal.pageSize.getHeight() - 10);
            }
            
            const currentDate = new Date().toISOString().slice(0,10);
            doc.save(`TRAKS_Dashboard_Export_${currentDate}.pdf`);
        }

        $('#mapDropdown').on('show.bs.dropdown', function() {
            $('.dropdown-menu').css({
                'width': 'auto',
                'min-width': '0',
                'padding': '0',
                'margin': '0'
            });
            $('.dropdown-item').css({
                'padding': '8px 2px',
                'margin': '0',
                'text-align': 'center'
            });
        });
                
        document.querySelectorAll('[data-dismiss="modal"]').forEach(button => {
            button.addEventListener('click', () => {
                $('#customYearModal').modal('hide');
            });
        });
        
        function updateTouristArrivalsRealtime() {
            fetch(`/admin/api/tourist-arrivals/${currentTouristFilter}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (!dashboardData.touristArrivals || dashboardData.touristArrivals.count !== data.count) {
                        touristStatsNumber.textContent = data.count;
                        dashboardData.touristArrivals = data;
                        console.log('Tourist arrivals updated');
                    }
                })
                .catch(error => {
                    console.error('Error fetching tourist arrivals:', error);
                });
        }

        function updateIncidentReportsRealtime() {
            fetch(`/admin/api/incident-reports/${currentIncidentFilter}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (!dashboardData.incidentReports || dashboardData.incidentReports.count !== data.count) {
                        incidentStatsNumber.textContent = data.count;
                        dashboardData.incidentReports = data;
                        console.log('Incident reports updated');
                    }
                })
                .catch(error => {
                    console.error('Error fetching incident reports:', error);
                });
        }

        function updateMapRealtime() {
            fetch(`/admin/api/checkins-by-spot/${currentMapFilter}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    const currentTotal = dashboardData.mapData ? 
                        dashboardData.mapData.reduce((sum, spot) => sum + spot.count, 0) : 0;
                    const newTotal = data.reduce((sum, spot) => sum + spot.count, 0);
                    
                    if (!dashboardData.mapData || currentTotal !== newTotal) {
                        markerLayer.clearLayers();
                        data.forEach(spot => {
                            L.marker([spot.latitude, spot.longitude]).addTo(markerLayer)
                                .bindPopup(`<b>${spot.name}</b><br>Check-ins: ${spot.count}`);
                        });
                        dashboardData.mapData = data;
                        console.log('Map data updated');
                    }
                })
                .catch(error => {
                    console.error('Error fetching check-ins:', error);
                });
        }

        function updatePopularSpotsChartRealtime() {
            $.get(`/admin/api/popular-spots/${currentSpotsFilter}`, function(data) {
                if (data.error) {
                    console.error('API Error:', data.error);
                    return;
                }
                
                const dataChanged = !dashboardData.popularSpots || 
                    JSON.stringify(data) !== JSON.stringify(dashboardData.popularSpots);
                    
                if (dataChanged) {
                    var topSpots = data.sort((a, b) => b.visits - a.visits).slice(0, 4);
                    var labels = topSpots.map(item => item.spot);
                    var values = topSpots.map(item => item.visits);
                    popularSpotsDashboardChart.data.labels = labels;
                    popularSpotsDashboardChart.data.datasets[0].data = values;
                    popularSpotsDashboardChart.update();
                    dashboardData.popularSpots = data;
                    console.log('Popular spots updated');
                }
            }).fail(function(xhr, status, error) {
                console.error('Failed to fetch popular spots:', error);
            });
        }

        function updateAccountCountsRealtime() {
            fetch('/admin/api/accounts/count')
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    const touristCount = data.touristCount || 0;
                    const adminCount = data.adminCount || 0;
                    
                    if (dashboardData.accountCounts.touristCount !== touristCount || 
                        dashboardData.accountCounts.adminCount !== adminCount) {
                        document.getElementById('touristCount').textContent = touristCount;
                        document.getElementById('adminCount').textContent = adminCount;
                        dashboardData.accountCounts.touristCount = touristCount;
                        dashboardData.accountCounts.adminCount = adminCount;
                        console.log('Account counts updated');
                    }
                })
                .catch(error => {
                    console.error('Error fetching account counts:', error);
                });
        }

        function updateActiveDropdownItems(dropdownId, currentFilter) {
            const dropdownMenu = document.querySelector(`[aria-labelledby="${dropdownId}"]`);
            if (!dropdownMenu) return;

            dropdownMenu.querySelectorAll('.dropdown-item').forEach(item => {
                const itemFilter = item.getAttribute('data-filter');
                const isActive = currentFilter.startsWith(itemFilter);
                item.classList.toggle('active', isActive);
            });
        }

        touristDropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                let filter = this.getAttribute('data-filter');
                if (filter === 'custom_year') {
                    $('#customYearModal').data('card', 'touristArrivals');
                    $('#customYearModal').modal('show');
                } else {
                    if (touristInterval) {
                        clearInterval(touristInterval);
                    }
                    currentTouristFilter = filter;
                    updateTouristArrivals(filter, true);
                    updateActiveDropdownItems('touristArrivalsDropdown', currentTouristFilter);
                }
            });
        });

        incidentDropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                let filter = this.getAttribute('data-filter');
                if (filter === 'custom_year') {
                    $('#customYearModal').data('card', 'incidentReports');
                    $('#customYearModal').modal('show');
                } else {
                    if (incidentInterval) {
                        clearInterval(incidentInterval);
                    }
                    currentIncidentFilter = filter;
                    updateIncidentReports(filter, true);
                    updateActiveDropdownItems('incidentDropdown', currentIncidentFilter);
                }
            });
        });

        mapDropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                let filter = this.getAttribute('data-filter');
                if (filter === 'custom_year') {
                    $('#customYearModal').data('card', 'map');
                    $('#customYearModal').modal('show');
                } else {
                    if (mapInterval) {
                        clearInterval(mapInterval);
                    }
                    currentMapFilter = filter;
                    updateMap(filter, true);
                    updateActiveDropdownItems('mapDropdown', currentMapFilter);
                }
            });
        });

        document.getElementById('applyYearBtn').addEventListener('click', function() {
            const year = document.getElementById('yearInput').value;
            if (year && /^\d{4}$/.test(year)) {
                const card = $('#customYearModal').data('card');
                const filter = `custom_year:${year}`;
                if (card === 'touristArrivals') {
                    if (touristInterval) {
                        clearInterval(touristInterval);
                    }
                    currentTouristFilter = filter;
                    updateTouristArrivals(filter, true);
                    updateActiveDropdownItems('touristArrivalsDropdown', currentTouristFilter);
                } else if (card === 'incidentReports') {
                    if (incidentInterval) {
                        clearInterval(incidentInterval);
                    }
                    currentIncidentFilter = filter;
                    updateIncidentReports(filter, true);
                    updateActiveDropdownItems('incidentDropdown', currentIncidentFilter);
                } else if (card === 'map') {
                    if (mapInterval) {
                        clearInterval(mapInterval);
                    }
                    currentMapFilter = filter;
                    updateMap(filter, true);
                    updateActiveDropdownItems('mapDropdown', currentMapFilter);
                } else if (card === 'popularSpots') {
                    if (spotsInterval) {
                        clearInterval(spotsInterval);
                    }
                    currentSpotsFilter = filter;
                    updatePopularSpotsChart(filter, true);
                    updateEditYearButtonVisibility();
                }
                $('#customYearModal').modal('hide');
            } else {
                document.getElementById('yearError').style.display = 'block';
            }
        });

        updateActiveDropdownItems('touristArrivalsDropdown', currentTouristFilter);
        updateActiveDropdownItems('incidentDropdown', currentIncidentFilter);
        updateActiveDropdownItems('mapDropdown', currentMapFilter);
    });
</script>
@endsection