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
                        <a class="dropdown-item" href="#">Today</a>
                        <a class="dropdown-item" href="#">Past Week</a>
                        <a class="dropdown-item" href="#">Past Month</a>
                        <a class="dropdown-item" href="#">All Time</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="stats-number">210+</div>
        </div>
    </div>

    <!-- Active Tracking Card -->
    <div class="card stats-card active-tracking">
        <div class="card-header">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.515 11.1891C16.8722 11.1892 16.2549 11.4397 15.7937 11.8873L14.8129 11.397C15.6135 8.77141 14.134 5.99403 11.5085 5.19348C10.5481 4.90067 9.5215 4.90559 8.56396 5.20758L7.98908 4.16965C8.91181 3.15926 8.84076 1.59215 7.83037 0.669457C6.81998 -0.253277 5.25291 -0.182223 4.33017 0.828168C3.40744 1.83856 3.47849 3.40563 4.48888 4.32836C4.94724 4.74696 5.54603 4.97825 6.16673 4.97645C6.28587 4.97305 6.40462 4.96114 6.52208 4.94082L7.09197 5.97043C4.93341 7.57227 4.449 10.6047 6.00103 12.7993L3.58724 15.1186C2.30669 14.5451 0.803726 15.1183 0.230249 16.3988C-0.343227 17.6794 0.229937 19.1823 1.51048 19.7558C2.79103 20.3293 4.294 19.7561 4.86748 18.4756C5.18701 17.7621 5.16025 16.9413 4.79498 16.2501L7.16072 13.9763C9.3713 15.5774 12.4611 15.0847 14.0641 12.8754L15.0581 13.3724C15.0424 13.4722 15.0331 13.5729 15.03 13.6739C15.03 15.0464 16.1426 16.159 17.515 16.159C18.8875 16.159 20 15.0465 20 13.6741C20 12.3017 18.8874 11.1891 17.515 11.1891Z" fill="#484A58"/>
            </svg>
            Active<span>Tracking</span>
        </div>
        <div class="card-body">
            <div class="stats-number">112+</div>
        </div>
    </div>

    <!-- Incident Alerts Card -->
    <div class="card incidents-card">
        <div class="card-header">Incident<span>Alerts</span></div>
        <div class="card-body">
            <div class="incident-stats">
                <div class="incident high-risk">
                    <div class="incident-number">32</div>
                    <div class="incident-type">
                        <div class="incident-title">High Risk</div>
                        <div class="incident-description">Tourist with severe health incidents.</div>
                    </div>
                    <div class="incident-icon-container">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="9" cy="9" r="9" fill="#FF0000" fill-opacity="0.44"/>
                            <g transform="translate(3.5, 3.5) rotate(0 4 4.5)">
                                <path d="M2.06323 1.61511C2.05999 1.73683 2.08078 1.858 2.12439 1.97169C2.168 2.08537 2.23359 2.18935 2.3174 2.27768C2.40122 2.36601 2.50161 2.43695 2.61285 2.48647C2.7241 2.53598 2.84401 2.56309 2.96573 2.56624L5.09627 2.62213L1.15504 6.36186C0.976731 6.53106 0.872933 6.76416 0.866487 7.00989C0.860041 7.25562 0.951474 7.49384 1.12067 7.67216C1.28987 7.85047 1.52297 7.95427 1.7687 7.96071C2.01443 7.96716 2.25265 7.87573 2.43097 7.70653L6.37219 3.9668L6.3163 6.09734C6.3098 6.34309 6.40118 6.58136 6.57035 6.75973C6.73952 6.93809 6.97262 7.04196 7.21837 7.04846C7.46411 7.05497 7.70238 6.96358 7.88075 6.79441C8.05912 6.62524 8.16298 6.39214 8.16948 6.14639L8.25166 3.01392C8.25912 2.73 8.21058 2.44739 8.10882 2.18222C8.00706 1.91706 7.85406 1.67455 7.65856 1.46852C7.46307 1.26249 7.22891 1.09698 6.96944 0.981458C6.70998 0.865933 6.43031 0.802645 6.14639 0.795212L3.01391 0.713037C2.76823 0.706629 2.53006 0.798061 2.35178 0.967225C2.1735 1.13639 2.06971 1.36944 2.06323 1.61511Z" fill="#FF0000"/>
                            </g>
                        </svg>
                    </div>
                </div>
                <div class="incident medium-risk">
                    <div class="incident-number">12</div>
                    <div class="incident-type">
                        <div class="incident-title">Medium Risk</div>
                        <div class="incident-description">Tourist with minor safety issues.</div>
                    </div>
                    <div class="incident-icon-container">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="9" cy="9" r="9" fill="#FF7700" fill-opacity="0.44"/>
                            <g transform="translate(3.5, 3.5) rotate(0 4 4.5)">
                                <path d="M2.06326 1.61511C2.06002 1.73683 2.08081 1.858 2.12442 1.97169C2.16803 2.08537 2.23362 2.18935 2.31743 2.27768C2.40125 2.36601 2.50164 2.43695 2.61288 2.48647C2.72413 2.53598 2.84404 2.56309 2.96576 2.56624L5.0963 2.62213L1.15508 6.36186C0.976761 6.53106 0.872964 6.76416 0.866518 7.00989C0.860071 7.25562 0.951504 7.49384 1.1207 7.67216C1.2899 7.85047 1.523 7.95427 1.76873 7.96071C2.01446 7.96716 2.25268 7.87573 2.431 7.70653L6.37222 3.9668L6.31633 6.09734C6.30983 6.34309 6.40121 6.58136 6.57038 6.75973C6.73955 6.93809 6.97265 7.04196 7.2184 7.04846C7.46414 7.05497 7.70241 6.96358 7.88078 6.79441C8.05915 6.62524 8.16301 6.39214 8.16951 6.14639L8.25169 3.01392C8.25915 2.73 8.21062 2.44739 8.10885 2.18222C8.00709 1.91706 7.85409 1.67455 7.65859 1.46852C7.4631 1.26249 7.22894 1.09698 6.96947 0.981458C6.71001 0.865933 6.43034 0.802645 6.14642 0.795212L3.01394 0.713037C2.76826 0.706629 2.53009 0.798061 2.35181 0.967225C2.17353 1.13639 2.06974 1.36944 2.06326 1.61511Z" fill="#FF7700"/>
                            </g>
                        </svg>
                    </div>
                </div>
                <div class="incident resolved">
                    <div class="incident-number">01</div>
                    <div class="incident-type">
                        <div class="incident-title">Resolved</div>
                        <div class="incident-description">Resolved issues.</div>
                    </div>
                    <div class="incident-icon-container">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="9" cy="9" r="9" fill="#00A70B" fill-opacity="0.44"/>
                            <g transform="translate(3.5, 3.5) rotate(0 4 4.5)">
                                <path d="M2.09853 1.61511C2.0953 1.73683 2.11609 1.858 2.1597 1.97169C2.20331 2.08537 2.2689 2.18935 2.35271 2.27768C2.43652 2.36601 2.53692 2.43695 2.64816 2.48647C2.75941 2.53598 2.87931 2.56309 3.00104 2.56624L5.13158 2.62213L1.19035 6.36186C1.01204 6.53106 0.908242 6.76416 0.901796 7.00989C0.89535 7.25562 0.986782 7.49384 1.15598 7.67216C1.32518 7.85047 1.55828 7.95427 1.80401 7.96071C2.04974 7.96716 2.28796 7.87573 2.46628 7.70653L6.4075 3.9668L6.35161 6.09734C6.3451 6.34309 6.43649 6.58136 6.60566 6.75973C6.77483 6.93809 7.00793 7.04196 7.25367 7.04846C7.49942 7.05497 7.73769 6.96358 7.91606 6.79441C8.09443 6.62524 8.19829 6.39214 8.20479 6.14639L8.28697 3.01392C8.29443 2.73 8.24589 2.44739 8.14413 2.18222C8.04237 1.91706 7.88937 1.67455 7.69387 1.46852C7.49838 1.26249 7.26421 1.09698 7.00475 0.981458C6.74529 0.865933 6.46562 0.802645 6.1817 0.795212L3.04922 0.713037C2.80354 0.706629 2.56537 0.798061 2.38709 0.967225C2.20881 1.13639 2.10502 1.36944 2.09853 1.61511Z" fill="#00A70B"/>
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
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
                <div class="dropdown">
                    <button class="btn btn-link" type="button" id="mapDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="las la-ellipsis-v" style="color: #FF7E3F;"></i>
                    </button>
                    <div class="dropdown-menu custom-dropdown" aria-labelledby="mapDropdown">
                        <a class="dropdown-item" href="#">Today</a>
                        <a class="dropdown-item" href="#">Past Week</a>
                        <a class="dropdown-item" href="#">Past Month</a>
                        <a class="dropdown-item" href="#">All Time</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="map" class="map-container"></div>
        </div>
    </div>

    <!-- Manage Tourist -->
    <div class="card manage-card">
        <div class="card-header">Manage <span>Tourist</span></div>
        <div class="card-body">
            <div class="tourist-table-container">
                <table class="tourist-table">
                    <thead>
                        <tr>
                            <th>Tourist No.</th>
                            <th>Date Created</th>
                            <th>Contact No.</th>
                            <th>Account Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>T-1001</td>
                            <td>Apr 01, 2025</td>
                            <td>+639 12 345 6789</td>
                            <td>Premium</td>
                        </tr>
                        <tr>
                            <td>T-1002</td>
                            <td>Apr 02, 2025</td>
                            <td>+639 12 345 6789</td>
                            <td>Standard</td>
                        </tr>
                        <tr>
                            <td>T-1003</td>
                            <td>Apr 03, 2025</td>
                            <td>+639 12 345 6789</td>
                            <td>Premium</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="see-more-container">
                <button class="see-more-button">See More <i class="las la-arrow-right"></i></button>
            </div>
        </div>
    </div>

    <!-- Analytics -->
    <div class="card analytics-card">
        <div class="card-header">Analytics</div>
        <div class="card-body">
            <div class="analytics-header">
                <div class="analytics-total">
                    <h2>1532</h2>
                    <p>on this Week</p>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="analyticsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('map').setView([7.0767, 125.8259], 13);  
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    setTimeout(function() {
        map.invalidateSize();
    }, 500);

    var ctx = document.getElementById('analyticsChart').getContext('2d');
    var chartConfig = {
        type: 'bar',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: '',
                data: [30, 40, 35, 45],
                backgroundColor: document.body.classList.contains('dark-mode') ? '#8b8dff' : '#333',
                barThickness: 10,
                borderRadius: 2,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: true }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: document.body.classList.contains('dark-mode') ? '#3a3a55' : '#eee' },
                    ticks: { color: document.body.classList.contains('dark-mode') ? '#b0b0b0' : '#666' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: document.body.classList.contains('dark-mode') ? '#b0b0b0' : '#666' }
                }
            }
        }
    };
    
    var analyticsChart = new Chart(ctx, chartConfig);
    setTimeout(function() {
        analyticsChart.resize();
    }, 500);
    
    function updateChartColors(isDarkMode) {
        analyticsChart.data.datasets[0].backgroundColor = isDarkMode ? '#8b8dff' : '#333';
        analyticsChart.options.scales.y.grid.color = isDarkMode ? '#3a3a55' : '#eee';
        analyticsChart.options.scales.y.ticks.color = isDarkMode ? '#b0b0b0' : '#666';
        analyticsChart.options.scales.x.ticks.color = isDarkMode ? '#b0b0b0' : '#666';
        analyticsChart.update();
    }

    if (document.body.classList.contains('dark-mode')) {
        updateChartColors(true);
    }

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'class') {
                const isDarkMode = document.body.classList.contains('dark-mode');
                updateChartColors(isDarkMode);
            }
        });
    });

    observer.observe(document.body, { attributes: true });

    window.addEventListener('storage', function(e) {
        if (e.key === 'backpack_theme' || e.key === 'darkMode' || e.key === 'theme') {
            const isDarkMode = document.body.classList.contains('dark-mode');
            updateChartColors(isDarkMode);
        }
    });
    
    window.addEventListener('resize', function() {
        map.invalidateSize();
        analyticsChart.resize();
    });
});
</script>
@endsection