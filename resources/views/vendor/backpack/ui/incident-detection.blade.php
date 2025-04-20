<!-- incident-detection.blade.php -->
@extends(backpack_view('blank'))

@section('header')
    <div class="container-fluid">
        <div class="justify-content-between align-items-left">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="#">Pages</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Incident Detection</li>
                    </ol>
                </nav>
            </div>
            <div>
                <h2 class="header-container">
                    <span>Incident Overview</span> 
                </h2>
            </div>
                <div class="incident-actions">
                    <button class="send-notification">Send Notification</button>
                    <button class="export">Export</button>
                </div>
        </div>
    </div>
@endsection

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<link href="{{ asset('css/incident_detection.css') }}" rel="stylesheet">
    
    <div class="incident-content">
        <div class="incident-main">
            <div class="incident-cards">
                <!-- First Incident Card -->
                <div class="incident-card">
                    <div class="card-content">
                        <div class="card-row">
                            <span class="card-label">Tourist No.</span>
                        </div>
                        <div class="card-row">
                            <span class="card-label">Incident Type:</span>
                        </div>
                        <div class="card-row">
                            <span class="card-label">Time</span>
                        </div>
                        <div class="card-row">
                            <span class="card-label">Severity:</span>
                        </div>
                    </div>
                    <div class="card-actions">
                        <button class="action-button">Acknowledge</button>
                        <button class="action-button">Resolve</button>
                        <button class="action-button">Escalate</button>
                    </div>
                </div>
                
                <!-- Second Incident Card -->
                <div class="incident-card">
                    <div class="card-content">
                        <div class="card-row">
                            <span class="card-label">Tourist No.</span>
                        </div>
                        <div class="card-row">
                            <span class="card-label">Incident Type:</span>
                        </div>
                        <div class="card-row">
                            <span class="card-label">Time</span>
                        </div>
                        <div class="card-row">
                            <span class="card-label">Severity:</span>
                        </div>
                    </div>
                    <div class="card-actions">
                        <button class="action-button">Acknowledge</button>
                        <button class="action-button">Resolve</button>
                        <button class="action-button">Escalate</button>
                    </div>
                </div>
                
                <!-- Third Incident Card -->
                <div class="incident-card">
                    <div class="card-content">
                        <div class="card-row">
                            <span class="card-label">Tourist No.</span>
                        </div>
                        <div class="card-row">
                            <span class="card-label">Incident Type:</span>
                        </div>
                        <div class="card-row">
                            <span class="card-label">Time</span>
                        </div>
                        <div class="card-row">
                            <span class="card-label">Severity:</span>
                        </div>
                    </div>
                    <div class="card-actions">
                        <button class="action-button">Acknowledge</button>
                        <button class="action-button">Resolve</button>
                        <button class="action-button">Escalate</button>
                    </div>
                </div>
            </div>
            
            <div class="incident-filters">
                <button class="filter-button">Health Concerns</button>
                <button class="filter-button">Lost Tourist</button>
                <button class="filter-button">Environmental Hazard</button>
                <button class="filter-button">Security Threat</button>
            </div>
            
            <div class="incident-table-card">
                <table class="incident-table">
                    <thead>
                        <tr>
                            <th>user id</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Incident Type</th>
                            <th>Description</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Table data will be populated dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="incident-history-container">
            <div class="incident-history-card">
                <h2 class="history-title">Incident History</h2>
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>user id</th>
                            <th>Date</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- History data will be populated dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection