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
                    <small class="d-block">It's <span class="day-bold">{{ now()->format('l') }}</span>, {{ now()->format('F d Y') }}</small>
                </h2>
            </div>
        </div>
    </div>
    <div class="button-container">
        <button class="export-button">Export</button>
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
                <rect width="18" height="18" fill="url(#pattern0_4_6)"/>
                <defs>
                <pattern id="pattern0_4_6" patternContentUnits="objectBoundingBox" width="1" height="1">
                <use xlink:href="#image0_4_6" transform="scale(0.00195312)"/>
                </pattern>
                <image id="image0_4_6" width="512" height="512" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
                </defs>
                </svg>
            Incident<span>Reports</span></div>
        <div class="header-right">
            <div class="dropdown" style="display:inline-block; padding:0; margin:0;">
                <button class="btn btn-link" type="button" id="incidentDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding:0; margin:0;">
                    <i class="las la-ellipsis-v" style="color: #FF7E3F; padding:0; margin:0; margin-top: 12px;"></i>
                </button>
                <div class="dropdown-menu custom-dropdown" aria-labelledby="incidentDropdown" style="width:auto !important; min-width:0 !important; padding:0 !important;">
                    <a class="dropdown-item" href="#" data-filter="today">Today</a>
                    <a class="dropdown-item" href="#" data-filter="this_week">This Week</a>
                    <a class="dropdown-item" href="#" data-filter="this_month">This Month</a>
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
                        <a class="dropdown-item" href="#" style="padding:8px 2px !important; text-align:center; margin:0;">Today</a>
                        <a class="dropdown-item" href="#" style="padding:8px 2px !important; text-align:center; margin:0;">This Week</a>
                        <a class="dropdown-item" href="#" style="padding:8px 2px !important; text-align:center; margin:0;">This Month</a>
                        <a class="dropdown-item" href="#" style="padding:8px 2px !important; text-align:center; margin:0;">All Time</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="map" class="map-container"></div>
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
                <image id="image0_10_4" width="512" height="512" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAgAAAAIACAYAAAD0eNT6AAAAAXNSR0IArs4c6QAAIABJREFUeAHt3Qe4NVdZL/A/gRRqAoRepEdAFOl4KSK9RECBCCIglyYIeLkUFZSmGAtVUUEQBBIFVFCkKE2QFgQCEqlB6SX0khAg4d79kv3hyZdT9tlnZu+ZNb95nvN85zt779kzv/Wutd5payUWAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIEBiUwP8HNYhnId0FHxkAAAAASUVORK5CYII="/>
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

    <!-- Analytics Card (Replaced with Popular Tourist Spots) -->
    <div class="card analytics-card">
        <div class="card-header">
            <span>Popular Tourist Spots</span>
            <select class="form-select form-select-sm" id="spotsFilterDashboard">
                <option value="today">Today</option>
                <option value="this_week">This Week</option>
                <option value="this_month">This Month</option>
                <option value="all_time" selected>All Time</option>
            </select>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="popularSpotsDashboardChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Map setup
        var map = L.map('map').setView([7.0767, 125.8259], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    
        var markerLayer = L.layerGroup().addTo(map);
        setTimeout(function() { map.invalidateSize(); }, 500);
    
        // Track current selected map filter
        let currentMapFilter = 'today';
    
        function updateMap(filter, showLoading = false) {
            if (showLoading) {}
            
            fetch(`/admin/api/checkins-by-spot/${filter}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    markerLayer.clearLayers();
                    data.forEach(spot => {
                        L.marker([spot.latitude, spot.longitude]).addTo(markerLayer)
                            .bindPopup(`<b>${spot.name}</b><br>Check-ins: ${spot.count}`);
                    });
                })
                .catch(error => console.error('Error fetching check-ins:', error));
        }
        
        const mapDropdownItems = document.querySelectorAll('.dropdown-menu[aria-labelledby="mapDropdown"] .dropdown-item');
        mapDropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const filter = this.textContent.toLowerCase().replace(' ', '_');
                currentMapFilter = filter;
                updateMap(filter, true);
            });
        });
        
        updateMap('today', true);
    
        // Popular Tourist Spots Chart
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

        // Track the current selected filter
        let currentSpotsFilter = 'all_time';

        function updatePopularSpotsChart(filter) {
    $.get(`/admin/api/popular-spots/${filter}`, function(data) {
        var topSpots = data.sort((a, b) => b.visits - a.visits).slice(0, 4);
        var labels = topSpots.map(item => item.spot);
        var values = topSpots.map(item => item.visits);
        popularSpotsDashboardChart.data.labels = labels;
        popularSpotsDashboardChart.data.datasets[0].data = values;
        popularSpotsDashboardChart.update();
    }).fail(function(xhr, status, error) {
        console.error('Failed to fetch popular spots:', error);
    });
}

        // Initial update with default filter
        updatePopularSpotsChart(currentSpotsFilter);

        // Update filter on dropdown change
        $('#spotsFilterDashboard').change(function() {
            currentSpotsFilter = $(this).val();
            updatePopularSpotsChart(currentSpotsFilter);
        });

        
        setInterval(() => updatePopularSpotsChart(currentSpotsFilter), 5000);
    
        // Tourist Arrivals
        const touristDropdownItems = document.querySelectorAll('.dropdown-menu[aria-labelledby="touristArrivalsDropdown"] .dropdown-item');
        const touristStatsNumber = document.querySelector('.tourist-arrivals .stats-number');
        let currentTouristFilter = 'today';
        
        touristDropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const filter = this.getAttribute('data-filter');
                currentTouristFilter = filter;
                touristStatsNumber.classList.add('loading');
                updateTouristArrivals(filter, true);
            });
        });
        
        function updateTouristArrivals(filter, showLoading = false) {
            if (showLoading) {
                touristStatsNumber.classList.add('loading');
            }
            
            fetch(`/admin/api/tourist-arrivals/${filter}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    touristStatsNumber.classList.remove('loading');
                    touristStatsNumber.textContent = data.count;
                })
                .catch(error => {
                    touristStatsNumber.classList.remove('loading');
                    touristStatsNumber.textContent = 'Error';
                });
        }
        
        touristStatsNumber.classList.add('loading');
        updateTouristArrivals('today', true);
    
        // Incident Reports
        const incidentDropdownItems = document.querySelectorAll('.dropdown-menu[aria-labelledby="incidentDropdown"] .dropdown-item');
        const incidentStatsNumber = document.querySelector('.incidents-card .stats-number');
        let currentIncidentFilter = 'today';
        
        incidentDropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const filter = this.getAttribute('data-filter');
                currentIncidentFilter = filter;
                updateIncidentReports(filter, true);
            });
        });
        
        function updateIncidentReports(filter, showLoading = false) {
            if (showLoading) {
                incidentStatsNumber.classList.add('loading');
            }
            
            fetch(`/admin/api/incident-reports/${filter}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    incidentStatsNumber.classList.remove('loading');
                    incidentStatsNumber.textContent = data.count;
                })
                .catch(error => {
                    incidentStatsNumber.classList.remove('loading');
                    incidentStatsNumber.textContent = 'Error';
                });
        }
        
        incidentStatsNumber.classList.add('loading');
        updateIncidentReports('today', true);
    
        // Manage Accounts
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
                })
                .catch(error => {
                    console.error('Error fetching account counts:', error);
                    document.getElementById('touristCount').textContent = 0;
                    document.getElementById('adminCount').textContent = 0;
                    loadingOverlay.style.display = 'none';
                });
        }
        
        updateAccountCounts(true);
    
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
        
        setInterval(() => updateTouristArrivals(currentTouristFilter, false), 5000);
        setInterval(() => updateIncidentReports(currentIncidentFilter, false), 5000);
        setInterval(() => updateAccountCounts(false), 5000);
        setInterval(() => updateMap(currentMapFilter, false), 5000);
    });
</script>
@endsection