@extends(backpack_view('blank'))

@section('header')
<div class="container-fluid">
    <div class="justify-content-between align-items-left">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="#">Pages</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tracking</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
@endsection

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/tracking.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<div class="tracking-container">
    <div class="map-section">
        <div class="map-container">
            <div id="map"></div>
            <div class="active-tourist-overlay">
                <div class="tourist-count-header">ACTIVE TOURIST</div>
                <div class="tourist-count">71</div>
            </div>
            <div class="description-overlay">
                <div class="description-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed feugiat accumsan dui, at dignissim neque molestie sit amet. Integer nec accumsan orci. Maecenas ultricies magna vel felis tincidunt, ac cursus enim venenatis.</div>
            </div>
            
            <!-- Tourist info section as overlay -->
            <div class="tourist-info-section">
                <div class="search-container">
                    <div class="search-header">Search ID</div>
                    <input type="text" class="search-input" placeholder="Enter tourist ID">
                </div>
                
                <div class="tourist-cards">
                    <div class="tourist-card">
                        <div class="tourist-detail">
                            <div class="detail-label">USER ID</div>
                            <div class="detail-value">T-1001</div>
                        </div>
                        <div class="tourist-detail">
                            <div class="detail-label">CHECK-IN</div>
                            <div class="detail-value">08:30 AM</div>
                        </div>
                        <div class="tourist-detail">
                            <div class="detail-label">TIME</div>
                            <div class="detail-value">2h 15m</div>
                        </div>
                        <div class="tourist-detail">
                            <div class="detail-label">STATUS</div>
                            <div class="detail-value">Active</div>
                        </div>
                    </div>
                    
                    <div class="tourist-card">
                        <div class="tourist-detail">
                            <div class="detail-label">USER ID</div>
                            <div class="detail-value">T-1002</div>
                        </div>
                        <div class="tourist-detail">
                            <div class="detail-label">CHECK-IN</div>
                            <div class="detail-value">09:15 AM</div>
                        </div>
                        <div class="tourist-detail">
                            <div class="detail-label">TIME</div>
                            <div class="detail-value">1h 30m</div>
                        </div>
                        <div class="tourist-detail">
                            <div class="detail-label">STATUS</div>
                            <div class="detail-value">Active</div>
                        </div>
                    </div>
                    
                    <div class="tourist-card">
                        <div class="tourist-detail">
                            <div class="detail-label">USER ID</div>
                            <div class="detail-value">T-1003</div>
                        </div>
                        <div class="tourist-detail">
                            <div class="detail-label">CHECK-IN</div>
                            <div class="detail-value">10:00 AM</div>
                        </div>
                        <div class="tourist-detail">
                            <div class="detail-label">TIME</div>
                            <div class="detail-value">0h 45m</div>
                        </div>
                        <div class="tourist-detail">
                            <div class="detail-label">STATUS</div>
                            <div class="detail-value">Active</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    var map = L.map('map').setView([7.0767, 125.8259], 13);  

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Add the red circle markers as shown in the hi-fi design
    var circle1 = L.circle([30.621, -96.340], {
        color: 'rgba(255, 0, 0, 0)',
        fillColor: '#ff6666',
        fillOpacity: 0.5,
        radius: 500
    }).addTo(map);
    
    var circle2 = L.circle([30.633, -96.315], {
        color: 'rgba(255, 0, 0, 0)',
        fillColor: '#ff6666',
        fillOpacity: 0.5,
        radius: 500
    }).addTo(map);
    
    var circle3 = L.circle([30.605, -96.297], {
        color: 'rgba(255, 0, 0, 0)',
        fillColor: '#ff9966',
        fillOpacity: 0.5,
        radius: 500
    }).addTo(map);
    
    // Fix map container issues by refreshing the map
    setTimeout(function() {
        map.invalidateSize();
    }, 500);
    
    // Handle window resize for responsiveness
    window.addEventListener('resize', function() {
        map.invalidateSize();
    });
});
</script>
@endsection