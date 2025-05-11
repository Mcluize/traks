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
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month" selected>This Month</option>
                            <option value="year">This Year</option>
                            <option value="all_time">All Time</option>
                            <option value="custom_year">Custom Year</option>
                        </select>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="incidentStatusChart" height="250"></canvas>
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
                <div class="chart-body">
                    <div class="metric-highlight">
                        <div class="metric-value" id="totalActivitiesValue">{{ $totalActivities }}</div>
                        <div class="metric-label" id="totalActivitiesLabel">Total Activities This Week</div>
                    </div>
                    <canvas id="touristActivitiesChart" height="200"></canvas>
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
                        <input type="number" id="customYearInputTourist" class="form-control" placeholder="Enter year (e.g., 2023)" min="1900" max="2100">
                    </div>
                    <div class="modal-footer">
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

    <!-- Custom Year Modal -->
    <div class="modal fade" id="customYearModal" tabindex="-1" aria-labelledby="customYearModalLabel" aria-hidden="true" data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customYearModalLabel">Select Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="number" id="customYearInput" class="form-control" placeholder="Enter year (e.g., 2023)" min="1900" max="2100">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="applyCustomYear">Apply</button>
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

        var statusCtx = document.getElementById('incidentStatusChart').getContext('2d');
        var statusColors = {
            'Pending': '#FFC107',
            'Cancelled': '#DC3545',
            'Resolved': '#28A745',
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

        $('#incidentPeriodFilter').change(function() {
            var period = $(this).val();
            if (period === 'custom_year') {
                $('#customYearModal').modal('show');
            } else {
                $.get('/admin/analytics/incident-status?period=' + period, function(data) {
                    statusChart.data.labels = data.labels;
                    statusChart.data.datasets[0].data = data.values;
                    statusChart.data.datasets[0].backgroundColor = data.labels.map(label => statusColors[label] || '#6C757D');
                    statusChart.update();
                }).fail(function() {
                    alert('Failed to load data for the selected period.');
                });
            }
        });

        $('#applyCustomYear').click(function() {
            var year = $('#customYearInput').val();
            if (year && /^\d{4}$/.test(year)) {
                $.get('/admin/analytics/incident-status?period=custom_year&year=' + year, function(data) {
                    statusChart.data.labels = data.labels;
                    statusChart.data.datasets[0].data = data.values;
                    statusChart.data.datasets[0].backgroundColor = data.labels.map(label => statusColors[label] || '#6C757D');
                    statusChart.update();
                    $('#customYearModal').modal('hide');
                }).fail(function() {
                    alert('Failed to load data for the selected year.');
                });
            } else {
                $('#customYearInput').addClass('is-invalid');
                if (!$('#customYearError').length) {
                    $('#customYearInput').after('<div id="customYearError" class="invalid-feedback">Please enter a valid 4-digit year.</div>');
                }
            }
        });

        $('#customYearInput').on('input', function() {
            $(this).removeClass('is-invalid');
            $('#customYearError').remove();
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

        $('#touristPeriodFilter').change(function() {
            var period = $(this).val();
            if (period === 'custom_year') {
                $('#customYearModalTourist').modal('show');
            } else {
                $.get('/admin/analytics/tourist-activities?period=' + period, function(data) {
                    touristActivitiesChart.data.labels = data.labels;
                    touristActivitiesChart.data.datasets[0].data = data.checkins;
                    touristActivitiesChart.data.datasets[1].data = data.incidents;
                    touristActivitiesChart.update();
                    $('#totalActivitiesValue').text(data.totalActivities);
                    $('#totalActivitiesLabel').text('Total Activities ' + data.period);
                }).fail(function() {
                    console.error('Failed to load tourist activities data.');
                });
            }
        });

        $('#applyCustomYearTourist').click(function() {
            var year = $('#customYearInputTourist').val();
            if (year && /^\d{4}$/.test(year)) {
                $.get('/admin/analytics/tourist-activities?period=custom_year&year=' + year, function(data) {
                    touristActivitiesChart.data.labels = data.labels;
                    touristActivitiesChart.data.datasets[0].data = data.checkins;
                    touristActivitiesChart.data.datasets[1].data = data.incidents;
                    touristActivitiesChart.update();
                    $('#totalActivitiesValue').text(data.totalActivities);
                    $('#totalActivitiesLabel').text('Total Activities ' + data.period);
                    $('#customYearModalTourist').modal('hide');
                }).fail(function() {
                    alert('Failed to load data for the selected year.');
                });
            } else {
                $('#customYearInputTourist').addClass('is-invalid');
                if (!$('#customYearErrorTourist').length) {
                    $('#customYearInputTourist').after('<div id="customYearErrorTourist" class="invalid-feedback">Please enter a valid 4-digit year.</div>');
                }
            }
        });

        $('#customYearInputTourist').on('input', function() {
            $(this).removeClass('is-invalid');
            $('#customYearErrorTourist').remove();
        });

        $('.export-btn').click(function() {
            var labels = touristActivitiesChart.data.labels;
            var checkinsData = touristActivitiesChart.data.datasets[0].data;
            var incidentsData = touristActivitiesChart.data.datasets[1].data;
            var csv = "Period,Tourist Activities,Incident Reports\n";
            for (var i = 0; i < labels.length; i++) {
                csv += `${labels[i]},${checkinsData[i]},${incidentsData[i]}\n`;
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
            doc.text(`Period: ${$('#totalActivitiesLabel').text().replace('Total Activities ', '')}`, 12, yOffset + 5);
            doc.text(`Total Activities: ${$('#totalActivitiesValue').text()}`, 12, yOffset + 10);
            yOffset += 15;

            const headers = ["Period", "Tourist Activities", "Incident Reports"];
            const rows = touristActivitiesChart.data.labels.map((label, i) => [
                label,
                touristActivitiesChart.data.datasets[0].data[i],
                touristActivitiesChart.data.datasets[1].data[i]
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

        $('#spotsFilterChart').change(function() {
            var filter = $(this).val();
            $.get(`/admin/analytics/popular-spots/${filter}`, function(data) {
                var labels = data.map(item => item.spot);
                var values = data.map(item => item.visits);
                popularSpotsChart.data.labels = labels;
                popularSpotsChart.data.datasets[0].data = values;
                popularSpotsChart.update();
            }).fail(function(xhr, status, error) {
                console.error('Failed to fetch popular spots:', error);
            });
        });

        var map = L.map('activityMap').setView([7.08, 125.6], 11);
        var tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        map.tileLayer = tileLayer;

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
                div.inner567HTML = `
                    <div class="legend-item"><div class="legend-color" style="background:#4ECDC4;"></div> Check-ins</div>
                    <div class="legend-item"><div class="legend-color" style="background:#FFC107;"></div> Pending Incidents</div>
                    <div class="legend-item"><div class="legend-color" style="background:#DC3545;"></div> Cancelled Incidents</div>
                    <div class="legend-item"><div class="legend-color" style="background:#28A745;"></div> Resolved Incidents</div>
                    <div class="legend-item"><div class="legend-color" style="background:#6C757D;"></div> Ignored Incidents</div>
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

        $('#spotsModal').on('shown.bs.modal', function () {
            $('.modal-backdrop').on('click', function () {
                $('#spotsModal').modal('hide');
            });
        });

        window.onpopstate = function(event) {
            $('#spotsModal').modal('hide');
            $('.modal-backdrop').remove();
        };

        $('.view-all-reports').click(function() {
            window.location.href = '/admin/incidents';
        });
    });
</script>
@endsection