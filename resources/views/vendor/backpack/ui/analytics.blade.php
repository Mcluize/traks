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
                        <i class="fas {{ $touristChangeIcon }}"></i> {{ $touristChange }}% from last week
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
                        <button class="btn btn-sm btn-outline-secondary">Export</button>
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
                        <button class="btn btn-sm btn-outline-secondary">View All</button>
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
                        <button class="btn btn-sm btn-outline-secondary">View All Reports</button>
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
</div>

<!-- Scripts (moved to the end for better performance) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

<script>
    $(document).ready(function() {
        // Check if Leaflet and heatLayer are defined
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

        // Filter Incident Status by Time Period
        $('.time-filter').click(function() {
            $('.time-filter').removeClass('active');
            $(this).addClass('active');
            var period = $(this).data('period');
            $.get('/analytics/incident-status?period=' + period, function(data) {
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

        // Initialize Map
        var map = L.map('activityMap').setView([7.08, 125.6], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var spots = {!! json_encode($touristSpots) !!};
        var incidents = {!! json_encode($incidents) !!};
        var checkins = {!! json_encode($checkins) !!};

        // Add tourist spot markers
        spots.forEach(function(spot) {
            L.marker([spot.latitude, spot.longitude])
                .addTo(map)
                .bindPopup(spot.name);
        });

        // Add incident markers
        function getIncidentIcon(status) {
            var color = status === 'Pending' ? '#FFC107' : (status === 'Cancelled' ? '#DC3545' : '#6C757D');
            return L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background-color: ${color}; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white;"></div>`,
                iconSize: [12, 12],
                iconAnchor: [6, 6]
            });
        }

        incidents.forEach(function(incident) {
            L.marker([incident.latitude, incident.longitude], {icon: getIncidentIcon(incident.status)})
                .addTo(map)
                .bindPopup("Incident - " + incident.status);
        });

        // Add check-in heat layer
        var heatPoints = checkins.map(function(checkin) {
            var spot = spots.find(s => s.spot_id === checkin.spot_id);
            return spot ? [spot.latitude, spot.longitude, 1] : null;
        }).filter(Boolean);
        var heat = L.heatLayer(heatPoints, {radius: 25}).addTo(map);

        // Map filter functionality
        $('#mapFilter').change(function() {
            map.eachLayer(function(layer) {
                if (layer !== map.tileLayer) map.removeLayer(layer);
            });
            heat.addTo(map);
            var filter = $(this).val();
            if (filter === 'all' || filter === 'checkins') {
                spots.forEach(function(spot) {
                    L.marker([spot.latitude, spot.longitude]).addTo(map).bindPopup(spot.name);
                });
                heatPoints = checkins.map(c => {
                    var spot = spots.find(s => s.spot_id === c.spot_id);
                    return spot ? [spot.latitude, spot.longitude, 1] : null;
                }).filter(Boolean);
                heat.setLatLngs(heatPoints);
            }
            if (filter === 'all' || filter === 'incidents') {
                incidents.forEach(function(incident) {
                    L.marker([incident.latitude, incident.longitude], {icon: getIncidentIcon(incident.status)})
                        .addTo(map)
                        .bindPopup("Incident - " + incident.status);
                });
            }
        });
    });
</script>
@endsection