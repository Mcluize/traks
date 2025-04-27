<div class="table-responsive">
    <table id="incidents-table" class="table incident-table">
        <thead>
            <tr>
                <th>TOURIST ID</th>
                <th>DATE</th>
                <th>TIME</th>
                <th>LATITUDE</th>
                <th>LONGITUDE</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($incidents as $incident)
                <tr class="incident-row">
                    <td>{{ $incident['user_id'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($incident['timestamp'])->toDateString() }}</td>
                    <td>{{ \Carbon\Carbon::parse($incident['timestamp'])->format('H:i') }}</td>
                    <td>{{ $incident['latitude'] }}</td>
                    <td>{{ $incident['longitude'] }}</td>
                    <td>{{ $incident['status'] }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center error-message">{{ $message ?? 'No incidents found' }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="row mt-3">
    <div class="col-md-6">
        <div class="dataTables_info">
            Showing {{ count($incidents) }} of {{ $total }} entries
        </div>
    </div>
    <div class="col-md-6">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-end">
            </ul>
        </nav>
    </div>
</div>