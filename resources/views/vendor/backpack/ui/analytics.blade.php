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
                    <span1>Analytics</span1> 
                    <small class="d-block">It's <span class="day-bold">{{ now('Asia/Manila')->format('l') }}</span>, {{ now('Asia/Manila')->format('F d Y') }}</small>
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
    .form-select {
        background-color: #FF6B6B;
        color: white;
        border: none;
        background-image: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        padding-right: 10px;
    }
    .form-select:hover,
    .form-select:active,
    .form-select:focus {
        background-color: #FF6B6B;
        color: white;
        box-shadow: none;
    }
    .form-select option {
        background-color: white;
        color: black;
    }
    .chart-body {
        position: relative;
    }
    .error-message { 
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #DC3545; 
        font-size: 14px; 
        text-align: center; 
        display: none;
    }
    .loading-state {
        display: none;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 16px;
        color: #6c757d;
    }
    .chart-body.loading .loading-state {
        display: block;
    }
    .chart-body.loading canvas {
        opacity: 0.5;
    }
    #customYearModalSpotsTable {
        z-index: 1060 !important;
    }
    #spotsModal {
        z-index: 1050 !important;
    }
    .modal-body.loading #spotsTableBody {
        display: none;
    }
    .modal-body.loading .loading-state {
        display: block;
    }
</style>

<div class="analytics-container">
    <!-- Top Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card" id="touristArrivals">
                <div class="stat-icon tourist"><i class="fas fa-users"></i></div>
                <div class="stat-content">
                    <h3>Tourist Arrivals</h3>
                    <div class="stat-number">{{ $touristArrivals }}</div>
                    <div class="stat-change {{ $touristChangeClass }}"><i class="fas {{ $touristChangeIcon }}"></i> {{ $touristChange }}% from yesterday</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" id="incidentReports">
                <div class="stat-icon incident"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-content">
                    <h3>Incident Reports</h3>
                    <div class="stat-number">{{ $incidentReports }}</div>
                    <div class="stat-change {{ $incidentChangeClass }}"><i class="fas {{ $incidentChangeIcon }}"></i> {{ $incidentChange }}% from last week</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" id="touristAccounts">
                <div class="stat-icon tourist-account"><i class="fas fa-id-card"></i></div>
                <div class="stat-content">
                    <h3>Tourist Accounts</h3>
                    <div class="stat-number">{{ $touristAccounts }}</div>
                    <div class="stat-change {{ $touristAccountsChangeClass }}"><i class="fas {{ $touristAccountsChangeIcon }}"></i> {{ $touristAccountsChange }}% from last month</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" id="adminAccounts">
                <div class="stat-icon admin-account"><i class="fas fa-user-shield"></i></div>
                <div class="stat-content">
                    <h3>Admin Accounts</h3>
                    <div class="stat-number">{{ $adminAccounts }}</div>
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
                        <select class="form-select form-select-sm time-filter" id="incidentPeriodFilter">
                            <option value="today" selected>Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                            <option value="all_time">All Time</option>
                            <option value="custom_year">Custom Year</option>
                        </select>
                    </div>
                </div>
                <div class="chart-body" id="incidentChartBody">
                    <div class="loading-state">Loading...</div>
                    <canvas id="incidentStatusChart" height="250"></canvas>
                    <div id="incidentError" class="error-message" style="display: none;"></div>
                </div>
            </div>
        </div>
        
        <!-- Tourist Activities -->
        <div class="col-md-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Tourist Activities</h3>
                    <div class="chart-actions">
                        <select class="form-select form-select-sm activity-filter" id="touristPeriodFilter">
                            <option value="this_week" selected>This Week</option>
                            <option value="this_month">This Month</option>
                            <option value="this_year">This Year</option>
                            <option value="custom_year">Custom Year</option>
                        </select>
                        <button class="btn btn-sm btn-outline-secondary export-btn">Export CSV</button>
                        <button class="btn btn-sm btn-outline-secondary export-btn-pdf">Export PDF</button>
                    </div>
                </div>
                <div class="chart-body" id="touristChartBody">
                    <div class="loading-state">Loading...</div>
                    <div class="row" id="touristMetrics">
                        <div class="col-6">
                            <div class="metric-highlight">
                                <div class="metric-value" id="totalActivitiesValue">{{ $totalActivities }}</div>
                                <div class="metric-label" id="totalActivitiesLabel">Total Activities {{ $initialPeriod }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="metric-highlight">
                                <div class="metric-value" id="totalIncidentsValue">{{ $totalIncidents }}</div>
                                <div class="metric-label" id="totalIncidentsLabel">Total Incident Reports {{ $initialPeriod }}</div>
                            </div>
                        </div>
                    </div>
                    <canvas id="touristActivitiesChart" height="200"></canvas>
                    <div id="touristError" class="error-message" style="display: none;"></div>
                </div>
            </div>
        </div>

        <!-- Custom Year Modal for Tourist Activities -->
        <div class="modal fade" id="customYearModalTourist" tabindex="-1" aria-labelledby="customYearModalTouristLabel" aria-hidden="true" data-bs-backdrop="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="customYearModalTouristLabel">Select Year for Tourist Activities</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="number" id="customYearInputTourist" class="form-control" placeholder="Enter year (e.g., 2023)" min="{{ $minYear }}" max="{{ $maxYear }}">
                        <div id="customYearErrorTourist" class="invalid-feedback"></div>
                    </div>
                    <div class="modal-footer modal-footer-centered">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="applyCustomYearTourist">Apply</button>
                    </div>
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
                    <h3>Tourist Growth Over Time</h3>
                    <div class="chart-actions">
                        <select class="form-select form-select-sm" id="userGrowthPeriodFilter">
                            <option value="this_week">This Week</option>
                            <option value="this_month" selected>This Month</option>
                            <option value="this_year">This Year</option>
                            <option value="custom_year">Custom Year</option>
                            <option value="all_time">All Time</option>
                        </select>
                    </div>
                </div>
                <div class="chart-body" id="userGrowthChartBody">
                    <div class="loading-state">Loading...</div>
                    <canvas id="userGrowthChart" height="250"></canvas>
                    <div id="userGrowthError" class="error-message" style="display: none;"></div>
                </div>
            </div>
        </div>
        
        <!-- User Type Distribution -->
        <div class="col-md-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>User Type Distribution</h3>
                </div>
                <div class="chart-body" id="userTypeChartBody">
                    <div class="loading-state">Loading...</div>
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
                            <option value="today" selected>Today</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month">This Month</option>
                            <option value="all_time">All Time</option>
                            <option value="custom_year">Custom Year</option>
                        </select>
                        <button class="btn btn-sm btn-outline-secondary view-all-spots">View All</button>
                    </div>
                </div>
                <div class="chart-body" id="popularSpotsChartBody">
                    <div class="loading-state">Loading...</div>
                    <canvas id="popularSpotsChart" height="300"></canvas>
                    <div id="spotsError" class="error-message" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Year Modal for Incident Report Status -->
    <div class="modal fade" id="customYearModal" tabindex="-1" aria-labelledby="customYearModalLabel" aria-hidden="true" data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customYearModalLabel">Select Year for Incident Reports</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="number" id="customYearInput" class="form-control" placeholder="Enter year (e.g., 2023)" min="{{ $minYear }}" max="{{ $maxYear }}">
                    <div id="customYearError" class="invalid-feedback"></div>
                </div>
                <div class="modal-footer modal-footer-centered">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="applyCustomYear">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Year Modal for Popular Tourist Spots -->
    <div class="modal fade" id="customYearModalSpots" tabindex="-1" aria-labelledby="customYearModalSpotsLabel" aria-hidden="true" data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customYearModalSpotsLabel">Select Year for Popular Spots</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="number" id="customYearInputSpots" class="form-control" placeholder="Enter year (e.g., 2023)" min="{{ $minYear }}" max="{{ $maxYear }}">
                    <div id="customYearErrorSpots" class="invalid-feedback"></div>
                </div>
                <div class="modal-footer modal-footer-centered">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="applyCustomYearSpots">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Year Modal for User Growth -->
    <div class="modal fade" id="customYearModalUserGrowth" tabindex="-1" aria-labelledby="customYearModalUserGrowthLabel" aria-hidden="true" data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customYearModalUserGrowthLabel">Select Year for User Growth</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="number" id="customYearInputUserGrowth" class="form-control" placeholder="Enter year (e.g., 2023)" min="{{ $minYear }}" max="{{ $maxYear }}">
                    <div id="customYearErrorUserGrowth" class="invalid-feedback"></div>
                </div>
                <div class="modal-footer modal-footer-centered">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="applyCustomYearUserGrowth">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Year Modal for All Tourist Spots Table -->
    <div class="modal fade" id="customYearModalSpotsTable" tabindex="-1" aria-labelledby="customYearModalSpotsTableLabel" aria-hidden="true" data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customYearModalSpotsTableLabel">Select Year for All Tourist Spots</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="number" id="customYearInputSpotsTable" class="form-control" placeholder="Enter year (e.g., 2023)" min="{{ $minYear }}" max="{{ $maxYear }}">
                    <div id="customYearErrorSpotsTable" class="invalid-feedback"></div>
                </div>
                <div class="modal-footer modal-footer-centered">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="applyCustomYearSpotsTable">Apply</button>
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
                        <option value="custom_year">Custom Year</option>
                    </select>
                    <table class="table">
                        <thead><tr><th>Spot Name</th><th>Visits</th></tr></thead>
                        <tbody id="spotsTableBody"></tbody>
                    </table>
                    <div class="loading-state" style="display: none;">Loading...</div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

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

    var minYear = {{ $minYear }};
    var maxYear = {{ $maxYear }};

    var statusCtx = document.getElementById('incidentStatusChart').getContext('2d');
    var statusColors = {
        'Pending': '#FFC107',
        'Cancelled': '#DC3545',
        'Reported': '#28A745',
        'Ignored': '#6C757D'
    };

    var incidentStatusLabels = {!! json_encode($incidentStatusLabels) !!};
    var incidentStatusData = {!! json_encode($incidentStatusData) !!};

    var backgroundColors = incidentStatusLabels.map(label => statusColors[label] || '#6C757D');

    var statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: incidentStatusLabels,
            datasets: [{
                data: incidentStatusData,
                backgroundColor: backgroundColors,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            legend: { 
                position: 'right', 
                labels: { 
                    boxWidth: 12, 
                    fontFamily: 'Poppins',
                    generateLabels: function(chart) {
                        var data = chart.data;
                        if (data.labels.length && data.datasets.length) {
                            return data.labels.map(function(label, i) {
                                var meta = chart.getDatasetMeta(0);
                                var ds = data.datasets[0];
                                var arc = meta.data[i];
                                var custom = arc && arc.custom || {};
                                var getValueAtIndexOrDefault = Chart.helpers.getValueAtIndexOrDefault;
                                var arcOpts = chart.options.elements.arc;
                                
                                var fill = statusColors[label] || getValueAtIndexOrDefault(ds.backgroundColor, i, arcOpts.backgroundColor);
                                
                                return {
                                    text: label,
                                    fillStyle: fill,
                                    strokeStyle: custom.borderColor ? custom.borderColor : getValueAtIndexOrDefault(ds.borderColor, i, arcOpts.borderColor),
                                    lineWidth: custom.borderWidth ? custom.borderWidth : getValueAtIndexOrDefault(ds.borderWidth, i, arcOpts.borderWidth),
                                    hidden: isNaN(ds.data[i]) || meta.data[i].hidden,
                                    index: i
                                };
                            });
                        }
                        return [];
                    }
                }
            },
            tooltips: { 
                titleFontFamily: 'Poppins', 
                bodyFontFamily: 'Poppins',
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.labels[tooltipItem.index];
                        var value = data.datasets[0].data[tooltipItem.index];
                        return label + ': ' + value;
                    }
                }
            },
            cutoutPercentage: 70
        }
    });

    // Handle initial load with no data
    if (incidentStatusLabels.length === 0) {
        $('#incidentStatusChart').hide();
        $('#incidentError').text('No data found for today.').show();
    } else {
        $('#incidentStatusChart').show();
        $('#incidentError').hide();
    }

    var previousIncidentFilter = 'today';

    $('#incidentPeriodFilter').change(function() {
        var period = $(this).val();
        $('#incidentError').hide();
        if (period === 'custom_year') {
            $('#customYearModal').modal('show');
        } else if (period.startsWith('custom_year_')) {
            var year = period.split('_')[2];
            previousIncidentFilter = period;
            $('#incidentChartBody').addClass('loading');
            $.get('/admin/analytics/incident-status?period=custom_year&year=' + year)
                .done(function(data) {
                    if (data.labels.length === 0) {
                        statusChart.data.labels = [];
                        statusChart.data.datasets[0].data = [];
                        statusChart.data.datasets[0].backgroundColor = [];
                        statusChart.update();
                        $('#incidentStatusChart').hide();
                        $('#incidentError').text('No data found for the selected period.').show();
                    } else {
                        $('#incidentError').hide();
                        $('#incidentStatusChart').show();
                        statusChart.data.labels = data.labels;
                        statusChart.data.datasets[0].data = data.values;
                        statusChart.data.datasets[0].backgroundColor = data.labels.map(label => statusColors[label] || '#6C757D');
                        statusChart.update();
                    }
                })
                .fail(function(xhr) {
                    statusChart.data.labels = [];
                    statusChart.data.datasets[0].data = [];
                    statusChart.data.datasets[0].backgroundColor = [];
                    statusChart.update();
                    $('#incidentStatusChart').hide();
                    $('#incidentError').text('Failed to load data for Year ' + year).show();
                })
                .always(function() {
                    $('#incidentChartBody').removeClass('loading');
                });
        } else {
            $('#incidentPeriodFilter option[value^="custom_year_"]').remove();
            previousIncidentFilter = period;
            $('#incidentChartBody').addClass('loading');
            $.get('/admin/analytics/incident-status?period=' + period)
                .done(function(data) {
                    if (data.labels.length === 0) {
                        statusChart.data.labels = [];
                        statusChart.data.datasets[0].data = [];
                        statusChart.data.datasets[0].backgroundColor = [];
                        statusChart.update();
                        $('#incidentStatusChart').hide();
                        $('#incidentError').text('No data found for the selected period.').show();
                    } else {
                        $('#incidentError').hide();
                        $('#incidentStatusChart').show();
                        statusChart.data.labels = data.labels;
                        statusChart.data.datasets[0].data = data.values;
                        statusChart.data.datasets[0].backgroundColor = data.labels.map(label => statusColors[label] || '#6C757D');
                        statusChart.update();
                    }
                })
                .fail(function(xhr) {
                    statusChart.data.labels = [];
                    statusChart.data.datasets[0].data = [];
                    statusChart.data.datasets[0].backgroundColor = [];
                    statusChart.update();
                    $('#incidentStatusChart').hide();
                    if (xhr.status === 404) {
                        $('#incidentError').text('No data found for the selected period.').show();
                    } else {
                        $('#incidentError').text('Failed to load data for the selected period.').show();
                    }
                })
                .always(function() {
                    $('#incidentChartBody').removeClass('loading');
                });
        }
    });

    $('#applyCustomYear').click(function() {
        var $button = $(this);
        $button.addClass('loading').prop('disabled', true);
        var year = $('#customYearInput').val();
        $('#customYearError').text('');
        $('#incidentError').hide();
        if (year && /^\d{4}$/.test(year) && year >= minYear && year <= maxYear) {
            $('#incidentChartBody').addClass('loading');
            $.get('/admin/analytics/incident-status?period=custom_year&year=' + year)
                .done(function(data) {
                    if (data.labels.length === 0) {
                        statusChart.data.labels = [];
                        statusChart.data.datasets[0].data = [];
                        statusChart.data.datasets[0].backgroundColor = [];
                        statusChart.update();
                        $('#incidentStatusChart').hide();
                        $('#incidentError').text('No data found for the selected period.').show();
                    } else {
                        $('#incidentError').hide();
                        $('#incidentStatusChart').show();
                        statusChart.data.labels = data.labels;
                        statusChart.data.datasets[0].data = data.values;
                        statusChart.data.datasets[0].backgroundColor = data.labels.map(label => statusColors[label] || '#6C757D');
                        statusChart.update();
                        $('#incidentPeriodFilter option[value^="custom_year_"]').remove();
                        $('#incidentPeriodFilter').append(`<option value="custom_year_${year}" selected>Year ${year}</option>`);
                        previousIncidentFilter = 'custom_year_' + year;
                        $('#customYearModal').data('applied', true);
                    }
                })
                .fail(function(xhr) {
                    statusChart.data.labels = [];
                    statusChart.data.datasets[0].data = [];
                    statusChart.data.datasets[0].backgroundColor = [];
                    statusChart.update();
                    $('#incidentStatusChart').hide();
                    $('#incidentError').text('Failed to load data for Year ' + year).show();
                })
                .always(function() {
                    $('#incidentChartBody').removeClass('loading');
                    $button.removeClass('loading').prop('disabled', false);
                    $('#customYearModal').modal('hide');
                });
        } else {
            $('#customYearInput').addClass('is-invalid');
            $('#customYearError').text('Please enter a valid 4-digit year between ' + minYear + ' and ' + maxYear + '.');
            $button.removeClass('loading').prop('disabled', false);
        }
    });

    $('#customYearInput').on('input', function() {
        $(this).removeClass('is-invalid');
        $('#customYearError').text('');
    });

    $('#customYearModal').on('show.bs.modal', function () {
        $('#customYearInput').val('');
        $('#customYearError').text('');
    });

    $('#customYearModal').on('hidden.bs.modal', function () {
        if (!$('#customYearModal').data('applied')) {
            $('#incidentPeriodFilter option[value^="custom_year_"]').remove();
            $('#incidentPeriodFilter').val(previousIncidentFilter);
        }
        $('#customYearModal').data('applied', false);
    });

    var touristActivitiesCtx = document.getElementById('touristActivitiesChart').getContext('2d');
    var touristActivitiesChart = new Chart(touristActivitiesCtx, {
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

    var previousTouristFilter = 'this_week';

    $('#touristPeriodFilter').change(function() {
        var period = $(this).val();
        $('#touristError').hide();
        if (period === 'custom_year') {
            $('#customYearModalTourist').modal('show');
        } else if (period.startsWith('custom_year_')) {
            var year = period.split('_')[2];
            previousTouristFilter = period;
            $('#touristChartBody').addClass('loading');
            $.get('/admin/analytics/tourist-activities?period=custom_year&year=' + year + '&_=' + new Date().getTime())
                .done(function(data) {
                    if (data.error) {
                        $('#touristError').text(data.error).show();
                        $('#touristMetrics').hide();
                        $('#touristActivitiesChart').hide();
                    } else {
                        $('#touristError').hide();
                        $('#touristMetrics').show();
                        $('#touristActivitiesChart').show();
                        touristActivitiesChart.data.labels = data.labels;
                        touristActivitiesChart.data.datasets[0].data = data.checkins;
                        touristActivitiesChart.data.datasets[1].data = data.incidents;
                        touristActivitiesChart.update();
                        $('#totalActivitiesValue').text(data.totalActivities);
                        $('#totalActivitiesLabel').text('Total Activities ' + data.period);
                        $('#totalIncidentsValue').text(data.totalIncidents);
                        $('#totalIncidentsLabel').text('Total Incident Reports ' + data.period);
                    }
                })
                .fail(function(xhr) {
                    $('#touristError').text('Failed to load data for Year ' + year).show();
                    $('#touristMetrics').hide();
                    $('#touristActivitiesChart').hide();
                })
                .always(function() {
                    $('#touristChartBody').removeClass('loading');
                });
        } else {
            $('#touristPeriodFilter option[value^="custom_year_"]').remove();
            previousTouristFilter = period;
            $('#touristChartBody').addClass('loading');
            $.get('/admin/analytics/tourist-activities?period=' + period + '&_=' + new Date().getTime())
                .done(function(data) {
                    if (data.error) {
                        $('#touristError').text(data.error).show();
                        $('#touristMetrics').hide();
                        $('#touristActivitiesChart').hide();
                    } else {
                        $('#touristError').hide();
                        $('#touristMetrics').show();
                        $('#touristActivitiesChart').show();
                        touristActivitiesChart.data.labels = data.labels;
                        touristActivitiesChart.data.datasets[0].data = data.checkins;
                        touristActivitiesChart.data.datasets[1].data = data.incidents;
                        touristActivitiesChart.update();
                        $('#totalActivitiesValue').text(data.totalActivities);
                        $('#totalActivitiesLabel').text('Total Activities ' + data.period);
                        $('#totalIncidentsValue').text(data.totalIncidents);
                        $('#totalIncidentsLabel').text('Total Incident Reports ' + data.period);
                    }
                })
                .fail(function(xhr) {
                    if (xhr.status === 404) {
                        $('#touristError').text('No data available for the selected period.').show();
                    } else {
                        $('#touristError').text('Failed to load data for the selected period.').show();
                    }
                    $('#touristMetrics').hide();
                    $('#touristActivitiesChart').hide();
                })
                .always(function() {
                    $('#touristChartBody').removeClass('loading');
                });
        }
    });

    $('#applyCustomYearTourist').click(function() {
        var $button = $(this);
        $button.addClass('loading').prop('disabled', true);
        var year = $('#customYearInputTourist').val();
        $('#customYearErrorTourist').text('');
        $('#touristError').hide();
        if (year && /^\d{4}$/.test(year) && year >= minYear && year <= maxYear) {
            $('#touristChartBody').addClass('loading');
            $.get('/admin/analytics/tourist-activities?period=custom_year&year=' + year + '&_=' + new Date().getTime())
                .done(function(data) {
                    if (data.error) {
                        $('#touristError').text(data.error).show();
                        $('#touristMetrics').hide();
                        $('#touristActivitiesChart').hide();
                    } else {
                        $('#touristError').hide();
                        $('#touristMetrics').show();
                        $('#touristActivitiesChart').show();
                        touristActivitiesChart.data.labels = data.labels;
                        touristActivitiesChart.data.datasets[0].data = data.checkins;
                        touristActivitiesChart.data.datasets[1].data = data.incidents;
                        touristActivitiesChart.update();
                        $('#totalActivitiesValue').text(data.totalActivities);
                        $('#totalActivitiesLabel').html('Total Activities <span class="edit-year" style="cursor: pointer; color: #FF7E3F;" title="Change year">' + data.period + '</span>');
                        $('#totalIncidentsValue').text(data.totalIncidents);
                        $('#totalIncidentsLabel').html('Total Incident Reports <span class="edit-year" style="cursor: pointer; color: #FF6B6B;" title="Change year">' + data.period + '</span>');
                        $('#touristPeriodFilter option[value^="custom_year_"]').remove();
                        $('#touristPeriodFilter').append(`<option value="custom_year_${year}" selected>Year ${year}</option>`);
                        previousTouristFilter = 'custom_year_' + year;
                        $('#customYearModalTourist').data('applied', true);
                    }
                })
                .fail(function(xhr) {
                    $('#touristError').text('Failed to load data for Year ' + year).show();
                    $('#touristMetrics').hide();
                    $('#touristActivitiesChart').hide();
                })
                .always(function() {
                    $('#touristChartBody').removeClass('loading');
                    $button.removeClass('loading').prop('disabled', false);
                    $('#customYearModalTourist').modal('hide');
                });
        } else {
            $('#customYearInputTourist').addClass('is-invalid');
            $('#customYearErrorTourist').text('Please enter a valid 4-digit year between ' + minYear + ' and ' + maxYear + '.');
            $button.removeClass('loading').prop('disabled', false);
        }
    });

    $('#customYearInputTourist').on('input', function() {
        $(this).removeClass('is-invalid');
        $('#customYearErrorTourist').text('');
    });

    $('#customYearModalTourist').on('show.bs.modal', function () {
        $('#customYearInputTourist').val('');
        $('#customYearErrorTourist').text('');
    });

    $('#customYearModalTourist').on('hidden.bs.modal', function () {
        if (!$('#customYearModalTourist').data('applied')) {
            $('#touristPeriodFilter option[value^="custom_year_"]').remove();
            $('#touristPeriodFilter').val(previousTouristFilter);
        }
        $('#customYearModalTourist').data('applied', false);
    });

    $(document).on('click', '.edit-year', function() {
        $('#customYearModalTourist').modal('show');
    });

    var userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    var userGrowthChart = new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'New Tourists',
                data: [],
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
                xAxes: [{
                    ticks: {
                        fontFamily: 'Poppins',
                        autoSkip: true,
                        maxTicksLimit: 20,
                        maxRotation: 45,
                        minRotation: 45
                    },
                    gridLines: { display: false }
                }]
            },
            legend: { labels: { fontFamily: 'Poppins' } },
            tooltips: { titleFontFamily: 'Poppins', bodyFontFamily: 'Poppins' }
        }
    });

    var previousUserGrowthFilter = 'this_month';

    function updateUserGrowthChart(period, year = null) {
        $('#userGrowthChartBody').addClass('loading');
        var url = '/admin/analytics/tourist-growth?period=' + period;
        if (year) {
            url += '&year=' + year;
        }
        $.get(url)
            .done(function(data) {
                if (data.data.length === 0) {
                    $('#userGrowthError').text('No data available for the selected period.').show();
                    $('#userGrowthChart').hide();
                } else {
                    $('#userGrowthError').hide();
                    $('#userGrowthChart').show();
                    userGrowthChart.data.labels = data.labels;
                    userGrowthChart.data.datasets[0].data = data.data;
                    userGrowthChart.update();
                }
            })
            .fail(function(xhr) {
                $('#userGrowthError').text('Failed to load data.').show();
                $('#userGrowthChart').hide();
            })
            .always(function() {
                $('#userGrowthChartBody').removeClass('loading');
            });
    }

    updateUserGrowthChart('this_month');

    $('#userGrowthPeriodFilter').change(function() {
        var period = $(this).val();
        if (period === 'custom_year') {
            $('#customYearModalUserGrowth').modal('show');
        } else if (period.startsWith('custom_year_')) {
            var year = period.split('_')[2];
            previousUserGrowthFilter = period;
            updateUserGrowthChart('custom_year', year);
        } else {
            $('#userGrowthPeriodFilter option[value^="custom_year_"]').remove();
            previousUserGrowthFilter = period;
            updateUserGrowthChart(period);
        }
    });

    $('#applyCustomYearUserGrowth').click(function() {
        var $button = $(this);
        $button.addClass('loading').prop('disabled', true);
        var year = $('#customYearInputUserGrowth').val();
        $('#customYearErrorUserGrowth').text('');
        if (year && /^\d{4}$/.test(year) && year >= minYear && year <= maxYear) {
            updateUserGrowthChart('custom_year', year);
            $('#userGrowthPeriodFilter option[value^="custom_year_"]').remove();
            $('#userGrowthPeriodFilter').append(`<option value="custom_year_${year}" selected>Year ${year}</option>`);
            previousUserGrowthFilter = 'custom_year_' + year;
            $('#customYearModalUserGrowth').data('applied', true);
            $button.removeClass('loading').prop('disabled', false);
            $('#customYearModalUserGrowth').modal('hide');
        } else {
            $('#customYearInputUserGrowth').addClass('is-invalid');
            $('#customYearErrorUserGrowth').text('Please enter a valid 4-digit year between ' + minYear + ' and ' + maxYear + '.');
            $button.removeClass('loading').prop('disabled', false);
        }
    });

    $('#customYearInputUserGrowth').on('input', function() {
        $(this).removeClass('is-invalid');
        $('#customYearErrorUserGrowth').text('');
    });

    $('#customYearModalUserGrowth').on('show.bs.modal', function () {
        $('#customYearInputUserGrowth').val('');
        $('#customYearErrorUserGrowth').text('');
    });

    $('#customYearModalUserGrowth').on('hidden.bs.modal', function () {
        if (!$('#customYearModalUserGrowth').data('applied')) {
            $('#userGrowthPeriodFilter option[value^="custom_year_"]').remove();
            $('#userGrowthPeriodFilter').val(previousUserGrowthFilter);
        }
        $('#customYearModalUserGrowth').data('applied', false);
    });

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

    var previousSpotsFilter = 'all_time';

    $('#spotsFilterChart').change(function() {
        var filter = $(this).val();
        $('#spotsError').hide();
        if (filter === 'custom_year') {
            $('#customYearModalSpots').modal('show');
        } else if (filter.startsWith('custom_year_')) {
            var year = filter.split('_')[2];
            previousSpotsFilter = filter;
            $('#popularSpotsChartBody').addClass('loading');
            $.get(`/admin/analytics/popular-spots?filter=custom_year&year=${year}`)
                .done(function(data) {
                    if (data.error) {
                        $('#spotsError').text(data.error).show();
                        $('#popularSpotsChart').hide();
                    } else {
                        $('#spotsError').hide();
                        $('#popularSpotsChart').show();
                        var labels = data.map(item => item.spot);
                        var values = data.map(item => item.visits);
                        popularSpotsChart.data.labels = labels;
                        popularSpotsChart.data.datasets[0].data = values;
                        popularSpotsChart.update();
                    }
                })
                .fail(function(xhr) {
                    $('#spotsError').text('Failed to load data for Year ' + year).show();
                    $('#popularSpotsChart').hide();
                })
                .always(function() {
                    $('#popularSpotsChartBody').removeClass('loading');
                });
        } else {
            $('#spotsFilterChart option[value^="custom_year_"]').remove();
            previousSpotsFilter = filter;
            $('#popularSpotsChartBody').addClass('loading');
            $.get(`/admin/analytics/popular-spots?filter=${filter}`)
                .done(function(data) {
                    if (data.error) {
                        $('#spotsError').text(data.error).show();
                        $('#popularSpotsChart').hide();
                    } else {
                        $('#spotsError').hide();
                        $('#popularSpotsChart').show();
                        var labels = data.map(item => item.spot);
                        var values = data.map(item => item.visits);
                        popularSpotsChart.data.labels = labels;
                        popularSpotsChart.data.datasets[0].data = values;
                        popularSpotsChart.update();
                    }
                })
                .fail(function(xhr) {
                    $('#spotsError').text('Failed to load data for the selected period.').show();
                    $('#popularSpotsChart').hide();
                })
                .always(function() {
                    $('#popularSpotsChartBody').removeClass('loading');
                });
        }
    });

    $('#spotsFilterChart').trigger('change');

    $('#applyCustomYearSpots').click(function() {
        var $button = $(this);
        $button.addClass('loading').prop('disabled', true);
        var year = $('#customYearInputSpots').val();
        $('#customYearErrorSpots').text('');
        $('#spotsError').hide();
        if (year && /^\d{4}$/.test(year) && year >= minYear && year <= maxYear) {
            $('#popularSpotsChartBody').addClass('loading');
            $.get(`/admin/analytics/popular-spots?filter=custom_year&year=${year}`)
                .done(function(data) {
                    if (data.error) {
                        $('#spotsError').text(data.error).show();
                        $('#popularSpotsChart').hide();
                    } else {
                        $('#spotsError').hide();
                        $('#popularSpotsChart').show();
                        var labels = data.map(item => item.spot);
                        var values = data.map(item => item.visits);
                        popularSpotsChart.data.labels = labels;
                        popularSpotsChart.data.datasets[0].data = values;
                        popularSpotsChart.update();
                        $('#spotsFilterChart option[value^="custom_year_"]').remove();
                        $('#spotsFilterChart').append(`<option value="custom_year_${year}" selected>Year ${year}</option>`);
                        previousSpotsFilter = 'custom_year_' + year;
                        $('#customYearModalSpots').data('applied', true);
                    }
                })
                .fail(function(xhr) {
                    $('#spotsError').text('Failed to load data for Year ' + year).show();
                    $('#popularSpotsChart').hide();
                })
                .always(function() {
                    $('#popularSpotsChartBody').removeClass('loading');
                    $button.removeClass('loading').prop('disabled', false);
                    $('#customYearModalSpots').modal('hide');
                });
        } else {
            $('#customYearInputSpots').addClass('is-invalid');
            $('#customYearErrorSpots').text('Please enter a valid 4-digit year between ' + minYear + ' and ' + maxYear + '.');
            $button.removeClass('loading').prop('disabled', false);
        }
    });

    var map = L.map('activityMap').setView([7.08, 125.6], 11);
    var mapTileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    map.tileLayer = mapTileLayer;

    var spots = {!! json_encode($touristSpots) !!};
    var incidents = {!! json_encode($incidents) !!};
    var checkins = {!! json_encode($checkins) !!};
    var users = {!! json_encode($users) !!};

    function getIncidentIcon(status) {
        var color = statusColors[status] || '#6C757D';
        return L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background-color: ${color}; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white;"></div>`,
            iconSize: [12, 12],
            iconAnchor: [6, 6]
        });
    }

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

    var LegendControl = L.Control.extend({
        options: { position: 'bottomright' },
        onAdd: function(map) {
            var div = L.DomUtil.create('div', 'leaflet-control-legend');
            div.innerHTML = `
                <div class="legend-item"><div class="legend-color" style="background:#4ECDC4;"></div> Check-ins</div>
                <div class="legend-item"><div class="legend-color" style="background:#FFC107;"></div> Pending Incidents</div>
                <div class="legend-item"><div class="legend-color" style="background:#DC3545;"></div> Cancelled Incidents</div>
                <div class="legend-item"><div class="legend-color" style="background:#28A745;"></div> Reported Incidents</div>
                <div class="legend-item"><div class="legend-color" style="background:#6C757D;"></div> Not Specified Incidents</div>
            `;
            return div;
        }
    });

    new LegendControl().addTo(map);

    $('#mapFilter').change(function() {
        var filter = $(this).val();
        map.eachLayer(function(layer) {
            if (layer !== map.tileLayer && !(layer instanceof LegendControl)) {
                map.removeLayer(layer);
            }
        });
        if (!map.hasLayer(map.tileLayer)) {
            map.tileLayer.addTo(map);
        }
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
            }
        }
        if (filter === 'all' || filter === 'incidents') {
            if (incidents.length > 0) {
                incidents.forEach(function(incident) {
                    L.marker([incident.latitude, incident.longitude], {icon: getIncidentIcon(incident.status)})
                        .addTo(map)
                        .bindPopup(`Incident - ${incident.status}<br>Tourist ID: ${incident.user_id}`);
                });
            }
        }
    });

    $('.view-all-spots').click(function() {
        $('#spotsModal').modal('show');
        updateSpotsTable('today');
    });

    $('#spotsFilter').change(function() {
        var filter = $(this).val();
        if (filter === 'custom_year') {
            $('#customYearModalSpotsTable').modal('show');
        } else {
            updateSpotsTable(filter);
        }
    });

    $('#applyCustomYearSpotsTable').click(function() {
        var $button = $(this);
        $button.addClass('loading').prop('disabled', true);
        var year = $('#customYearInputSpotsTable').val();
        $('#customYearErrorSpotsTable').text('');
        if (year && /^\d{4}$/.test(year) && year >= minYear && year <= maxYear) {
            updateSpotsTable('custom_year', year);
            $('#spotsFilter option[value^="custom_year_"]').remove();
            $('#spotsFilter').append(`<option value="custom_year_${year}" selected>Year ${year}</option>`);
            $('#customYearModalSpotsTable').data('applied', true);
            $button.removeClass('loading').prop('disabled', false);
            $('#customYearModalSpotsTable').modal('hide');
        } else {
            $('#customYearInputSpotsTable').addClass('is-invalid');
            $('#customYearErrorSpotsTable').text('Please enter a valid 4-digit year between ' + minYear + ' and ' + maxYear + '.');
            $button.removeClass('loading').prop('disabled', false);
        }
    });

    $('#customYearInputSpotsTable').on('input', function() {
        $(this).removeClass('is-invalid');
        $('#customYearErrorSpotsTable').text('');
    });

    $('#customYearModalSpotsTable').on('show.bs.modal', function() {
        $('#customYearInputSpotsTable').val('');
        $('#customYearErrorSpotsTable').text('');
    });

    $('#customYearModalSpotsTable').on('hidden.bs.modal', function() {
        if (!$('#customYearModalSpotsTable').data('applied')) {
            $('#spotsFilter option[value^="custom_year_"]').remove();
            $('#spotsFilter').val('today');
            updateSpotsTable('today');
        }
        $('#customYearModalSpotsTable').data('applied', false);
    });

    function updateSpotsTable(filter, year = null, page = 1) {
        var $modalBody = $('#spotsModal .modal-body');
        $modalBody.addClass('loading');
        var url = `/admin/analytics/popular-spots?filter=${filter}&all=1&page=${page}&modal=true`;
        if (filter === 'custom_year' && year) {
            url += `&year=${year}`;
        } else if (filter.startsWith('custom_year_')) {
            var extractedYear = filter.split('_')[2];
            url = url.replace(`filter=${filter}`, `filter=custom_year&year=${extractedYear}`);
        }
        $('#spotsTableBody').hide();
        $('.loading-state', $modalBody).show();
        $.get(url)
            .done(function(response) {
                $('#spotsTableBody').empty();
                if (response.error) {
                    $('#spotsTableBody').html(`<tr><td colspan="2" class="text-center text-danger">${response.error}</td></tr>`);
                } else if (response.data.length === 0) {
                    $('#spotsTableBody').html('<tr><td colspan="2" class="text-center">No data available for the selected period.</td></tr>');
                } else {
                    response.data.forEach(spot => {
                        $('#spotsTableBody').append(`<tr><td>${spot.spot}</td><td>${spot.visits}</td></tr>`);
                    });
                    
                    $('#spotsModal .pagination').remove();
                    var totalPages = Math.ceil(response.total / response.per_page);
                    if (totalPages > 1) {
                        var paginationHtml = `<nav aria-label="Page navigation" class="d-flex justify-content-center">
                            <ul class="pagination mt-3">`;
                        paginationHtml += `<li class="page-item ${page === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${page - 1}">< Previous</a></li>`;
                        for (var i = 1; i <= totalPages; i++) {
                            paginationHtml += `<li class="page-item ${i === page ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                        }
                        paginationHtml += `<li class="page-item ${page === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${page + 1}">Next ></a></li>`;
                        paginationHtml += `</ul></nav>`;
                        $('#spotsTableBody').after(paginationHtml);
                    }
                }
                $('#spotsTableBody').show();
            })
            .fail(function(xhr, status, error) {
                $('#spotsTableBody').html(`<tr><td colspan="2" class="text-center text-danger">Failed to load data: ${error}</td></tr>`);
                $('#spotsTableBody').show();
            })
            .always(function() {
                $modalBody.removeClass('loading');
                $('.loading-state', $modalBody).hide();
            });
    }

    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        var filter = $('#spotsFilter').val();
        var year = filter.startsWith('custom_year_') ? filter.split('_')[2] : null;
        updateSpotsTable(filter, year, page);
    });

    $('#spotsModal').on('shown.bs.modal', function () {
        $('.modal-backdrop').on('click', function () {
            $('#spotsModal').modal('hide');
        });
    });

    window.onpopstate = function(event) {
        $('#spotsModal').modal('hide');
        $('.modal-backdrop').remove();
    };

    $('.export-btn').click(function() {
        var labels = touristActivitiesChart.data.labels;
        var checkinsData = touristActivitiesChart.data.datasets[0] ? touristActivitiesChart.data.datasets[0].data : [];
        var incidentsData = touristActivitiesChart.data.datasets[1] ? touristActivitiesChart.data.datasets[1].data : [];
        var csv = "Period,Tourist Activities,Incident Reports\n";
        for (var i = 0; i < labels.length; i++) {
            csv += `${labels[i]},${checkinsData[i] || 0},${incidentsData[i] || 0}\n`;
        }
        var blob = new Blob([csv], {type: 'text/csv'});
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'tourist_activities.csv';
        link.click();
    });

    $('.export-btn-pdf').click(function() {
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

        addSectionHeader("TOURIST ACTIVITIES");
        doc.text(`Period: ${$('#totalActivitiesLabel').text().replace('Total Activities ', '').replace(/<[^>]+>/g, '')}`, 12, yOffset + 5);
        doc.text(`Total Activities: ${$('#totalActivitiesValue').text()}`, 12, yOffset + 10);
        doc.text(`Total Incident Reports: ${$('#totalIncidentsValue').text()}`, 12, yOffset + 15);
        yOffset += 20;

        const headers = ["Period", "Tourist Activities", "Incident Reports"];
        const rows = touristActivitiesChart.data.labels.map((label, i) => [
            label,
            touristActivitiesChart.data.datasets[0] ? touristActivitiesChart.data.datasets[0].data[i] || 0 : 0,
            touristActivitiesChart.data.datasets[1] ? touristActivitiesChart.data.datasets[1].data[i] || 0 : 0
        ]);
        yOffset = addTable(headers, rows, yOffset);

        const pageCount = doc.internal.getNumberOfPages();
        for (let i = 1; i <= pageCount; i++) {
            doc.setPage(i);
            doc.setFontSize(8);
            doc.setTextColor(150, 150, 150);
            doc.text(`Page ${i} of ${pageCount}`, pageWidth - 20, doc.internal.pageSize.getHeight() - 10);
        }

        const currentDate = new Date().toISOString().slice(0,10);
        doc.save(`TRAKS_Tourist_Activities_Export_${currentDate}.pdf`);
    });

    // Function to update stats cards
    function updateStatsCards() {
        $.get('/admin/analytics/stats', function(data) {
            $('#touristArrivals .stat-number').text(data.touristArrivals);
            $('#touristArrivals .stat-change').html(`<i class="fas ${data.touristChangeIcon}"></i> ${data.touristChange}% from yesterday`).attr('class', 'stat-change ' + data.touristChangeClass);
            $('#incidentReports .stat-number').text(data.incidentReports);
            $('#incidentReports .stat-change').html(`<i class="fas ${data.incidentChangeIcon}"></i> ${data.incidentChange}% from last week`).attr('class', 'stat-change ' + data.incidentChangeClass);
            $('#touristAccounts .stat-number').text(data.touristAccounts);
            $('#touristAccounts .stat-change').html(`<i class="fas ${data.touristAccountsChangeIcon}"></i> ${data.touristAccountsChange}% from last month`).attr('class', 'stat-change ' + data.touristAccountsChangeClass);
            $('#adminAccounts .stat-number').text(data.adminAccounts);
        });
    }

    // Function to update charts
    function updateCharts() {
        var incidentPeriod = $('#incidentPeriodFilter').val();
        $.get('/admin/analytics/incident-status?period=' + incidentPeriod, function(data) {
            if (data.labels.length > 0) {
                statusChart.data.labels = data.labels;
                statusChart.data.datasets[0].data = data.values;
                statusChart.data.datasets[0].backgroundColor = data.labels.map(label => statusColors[label] || '#6C757D');
                statusChart.update();
                $('#incidentStatusChart').show();
                $('#incidentError').hide();
            } else {
                statusChart.data.labels = [];
                statusChart.data.datasets[0].data = [];
                statusChart.data.datasets[0].backgroundColor = [];
                statusChart.update();
                $('#incidentStatusChart').hide();
                $('#incidentError').text('No data found for the selected period.').show();
            }
        });

        var touristPeriod = $('#touristPeriodFilter').val();
        $.get('/admin/analytics/tourist-activities?period=' + touristPeriod, function(data) {
            if (!data.error) {
                touristActivitiesChart.data.labels = data.labels;
                touristActivitiesChart.data.datasets[0].data = data.checkins;
                touristActivitiesChart.data.datasets[1].data = data.incidents;
                touristActivitiesChart.update();
                $('#totalActivitiesValue').text(data.totalActivities);
                $('#totalIncidentsValue').text(data.totalIncidents);
            }
        });
    }

    // Initial call and set interval (every 60 seconds)
    updateStatsCards();
    updateCharts();
    setInterval(updateStatsCards, 60000);
    setInterval(updateCharts, 60000);   
});
</script>
@endsection