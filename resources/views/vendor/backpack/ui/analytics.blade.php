@extends(backpack_view('blank'))

@section('header')
    <div class="container-fluid">
        <div class="justify-content-between align-items-left">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="#">Pages</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Analytics</li>
                    </ol>
                </nav>
            </div>
            <div>
                <h2 class="header-container">
                    <span>Analytics</span> 
                    <small class="d-block">It's <span class="day-bold">{{ now()->format('l') }}</span>, {{ now()->format('F d Y') }}</small>
                </h2>
            </div>
        </div>
    </div>
@endsection

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/analytics.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css" />

<style>
    .leaflet-control-legend {
        background: rgba(255, 255, 255, 0.8);
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
    }
    .legend-item { display: flex; align-items: center; margin-bottom: 5px; }
    .legend-color { width: 12px; height: 12px; border-radius: 50%; margin-right: 5px; }
</style>

<div class="analytics-container">
    <!-- Top Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon tourist">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>Tourist Arrivals</h3>
                    <div class="stat-number">{{ $touristArrivals }}</div>
                    <div class="stat-change {{ $touristChangeClass }}">
                        <i class="fas {{ $touristChangeIcon }}"></i> {{ $touristChange }}% from yesterday
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon incident">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <h3>Incident Reports</h3>
                    <div class="stat-number">{{ $incidentReports }}</div>
                    <div class="stat-change {{ $incidentChangeClass }}">
                        <i class="fas {{ $incidentChangeIcon }}"></i> {{ $incidentChange }}% from last week
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon tourist-account">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="stat-content">
                    <h3>Tourist Accounts</h3>
                    <div class="stat-number">{{ $touristAccounts }}</div>
                    <div class="stat-change {{ $touristAccountsChangeClass }}">
                        <i class="fas {{ $touristAccountsChangeIcon }}"></i> {{ $touristAccountsChange }}% from last month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon admin-account">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-content">
                    <h3>Admin Accounts</h3>
                    <div class="stat-number">{{ $adminAccounts }}</div>
                    <div class="stat-change {{ $adminAccountsChangeClass }}">
                        <i class="fas {{ $adminAccountsChangeIcon }}"></i> {{ $adminAccountsChange }}% from last month
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Middle Sections -->
    <div class="row mb-4">
        <!-- Incident Report Status -->
        <div class="col-md-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Incident Report Status</h3>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-outline-secondary time-filter" data-period="week">Week</button>
                        <button class="btn btn-sm btn-outline-secondary time-filter active" data-period="month">Month</button>
                        <button class="btn btn-sm btn-outline-secondary time-filter" data-period="year">Year</button>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="incidentStatusChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Weekly Analytics -->
        <div class="col-md-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Weekly Analytics</h3>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-outline-secondary export-btn">Export</button>
                    </div>
                </div>
                <div class="chart-body">
                    <div class="metric-highlight">
                        <div class="metric-value">{{ $totalActivities }}</div>
                        <div class="metric-label">Total Activities This Week</div>
                    </div>
                    <canvas id="weeklyAnalyticsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- New Charts -->
    <div class="row mb-4">
        <!-- User Growth Over Time -->
        <div class="col-md-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>User Growth Over Time</h3>
                </div>
                <div class="chart-body">
                    <canvas id="userGrowthChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- User Type Distribution -->
        <div class="col-md-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>User Type Distribution</h3>
                </div>
                <div class="chart-body">
                    <canvas id="userTypeChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tourist Activity Map and Tourist Spots -->
    <div class="row mb-4">
        <!-- Tourist Activity Heatmap -->
        <div class="col-md-8">
            <div class="map-card">
                <div class="chart-header">
                    <h3>Tourist Activity Heatmap</h3>
                    <div class="chart-actions">
                        <select class="form-select form-select-sm" id="mapFilter">
                            <option value="all">All Activities</option>
                            <option value="checkins">Check-ins</option>
                            <option value="incidents">Incidents</option>
                        </select>
                    </div>
                </div>
                <div id="activityMap" class="map-container"></div>
            </div>
        </div>
        
        <!-- Popular Tourist Spots -->
        <div class="col-md-4">
            <div class="data-card">
                <div class="chart-header">
                    <h3>Popular Tourist Spots</h3>
                    <div class="chart-actions">
                        <select class="form-select form-select-sm" id="spotsFilterChart">
                            <option value="today">Today</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month">This Month</option>
                            <option value="all_time" selected>All Time</option>
                        </select>
                        <button class="btn btn-sm btn-outline-secondary view-all-spots">View All</button>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="popularSpotsChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Incident Reports -->
    <div class="row">
        <div class="col-12">
            <div class="data-card">
                <div class="chart-header">
                    <h3>Latest Incident Reports</h3>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-outline-secondary view-all-reports">View All Reports</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Report ID</th>
                                <th>Tourist ID</th>
                                <th>Location</th>
                                <th>Timestamp</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestIncidents as $incident)
                            <tr>
                                <td class="text-truncate" style="max-width: 150px;">{{ substr($incident['report_id'], 0, 4) }}...</td>
                                <td>{{ $incident['user_id'] }}</td>
                                <td>{{ $incident['latitude'] }}, {{ $incident['longitude'] }}</td>
                                <td>{{ $incident['timestamp'] }}</td>
                                <td><span class="badge bg-{{ $incident['status'] == 'Pending' ? 'warning' : ($incident['status'] == 'Cancelled' ? 'danger' : 'secondary') }}">{{ $incident['status'] }}</span></td>
                                <td><button class="btn btn-sm btn-outline-primary">View</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Spots Modal -->
    <div class="modal fade" id="spotsModal" tabindex="-1" aria-labelledby="spotsModalLabel" aria-hidden="true" data-bs-backdrop="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="spotsModalLabel">All Tourist Spots</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <select id="spotsFilter" class="form-select mb-3">
                        <option value="today" selected>Today</option>
                        <option value="this_week">This Week</option>
                        <option value="this_month">This Month</option>
                        <option value="all_time">All Time</option>
                    </select>
                    <table class="table">
                        <thead><tr><th>Spot Name</th><th>Visits</th></tr></thead>
                        <tbody id="spotsTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        if (typeof L === 'undefined') {
            console.error('Leaflet library is not loaded.');
            return;
        }
        if (typeof L.heatLayer !== 'function') {
            console.error('Leaflet Heat plugin is not loaded or incompatible.');
            return;
        }

        // Incident Status Chart
        var statusCtx = document.getElementById('incidentStatusChart').getContext('2d');
        var statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($incidentStatusLabels) !!},
                datasets: [{
                    data: {!! json_encode($incidentStatusData) !!},
                    backgroundColor: ['#FFC107', '#DC3545', '#6C757D', '#28A745'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                legend: { position: 'right', labels: { boxWidth: 12, fontFamily: 'Poppins' } },
                tooltips: { titleFontFamily: 'Poppins', bodyFontFamily: 'Poppins' },
                cutoutPercentage: 70
            }
        });

        $('.time-filter').click(function() {
            $('.time-filter').removeClass('active');
            $(this).addClass('active');
            var period = $(this).data('period');
            $.get('/admin/analytics/incident-status?period=' + period, function(data) {
                statusChart.data.labels = data.labels;
                statusChart.data.datasets[0].data = data.values;
                statusChart.update();
            });
        });

        // Weekly Analytics Chart
        var weeklyCtx = document.getElementById('weeklyAnalyticsChart').getContext('2d');
        var weeklyChart = new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($weeklyLabels) !!},
                datasets: [{
                    label: 'Tourist Activities',
                    data: {!! json_encode($weeklyCheckinsData) !!},
                    borderColor: '#4ECDC4',
                    backgroundColor: 'rgba(78, 205, 196, 0.1)',
                    pointBackgroundColor: '#4ECDC4',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Incident Reports',
                    data: {!! json_encode($weeklyIncidentsData) !!},
                    borderColor: '#FF6B6B',
                    backgroundColor: 'rgba(255, 107, 107, 0.1)',
                    pointBackgroundColor: '#FF6B6B',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    yAxes: [{ ticks: { beginAtZero: true, fontFamily: 'Poppins' }, gridLines: { drawBorder: false } }],
                    xAxes: [{ ticks: { fontFamily: 'Poppins' }, gridLines: { display: false } }]
                },
                legend: { labels: { fontFamily: 'Poppins' } },
                tooltips: { titleFontFamily: 'Poppins', bodyFontFamily: 'Poppins' }
            }
        });

        // User Growth Chart
        var userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        var userGrowthChart = new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($userGrowthLabels) !!},
                datasets: [{
                    label: 'New Users',
                    data: {!! json_encode($userGrowthData) !!},
                    borderColor: '#5D78FF',
                    backgroundColor: 'rgba(93, 120, 255, 0.1)',
                    pointBackgroundColor: '#5D78FF',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    yAxes: [{ ticks: { beginAtZero: true, fontFamily: 'Poppins' }, gridLines: { drawBorder: false } }],
                    xAxes: [{ ticks: { fontFamily: 'Poppins' }, gridLines: { display: false } }]
                },
                legend: { labels: { fontFamily: 'Poppins' } },
                tooltips: { titleFontFamily: 'Poppins', bodyFontFamily: 'Poppins' }
            }
        });

        // User Type Distribution Chart
        var userTypeCtx = document.getElementById('userTypeChart').getContext('2d');
        var userTypeChart = new Chart(userTypeCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($userTypeLabels) !!},
                datasets: [{
                    data: {!! json_encode($userTypeData) !!},
                    backgroundColor: ['#5D78FF', '#FFAA5A', '#4ECDC4'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                legend: { position: 'right', labels: { boxWidth: 12, fontFamily: 'Poppins' } },
                tooltips: { titleFontFamily: 'Poppins', bodyFontFamily: 'Poppins' }
            }
        });

        // Popular Tourist Spots Chart
        var popularSpotsCtx = document.getElementById('popularSpotsChart').getContext('2d');
        var popularSpotsChart = new Chart(popularSpotsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($popularSpotsLabels) !!},
                datasets: [{
                    label: 'Visits',
                    data: {!! json_encode($popularSpotsData) !!},
                    backgroundColor: '#4ECDC4'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    yAxes: [{ ticks: { beginAtZero: true, fontFamily: 'Poppins' } }],
                    xAxes: [{ ticks: { fontFamily: 'Poppins' } }]
                },
                legend: { display: false },
                tooltips: { titleFontFamily: 'Poppins', bodyFontFamily: 'Poppins' }
            }
        });

        // Update Popular Spots Chart based on filter
        $('#spotsFilterChart').change(function() {
            var filter = $(this).val();
            $.get(`/admin/analytics/popular-spots/${filter}`, function(data) {
                var topSpots = data.sort((a, b) => b.visits - a.visits).slice(0, 4);
                var labels = topSpots.map(item => item.spot);
                var values = topSpots.map(item => item.visits);
                popularSpotsChart.data.labels = labels;
                popularSpotsChart.data.datasets[0].data = values;
                popularSpotsChart.update();
            }).fail(function(xhr, status, error) {
                console.error('Failed to fetch popular spots:', error);
            });
        });

        // Initialize Map
        var map = L.map('activityMap').setView([7.08, 125.6], 11);
        var tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        map.tileLayer = tileLayer; // Store reference to tile layer

        var spots = {!! json_encode($touristSpots) !!};
        var incidents = {!! json_encode($incidents) !!};
        var checkins = {!! json_encode($checkins) !!};
        var users = {!! json_encode($users) !!};

        function getIncidentIcon(status) {
            var color = status === 'Pending' ? '#FFC107' :
                        status === 'Cancelled' ? '#DC3545' :
                        status === 'Resolved' ? '#28A745' :
                        status === 'Ignored' ? '#6C757D' : '#6C757D';
            return L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background-color: ${color}; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white;"></div>`,
                iconSize: [12, 12],
                iconAnchor: [6, 6]
            });
        }

        // Initial map setup
        spots.forEach(function(spot) {
            var checkinCount = checkins.filter(c => c.spot_id === spot.spot_id).length;
            L.marker([spot.latitude, spot.longitude], {
                icon: L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div style="background:#4ECDC4; width:12px; height:12px; border-radius:50%; border:2px solid white;"></div>`,
                    iconSize: [12, 12],
                    iconAnchor: [6, 6]
                })
            }).addTo(map).bindPopup(`<b>${spot.name}</b><br>Check-ins: ${checkinCount}`);
        });

        incidents.forEach(function(incident) {
            L.marker([incident.latitude, incident.longitude], {icon: getIncidentIcon(incident.status)})
                .addTo(map)
                .bindPopup(`Incident - ${incident.status}<br>Tourist ID: ${incident.user_id}`);
        });

        var heatPoints = checkins.map(function(checkin) {
            var spot = spots.find(s => s.spot_id === checkin.spot_id);
            return spot ? [spot.latitude, spot.longitude, 1] : null;
        }).filter(Boolean);
        var heat = L.heatLayer(heatPoints, {radius: 25}).addTo(map);

        // Create a custom legend control
        var LegendControl = L.Control.extend({
            options: { position: 'bottomright' },
            onAdd: function(map) {
                var div = L.DomUtil.create('div', 'leaflet-control-legend');
                div.innerHTML = `
                    <div class="legend-item"><div class="legend-color" style="background:#4ECDC4;"></div> Check-ins</div>
                    <div class="legend-item"><div class="legend-color" style="background:#FFC107;"></div> Pending Incidents</div>
                    <div class="legend-item"><div class="legend-color" style="background:#DC3545;"></div> Cancelled Incidents</div>
                    <div class="legend-item"><div class="legend-color" style="background:#28A745;"></div> Resolved Incidents</div>
                    <div class="legend-item"><div class="legend-color" style="background:#6C757D;"></div> Ignored Incidents</div>
                `;
                return div;
            }
        });

        // Add the legend to the map
        new LegendControl().addTo(map);

        $('#mapFilter').change(function() {
            var filter = $(this).val();
            
            // Remove all layers except the tile layer and legend
            map.eachLayer(function(layer) {
                if (layer !== map.tileLayer && !(layer instanceof LegendControl)) {
                    map.removeLayer(layer);
                }
            });
            
            // Ensure tile layer is present
            if (!map.hasLayer(map.tileLayer)) {
                map.tileLayer.addTo(map);
            }
            
            // Add heatmap layer
            heat.addTo(map);
            
            if (filter === 'all' || filter === 'checkins') {
                if (checkins.length > 0) {
                    spots.forEach(function(spot) {
                        var checkinCount = checkins.filter(c => c.spot_id === spot.spot_id).length;
                        L.marker([spot.latitude, spot.longitude], {
                            icon: L.divIcon({
                                className: 'custom-div-icon',
                                html: `<div style="background:#4ECDC4; width:12px; height:12px; border-radius:50%; border:2px solid white;"></div>`,
                                iconSize: [12, 12],
                                iconAnchor: [6, 6]
                            })
                        }).addTo(map).bindPopup(`<b>${spot.name}</b><br>Check-ins: ${checkinCount}`);
                    });
                    heatPoints = checkins.map(c => {
                        var spot = spots.find(s => s.spot_id === c.spot_id);
                        return spot ? [spot.latitude, spot.longitude, 1] : null;
                    }).filter(Boolean);
                    heat.setLatLngs(heatPoints);
                } else {
                    console.log('No check-ins data available');
                }
            }
            
            if (filter === 'all' || filter === 'incidents') {
                if (incidents.length > 0) {
                    incidents.forEach(function(incident) {
                        L.marker([incident.latitude, incident.longitude], {icon: getIncidentIcon(incident.status)})
                            .addTo(map)
                            .bindPopup(`Incident - ${incident.status}<br>Tourist ID: ${incident.user_id}`);
                    });
                } else {
                    console.log('No incidents data available');
                }
            }
        });

        // Export Button
        $('.export-btn').click(function() {
            var csv = "Day,Tourist Activities,Incident Reports\n";
            var labels = {!! json_encode($weeklyLabels) !!};
            var checkinsData = {!! json_encode($weeklyCheckinsData) !!};
            var incidentsData = {!! json_encode($weeklyIncidentsData) !!};
            for (var i = 0; i < labels.length; i++) {
                csv += `${labels[i]},${checkinsData[i]},${incidentsData[i]}\n`;
            }
            var blob = new Blob([csv], {type: 'text/csv'});
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'weekly_analytics.csv';
            link.click();
        });

        // View All Spots Modal
        $('.view-all-spots').click(function() {
            $('#spotsModal').modal('show');
            updateSpotsTable('today');
        });

        $('#spotsFilter').change(function() {
            updateSpotsTable($(this).val());
        });

        function updateSpotsTable(filter) {
            $.get(`/admin/analytics/popular-spots/${filter}`, function(data) {
                $('#spotsTableBody').empty();
                data.forEach(spot => {
                    $('#spotsTableBody').append(`<tr><td>${spot.spot}</td><td>${spot.visits}</td></tr>`);
                });
            }).fail(function(xhr, status, error) {
                console.error('Failed to fetch popular spots:', error);
                $('#spotsModal').modal('hide');
                $('.modal-backdrop').remove();
            });
        }

        // Make backdrop clickable to close modal
        $('#spotsModal').on('shown.bs.modal', function () {
            $('.modal-backdrop').on('click', function () {
                $('#spotsModal').modal('hide');
            });
        });

        // Handle navigation back to clean up modal
        window.onpopstate = function(event) {
            $('#spotsModal').modal('hide');
            $('.modal-backdrop').remove();
        };

        // View All Reports (Redirect or Modal can be implemented here)
        $('.view-all-reports').click(function() {
            window.location.href = '/admin/incidents';
        });
    });
</script>
@endsection