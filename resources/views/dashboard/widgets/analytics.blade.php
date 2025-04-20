<div class="card analytics">
    <div class="card-header">Analytics</div>
    <div class="card-body">
        <canvas id="analyticsChart"></canvas> <!-- Placeholder for the chart -->
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('analyticsChart').getContext('2d');
    var analyticsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'], // X-axis labels (Weeks)
            datasets: [{
                label: 'Tourist Visits', // Data label
                data: [1532, 1200, 1600, 1450], // Data for each week (replace with dynamic data)
                backgroundColor: 'rgba(54, 162, 235, 0.2)', // Bar color
                borderColor: 'rgba(54, 162, 235, 1)', // Border color
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true // Ensure the chart starts at 0 on the y-axis
                }
            }
        });
</script>
