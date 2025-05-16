<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IncidentController extends Controller
{
    protected $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all_time');
        $searchId = $request->input('search_id');
        $searchDate = $request->input('search_date');
        $selectedYear = $request->input('year');

        // Fetch min and max timestamps to determine year range
        $minMax = $this->supabaseService->fetchMinMax('emergency_reports', 'timestamp');
        if ($minMax && isset($minMax['min']) && isset($minMax['max'])) {
            $minYear = Carbon::parse($minMax['min'])->year;
            $maxYear = Carbon::parse($minMax['max'])->year;
            $years = range($minYear, $maxYear);
        } else {
            $years = [Carbon::now()->year]; // Default to current year if no data
        }

        $filters = $this->buildFilters($filter, $searchId, $searchDate, $selectedYear);
        $incidents = $this->supabaseService->fetchTable('emergency_reports', $filters);

        if ($incidents === null) {
            $incidents = [];
        }

        usort($incidents, function($a, $b) {
            return strtotime($b['timestamp'] ?? 0) - strtotime($a['timestamp'] ?? 0);
        });

        $total = count($incidents);
        $perPage = 5;
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;
        $paginatedIncidents = array_slice($incidents, $offset, $perPage);

        return view('vendor.backpack.ui.incident-detection', [
            'incidents' => $paginatedIncidents,
            'total' => $total,
            'currentPage' => $page,
            'lastPage' => ceil($total / $perPage),
            'filter' => $filter,
            'search' => $searchId,
            'search_date' => $searchDate,
            'years' => $years,
            'selectedYear' => $selectedYear,
            'isLoading' => false
        ]);
    }

    public function tableData(Request $request)
    {
        try {
            $filter = $request->input('filter', 'all_time');
            $searchId = $request->input('search_id');
            $searchDate = $request->input('search_date');
            $selectedYear = $request->input('year');

            $filters = $this->buildFilters($filter, $searchId, $searchDate, $selectedYear);
            $incidents = $this->supabaseService->fetchTable('emergency_reports', $filters);

            if ($incidents === null) {
                $incidents = [];
            }

            usort($incidents, function($a, $b) {
                return strtotime($b['timestamp'] ?? 0) - strtotime($a['timestamp'] ?? 0);
            });

            $total = count($incidents);
            $perPage = 5;
            $page = $request->input('page', 1);
            $offset = ($page - 1) * $perPage;
            $paginatedIncidents = array_slice($incidents, $offset, $perPage);

            // Fetch min and max timestamps to determine year range
            $minMax = $this->supabaseService->fetchMinMax('emergency_reports', 'timestamp');
            if ($minMax && isset($minMax['min']) && isset($minMax['max'])) {
                $minYear = Carbon::parse($minMax['min'])->year;
                $maxYear = Carbon::parse($minMax['max'])->year;
                $years = range($minYear, $maxYear);
            } else {
                $years = [Carbon::now()->year]; // Default to current year if no data
            }

            // Render the table partial
            $message = $total === 0 ? ($searchId ? 'No incidents found matching ID: '.$searchId : 'No incidents found') : '';
            $tableHtml = view('vendor.backpack.ui.incident-table-partial', [
                'incidents' => $paginatedIncidents,
                'total' => $total,
                'currentPage' => $page,
                'lastPage' => ceil($total / $perPage),
                'message' => $message
            ])->render();

            // Return JSON response with table HTML and years
            return response()->json([
                'tableHtml' => $tableHtml,
                'years' => $years,
                'total' => $total
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching incident data: ' . $e->getMessage(), [
                'filter' => $request->input('filter'),
                'search_id' => $request->input('search_id'),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Build filters based on the provided parameters
     */
    private function buildFilters($filter, $searchId, $searchDate, $selectedYear)
    {
        $now = Carbon::now();
        $filters = [];

        switch ($filter) {
            case 'today':
                $localStart = Carbon::today();
                $localEnd = $localStart->copy()->addDay();
                $start = $localStart->toIso8601String();
                $end = $localEnd->toIso8601String();
                $filters['and'] = "(timestamp.gte.{$start},timestamp.lt.{$end})";
                break;
            case 'this_week':
                $localStart = $now->copy()->startOfWeek();
                $localEnd = $localStart->copy()->addWeek();
                $start = $localStart->toIso8601String();
                $end = $localEnd->toIso8601String();
                $filters['and'] = "(timestamp.gte.{$start},timestamp.lt.{$end})";
                break;
            case 'this_month':
                $localStart = $now->copy()->startOfMonth();
                $localEnd = $localStart->copy()->addMonth();
                $start = $localStart->toIso8601String();
                $end = $localEnd->toIso8601String();
                $filters['and'] = "(timestamp.gte.{$start},timestamp.lt.{$end})";
                break;
            case 'all_time':
                if ($selectedYear) {
                    $start = Carbon::create($selectedYear, 1, 1)->startOfDay()->toIso8601String();
                    $end = Carbon::create($selectedYear + 1, 1, 1)->startOfDay()->toIso8601String();
                    $filters['and'] = "(timestamp.gte.{$start},timestamp.lt.{$end})";
                }
                break;
            default:
                $filter = 'all_time';
        }

        if ($searchId) {
            // Handle both numeric and string IDs
            $searchId = trim($searchId);
            if (is_numeric($searchId)) {
                $filters['user_id'] = "eq.{$searchId}";
            } else {
                $filters['user_id'] = "ilike.%{$searchId}%";
            }
        }

        if ($searchDate) {
            $dateStart = Carbon::parse($searchDate)->startOfDay()->toIso8601String();
            $dateEnd = Carbon::parse($searchDate)->endOfDay()->toIso8601String();
            $dateFilter = "(timestamp.gte.{$dateStart},timestamp.lt.{$dateEnd})";
            $filters['and'] = isset($filters['and']) ? $filters['and'] . ',' . $dateFilter : $dateFilter;
        }

        return $filters;
    }

    public function fetchMinMax($table, $column)
    {
        $min = $this->supabaseService->fetchTable($table, [], "min($column)")[0]['min'] ?? null;
        $max = $this->supabaseService->fetchTable($table, [], "max($column)")[0]['max'] ?? null;
        return ['min' => $min, 'max' => $max];
    }
}