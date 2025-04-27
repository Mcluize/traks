@extends(backpack_view('blank'))

@section('header')
<div class="container-fluid">
    <div class="justify-content-between align-items-left">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="#">Pages</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Incident Report</li>
                </ol>
            </nav>
        </div>
        <div>
            <h2 class="header-container">
                <span>Incident Overview</span>
            </h2>
        </div>
    </div>
</div>
@endsection

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<link href="{{ asset('css/incident_detection.css') }}" rel="stylesheet">

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Filter Controls -->
                    <div class="filter-section mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="btn-group filter-buttons" role="group">
                                    <button type="button" class="btn btn-filter {{ ($filter ?? 'all') == 'all' ? 'active' : '' }}" data-filter="all">All Time</button>
                                    <button type="button" class="btn btn-filter {{ ($filter ?? '') == 'monthly' ? 'active' : '' }}" data-filter="monthly">Monthly</button>
                                    <button type="button" class="btn btn-filter {{ ($filter ?? '') == 'weekly' ? 'active' : '' }}" data-filter="weekly">Weekly</button>
                                    <button type="button" class="btn btn-filter {{ ($filter ?? '') == 'daily' ? 'active' : '' }}" data-filter="daily">Daily</button>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-export">
                                    <i class="la la-download"></i> Export Data
                                </button>
                            </div>
                        </div>
                    </div>
                
                    <!-- Search Bar -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="search-container">
                                <i class="la la-search search-icon"></i>
                                <input type="text" id="incident-search" class="form-control search-input" 
                                       placeholder="Search by Tourist ID" value="{{ $search ?? '' }}">
                            </div>
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div id="incident-table-container">
                        @include('vendor.backpack.ui.incident-table-partial', [
                            'incidents' => $incidents,
                            'total' => $total ?? count($incidents),
                            'currentPage' => $currentPage ?? 1,
                            'lastPage' => $lastPage ?? 1
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after_scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const ITEMS_PER_PAGE = 5;
    let currentFilter = "{{ $filter ?? 'all' }}";
    let currentSearch = "{{ $search ?? '' }}";
    let currentPage = {{ $currentPage ?? 1 }};
    let allIncidents = [];

    // Fetch all incidents initially
    function loadAllIncidents(filter) {
        $.ajax({
            url: "{{ route('admin.incidents.table-data') }}",
            type: "GET",
            data: { filter: filter },
            success: function(response) {
                // Parse the table rows into a JavaScript array
                allIncidents = $(response).find('tbody tr').map(function() {
                    const cells = $(this).find('td');
                    return {
                        user_id: cells.eq(0).text(),
                        date: cells.eq(1).text(),
                        time: cells.eq(2).text(),
                        latitude: cells.eq(3).text(),
                        longitude: cells.eq(4).text(),
                        status: cells.eq(5).text(),
                        timestamp: new Date(cells.eq(1).text() + ' ' + cells.eq(2).text()).getTime()
                    };
                }).get();
                updateTable();
            },
            error: function(xhr) {
                console.error('Error:', xhr.status, xhr.responseText);
                $('#incident-table-container').html('<div class="alert alert-danger">Failed to load data. Please try again.</div>');
            }
        });
    }

    function updateTable() {
    let filteredIncidents = applyFilters(allIncidents);
    const totalItems = filteredIncidents.length;
    const totalPages = Math.max(1, Math.ceil(totalItems / ITEMS_PER_PAGE));
    currentPage = Math.min(currentPage, totalPages) || 1;

    const start = (currentPage - 1) * ITEMS_PER_PAGE;
    const end = Math.min(start + ITEMS_PER_PAGE, totalItems);
    const paginatedIncidents = filteredIncidents.slice(start, end);

    const tbody = $('#incidents-table tbody');
    tbody.empty();
    if (paginatedIncidents.length > 0) {
        paginatedIncidents.forEach(incident => {
            tbody.append(`
                <tr class="incident-row">
                    <td>${incident.user_id}</td>
                    <td>${incident.date}</td>
                    <td>${incident.time}</td>
                    <td>${incident.latitude}</td>
                    <td>${incident.longitude}</td>
                    <td>${incident.status}</td>
                </tr>
            `);
        });
    } else {
        // Apply .error-message class to the dynamically inserted message
        tbody.append('<tr><td colspan="6" class="text-center error-message">No incidents found.</td></tr>');
    }

    // Update no data message (optional, if used elsewhere)
    $('#noIncidentResultMessage').toggle(totalItems === 0 && currentSearch !== '');

    updatePagination(totalItems, totalPages);
    $('.dataTables_info').text(`Showing ${end - start} of ${totalItems} entries`);
}

    // Apply filters client-side
    function applyFilters(incidents) {
        let filtered = [...incidents];

        // Apply search filter
        if (currentSearch) {
            filtered = filtered.filter(incident => 
                incident.user_id.toLowerCase().includes(currentSearch.toLowerCase())
            );
        }

        // Apply time filter
        if (currentFilter !== 'all') {
            const now = Date.now();
            filtered = filtered.filter(incident => {
                const diff = (now - incident.timestamp) / 1000; // Difference in seconds
                switch (currentFilter) {
                    case 'daily': return diff <= 86400; // 24 hours
                    case 'weekly': return diff <= 604800; // 7 days
                    case 'monthly': return diff <= 2592000; // 30 days
                    default: return true;
                }
            });
        }

        return filtered;
    }

    // Update pagination controls
    function updatePagination(totalItems, totalPages) {
        const pagination = $('.pagination');
        pagination.empty();

        // Previous button
        pagination.append(`
            <li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" data-page="${currentPage - 1}">Previous</a>
            </li>
        `);

        // Page numbers (simplified to show all for now)
        for (let i = 1; i <= totalPages; i++) {
            pagination.append(`
                <li class="page-item ${currentPage === i ? 'active' : ''}">
                    <a class="page-link" href="javascript:void(0)" data-page="${i}">${i}</a>
                </li>
            `);
        }

        // Next button
        pagination.append(`
            <li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" data-page="${currentPage + 1}">Next</a>
            </li>
        `);

        // Bind pagination events
        $('.page-link').off('click').on('click', function(e) {
            e.preventDefault();
            if (!$(this).parent().hasClass('disabled')) {
                currentPage = parseInt($(this).data('page'));
                updateTable();
                $('html, body').animate({
                    scrollTop: $('#incident-table-container').offset().top - 100
                }, 300);
            }
        });
    }

    // Search input handler
    $('#incident-search').on('keyup', function() {
        currentSearch = $(this).val();
        currentPage = 1;
        updateTable();
    });

    // Filter button handler
    $('.btn-filter').click(function() {
        $('.btn-filter').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        currentPage = 1;
        loadAllIncidents(currentFilter); // Reload data for new filter
    });

    // Export button placeholder
    $('.btn-export').click(function() {
        alert('Export functionality will be implemented later');
    });

    // Initial load
    loadAllIncidents(currentFilter);
});
</script>
@endpush