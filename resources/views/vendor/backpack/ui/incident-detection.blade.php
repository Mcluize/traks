<!-- tracking.blade.php -->
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
                                    <button type="button" class="btn btn-filter active" data-filter="all">All Time</button>
                                    <button type="button" class="btn btn-filter" data-filter="monthly">Monthly</button>
                                    <button type="button" class="btn btn-filter" data-filter="weekly">Weekly</button>
                                    <button type="button" class="btn btn-filter" data-filter="daily">Daily</button>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <button class="btn btn-export">
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
                                <input type="text" id="incident-search" class="form-control search-input" placeholder="Search by Tourist ID">
                            </div>
                        </div>
                    </div>

                    <!-- Incident Table -->
                    <div class="table-responsive">
                        <table class="table table-striped incident-table" id="incidents-table">
                            <thead>
                                <tr>
                                    <th>Tourist ID</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample data - replace with actual data -->
                                <tr>
                                    <td>T-00123</td>
                                    <td>2025-04-24</td>
                                    <td>14:30</td>
                                </tr>
                                <tr>
                                    <td>T-00456</td>
                                    <td>2025-04-23</td>
                                    <td>10:15</td>
                                </tr>
                                <tr>
                                    <td>T-00789</td>
                                    <td>2025-04-22</td>
                                    <td>16:45</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="dataTables_info">
                                Showing 1 to 3 of 25 entries
                            </div>
                        </div>
                        <div class="col-md-6">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-end">
                                    <li class="page-item disabled">
                                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                                    </li>
                                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Filter button functionality
        $('.btn-filter').click(function() {
            $('.btn-filter').removeClass('active');
            $(this).addClass('active');
            
            // Add filter logic here
            let filterType = $(this).data('filter');
            console.log('Filter applied:', filterType);
            // Implement your filtering logic
        });
        
        // Export button functionality
        $('.btn-export').click(function() {
            // Add export functionality here
            console.log('Export requested');
            // Implement your export logic
        });
        
        // Search functionality
        $('#incident-search').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $("#incidents-table tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
@endsection