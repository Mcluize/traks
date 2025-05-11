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
            $filters['user_id'] = "ilike.%{$searchId}%";
        }

        if ($searchDate) {
            $dateStart = Carbon::parse($searchDate)->startOfDay()->toIso8601String();
            $dateEnd = Carbon::parse($searchDate)->endOfDay()->toIso8601String();
            $dateFilter = "(timestamp.gte.{$dateStart},timestamp.lt.{$dateEnd})";
            $filters['and'] = isset($filters['and']) ? $filters['and'] . ',' . $dateFilter : $dateFilter;
        }

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
            'selectedYear' => $selectedYear
        ]);
    }

    public function tableData(Request $request)
    {
        try {
            $filter = $request->input('filter', 'all_time');
            $searchId = $request->input('search_id');
            $searchDate = $request->input('search_date');
            $selectedYear = $request->input('year');

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
                    return response()->json(['error' => 'Invalid filter'], 400);
            }

            if ($searchId) {
                $searchId = (int)$searchId;
                $filters['user_id'] = "eq.{$searchId}";
            }

            if ($searchDate) {
                $dateStart = Carbon::parse($searchDate)->startOfDay()->toIso8601String();
                $dateEnd = Carbon::parse($searchDate)->endOfDay()->toIso8601String();
                $dateFilter = "(timestamp.gte.{$dateStart},timestamp.lt.{$dateEnd})";
                $filters['and'] = isset($filters['and']) ? $filters['and'] . ',' . $dateFilter : $dateFilter;
            }

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
            $tableHtml = view('vendor.backpack.ui.incident-table-partial', [
                'incidents' => $paginatedIncidents,
                'total' => $total,
                'currentPage' => $page,
                'lastPage' => ceil($total / $perPage)
            ])->render();

            // Return JSON response with table HTML and years
            return response()->json([
                'tableHtml' => $tableHtml,
                'years' => $years
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function fetchTable(Request $request)
    {
        $supabase = new SupabaseClient(env('SUPABASE_URL'), env('SUPABASE_KEY'));
        $table = $supabase->from('incidents');

        // Tourist ID search with partial matching
        $searchId = $request->input('search_id');
        if ($searchId) {
            $table = $table->ilike('tourist_id', "%{$searchId}%");
        }

        // Date search (specific day)
        $searchDate = $request->input('search_date');
        if ($searchDate) {
            $startOfDay = $searchDate . ' 00:00:00';
            $endOfDay = $searchDate . ' 23:59:59';
            $table = $table->gte('incident_date', $startOfDay)->lte('incident_date', $endOfDay);
        }

        // Time-based filters
        $filter = $request->input('filter', 'All Time');
        $today = date('Y-m-d');
        switch ($filter) {
            case 'Today':
                $table = $table->gte('incident_date', "$today 00:00:00")->lte('incident_date', "$today 23:59:59");
                break;
            case 'This Week':
                $startOfWeek = date('Y-m-d', strtotime('monday this week'));
                $table = $table->gte('incident_date', "$startOfWeek 00:00:00");
                break;
            case 'This Month':
                $startOfMonth = date('Y-m-01');
                $table = $table->gte('incident_date', "$startOfMonth 00:00:00");
                break;
            case 'All Time':
                // No additional filter
                break;
        }

        // Fetch paginated results
        $perPage = 10;
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;

        $data = $table->select('*')->limit($perPage)->offset($offset)->execute();
        $total = $table->count()->execute();

        return response()->json([
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page
        ]);
    }

    public function fetchMinMax($table, $column)
    {
        $min = $this->supabaseService->fetchTable($table, [], "min($column)")[0]['min'] ?? null;
        $max = $this->supabaseService->fetchTable($table, [], "max($column)")[0]['max'] ?? null;
        return ['min' => $min, 'max' => $max];
    }
}