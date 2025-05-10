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
                <div id="chartLoading" class="loading-overlay" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8);">
                    <div class="loading-spinner" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></div>
                </div>
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

        var map = L.map('map').setView([7.0767, 125.8259], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    
        var markerLayer = L.layerGroup().addTo(map);
        setTimeout(function() { map.invalidateSize(); }, 500);
    
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
                    dashboardData.mapData = data;
                })
                .catch(error => {
                    console.error('Error fetching check-ins:', error);
                    dashboardData.mapData = null;
                });
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

        let currentSpotsFilter = 'all_time';
        const chartLoading = document.getElementById('chartLoading');

        function updatePopularSpotsChart(filter, showLoading = false) {
            if (showLoading) {
                chartLoading.style.display = 'flex';
            }

            $.get(`/admin/api/popular-spots/${filter}`, function(data) {
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
            }).fail(function(xhr, status, error) {
                console.error('Failed to fetch popular spots:', error);
                popularSpotsDashboardChart.data.labels = ['Error'];
                popularSpotsDashboardChart.data.datasets[0].data = [0];
                dashboardData.popularSpots = null;
                popularSpotsDashboardChart.update();
                chartLoading.style.display = 'none';
            });
        }

        updatePopularSpotsChart(currentSpotsFilter, true);

        $('#spotsFilterDashboard').change(function() {
            currentSpotsFilter = $(this).val();
            updatePopularSpotsChart(currentSpotsFilter, true);
        });

        setInterval(() => updatePopularSpotsChart(currentSpotsFilter, false), 30000);

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
                    dashboardData.touristArrivals = data; // Store entire object
                })
                .catch(error => {
                    touristStatsNumber.classList.remove('loading');
                    touristStatsNumber.textContent = 'Error';
                    dashboardData.touristArrivals = null;
                });
        }
        
        touristStatsNumber.classList.add('loading');
        updateTouristArrivals('today', true);
    
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
                    dashboardData.incidentReports = data; // Store entire object
                })
                .catch(error => {
                    incidentStatsNumber.classList.remove('loading');
                    incidentStatsNumber.textContent = 'Error';
                    dashboardData.incidentReports = null;
                });
        }
        
        incidentStatsNumber.classList.add('loading');
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
                    csvContent += `${incident.user_id},${incident.latitude},${incident.longitude},${incident.timestamp},${incident.status}\n`; // Updated mapping
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
                const colWidths = [30, 30, 30, 120, 30];
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
        
        setInterval(() => updateTouristArrivals(currentTouristFilter, false), 5000);
        setInterval(() => updateIncidentReports(currentIncidentFilter, false), 5000);
        setInterval(() => updateAccountCounts(false), 5000);
        setInterval(() => updateMap(currentMapFilter, false), 5000);
    });
</script>
@endsection