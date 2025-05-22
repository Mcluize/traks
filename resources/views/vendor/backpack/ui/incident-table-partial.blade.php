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
                    <td>{{ $incident['date'] }}</td>
                    <td>{{ $incident['time'] }}</td>
                    <td>{{ $incident['latitude'] }}</td>
                    <td>{{ $incident['longitude'] }}</td>
                    <td class="status status-{{ $incident['status_class'] }}">{{ $incident['status'] }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center error-message">{{ $message ?? 'No incidents found' }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Result Summary -->
<div class="row mt-2 mb-2">
    <div class="col-md-12">
        <div class="dataTables_info">
            @if($total > 0)
                Showing {{ count($incidents) }} of {{ $total }} entries
            @endif
        </div>
    </div>
</div>

<!-- Pagination -->
@if($total > 0)
<div class="row">
    <div class="col-md-12">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-end">
                @if ($currentPage > 1)
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)" data-page="{{ $currentPage - 1 }}">Previous</a>
                    </li>
                @endif
                @for ($i = 1; $i <= $lastPage; $i++)
                    <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                        <a class="page-link" href="javascript:void(0)" data-page="{{ $i }}">{{ $i }}</a>
                    </li>
                @endfor
                @if ($currentPage < $lastPage)
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)" data-page="{{ $currentPage + 1 }}">Next</a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</div>
@endif