<div class="card navigation">
    <div class="card-header">Navigate Map</div>
    <div class="card-body">
        <div id="map" style="height: 100%;"></div> <!-- Placeholder for the map -->
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    // Initialize the map, setting the view to specific coordinates (e.g., the center of the island or a default location)
    var map = L.map('map').setView([51.505, -0.09], 13); // Replace with your desired coordinates

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Example of adding a marker (this should be replaced with dynamic data from your backend)
    L.marker([51.5, -0.09]).addTo(map)
        .bindPopup('<b>Tourist Location</b>') // Add a dynamic message here
        .openPopup();

    // You can add more markers dynamically based on your tourist data. 
    // Example:
    // L.marker([latitude, longitude]).addTo(map).bindPopup('<b>Location Name</b>');
</script>
