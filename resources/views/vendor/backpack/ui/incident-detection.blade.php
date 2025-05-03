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
                                    <button type="button" class="btn btn-filter {{ ($filter ?? 'all_time') == 'all_time' ? 'active' : '' }}" data-filter="all_time">All Time</button>
                                    <button type="button" class="btn btn-filter {{ ($filter ?? '') == 'this_month' ? 'active' : '' }}" data-filter="this_month">This Month</button>
                                    <button type="button" class="btn btn-filter {{ ($filter ?? '') == 'this_week' ? 'active' : '' }}" data-filter="this_week">This Week</button>
                                    <button type="button" class="btn btn-filter {{ ($filter ?? '') == 'today' ? 'active' : '' }}" data-filter="today">Today</button>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-export">
                                    <i class="la la-download"></i> Export Data
                                </button>
                            </div>
                        </div>
                    </div>
                
                    <!-- Search Bar with Date Search -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="search-container">
                                <i class="la la-search search-icon"></i>
                                <input type="text" id="incident-search" class="form-control search-input" 
                                       placeholder="Search by Tourist ID" value="{{ $search ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="search-container">
                                <i class="la la-calendar search-icon"></i>
                                <input type="date" id="date-search" class="form-control search-input" 
                                       placeholder="Search by Date" value="{{ $search_date ?? '' }}">
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
    let currentFilter = "{{ $filter ?? 'all_time' }}";
    let currentSearch = "{{ $search ?? '' }}";
    let searchDate = "{{ $search_date ?? '' }}";
    let currentPage = {{ $currentPage ?? 1 }};
    let isLoading = false;

    function updateTable(showLoading = true) {
        if (isLoading) return;
        isLoading = true;

        if (showLoading) {
            $('#incident-table-container').html('<div class="text-center p-3"><i class="la la-spinner la-spin"></i> Loading incidents...</div>');
        }

        $.ajax({
            url: "{{ route('admin.incidents.table-data') }}",
            type: "GET",
            data: {
                filter: currentFilter,
                search_id: currentSearch,
                search_date: searchDate,
                page: currentPage
            },
            success: function(response) {
                $('#incident-table-container').html(response);
                attachPaginationHandlers();
            },
            error: function(xhr) {
                console.error('Error:', xhr.status, xhr.responseText);
                if (showLoading) {
                    $('#incident-table-container').html('<div class="alert alert-danger">Failed to load data. Please try again.</div>');
                }
            },
            complete: function() {
                isLoading = false;
            }
        });
    }

    function attachPaginationHandlers() {
        $('.page-link').off('click').on('click', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page) {
                currentPage = page;
                updateTable(true);
                $('html, body').animate({
                    scrollTop: $('#incident-table-container').offset().top - 100
                }, 300);
            }
        });
    }

    $('#incident-search').on('keyup', function() {
        currentSearch = $(this).val();
        currentPage = 1;
        updateTable(true);
    });

    $('#date-search').on('change', function() {
        searchDate = $(this).val();
        currentPage = 1;
        updateTable(true);
    });

    $('.btn-filter').click(function() {
        $('.btn-filter').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        currentPage = 1;
        updateTable(true);
    });

    $('.btn-export').click(function() {
        alert('Export functionality will be implemented later');
    });

    // Initial load with loading indicator
    updateTable(true);

    // Periodic update every 5 seconds without loading indicator
    setInterval(() => updateTable(false), 5000);
});
</script>
@endpush