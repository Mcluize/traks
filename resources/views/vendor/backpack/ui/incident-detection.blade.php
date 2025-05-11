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
                        </div>
                    </div>
                
                    <!-- Search Bar with Date and Year Search -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="search-container">
                                <i class="la la-search search-icon"></i>
                                <input type="text" id="incident-search" class="form-control search-input" 
                                       placeholder="Search by Tourist ID" value="{{ $search ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="search-container">
                                <i class="la la-calendar search-icon"></i>
                                <input type="date" id="date-search" class="form-control search-input" 
                                       placeholder="Search by Date" value="{{ $search_date ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-4" id="year-filter" style="display: {{ $filter == 'all_time' ? 'block' : 'none' }};">
                            <div class="search-container">
                                <i class="la la-calendar-check search-icon"></i>
                                <select id="year-select" class="form-control search-input">
                                    <option value="">All Years</option>
                                    @foreach (array_reverse($years) as $year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
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

    function toggleYearFilter() {
        if (currentFilter === 'all_time') {
            $('#year-filter').show();
        } else {
            $('#year-filter').hide();
            $('#year-select').val('');
        }
    }

    function updateTable(showLoading = true) {
        if (isLoading) return;
        isLoading = true;

        if (showLoading) {
            $('#incident-table-container').html('<div class="text-center p-3"><i class="la la-spinner la-spin"></i> Loading incidents...</div>');
        }

        let year = currentFilter === 'all_time' ? $('#year-select').val() : '';

        $.ajax({
            url: "{{ route('admin.incidents.table-data') }}",
            type: "GET",
            data: {
                filter: currentFilter,
                search_id: currentSearch,
                search_date: searchDate,
                year: year,
                page: currentPage
            },
            success: function(response) {
                if (response.tableHtml && response.years) {
                    // Update table container
                    $('#incident-table-container').html(response.tableHtml);
                    attachPaginationHandlers();

                    // Update year dropdown if 'all_time' filter is active
                    if (currentFilter === 'all_time') {
                        let yearSelect = $('#year-select');
                        let selectedYear = yearSelect.val();
                        yearSelect.empty();
                        yearSelect.append('<option value="">All Years</option>');
                        response.years.reverse().forEach(function(year) {
                            yearSelect.append('<option value="' + year + '">' + year + '</option>');
                        });
                        // Restore the previously selected year if it still exists
                        if (selectedYear && response.years.includes(parseInt(selectedYear))) {
                            yearSelect.val(selectedYear);
                        } else {
                            yearSelect.val(''); // Default to "All Years" if the selected year is no longer valid
                        }
                    }
                } else {
                    console.error('Invalid response format');
                }
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

    $('#year-select').on('change', function() {
        if (currentFilter === 'all_time') {
            currentPage = 1;
            updateTable(true);
        }
    });

    $('.btn-filter').click(function() {
        $('.btn-filter').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        currentPage = 1;
        toggleYearFilter();
        updateTable(true);
    });

    toggleYearFilter();
    updateTable(true);

    setInterval(() => updateTable(false), 5000);
});
</script>
@endpush