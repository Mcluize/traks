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
        $filter = $request->input('filter', 'today');
        $searchId = $request->input('search_id');
        $searchDate = $request->input('search_date');
        $selectedYear = $request->input('year');

        // Fetch min and max timestamps for year range, assuming local time
        $minMax = $this->supabaseService->fetchMinMax('emergency_reports', 'timestamp');
        if ($minMax && isset($minMax['min']) && isset($minMax['max'])) {
            $minYear = Carbon::parse($minMax['min'], 'Asia/Manila')->year;
            $maxYear = Carbon::parse($minMax['max'], 'Asia/Manila')->year;
            $years = range($minYear, $maxYear);
        } else {
            $years = [Carbon::now('Asia/Manila')->year];
        }

        $filters = $this->buildFilters($filter, $searchId, $searchDate, $selectedYear);
        $incidents = $this->supabaseService->fetchTable('emergency_reports', $filters);

        if ($incidents === null) {
            $incidents = [];
        }

        $incidents = $this->formatIncidents($incidents);

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
            $filter = $request->input('filter', 'today');
            $searchId = $request->input('search_id');
            $searchDate = $request->input('search_date');
            $selectedYear = $request->input('year');

            $filters = $this->buildFilters($filter, $searchId, $searchDate, $selectedYear);
            $incidents = $this->supabaseService->fetchTable('emergency_reports', $filters);

            if ($incidents === null) {
                $incidents = [];
            }

            $incidents = $this->formatIncidents($incidents);

            usort($incidents, function($a, $b) {
                return strtotime($b['timestamp'] ?? 0) - strtotime($a['timestamp'] ?? 0);
            });

            $total = count($incidents);
            $perPage = 5;
            $page = $request->input('page', 1);
            $offset = ($page - 1) * $perPage;
            $paginatedIncidents = array_slice($incidents, $offset, $perPage);

            // Fetch min and max timestamps for year range, assuming local time
            $minMax = $this->supabaseService->fetchMinMax('emergency_reports', 'timestamp');
            if ($minMax && isset($minMax['min']) && isset($minMax['max'])) {
                $minYear = Carbon::parse($minMax['min'], 'Asia/Manila')->year;
                $maxYear = Carbon::parse($minMax['max'], 'Asia/Manila')->year;
                $years = range($minYear, $maxYear);
            } else {
                $years = [Carbon::now('Asia/Manila')->year];
            }

            $message = $total === 0 ? ($searchId ? 'No incidents found matching ID: '.$searchId : 'No incidents found') : '';
            $tableHtml = view('vendor.backpack.ui.incident-table-partial', [
                'incidents' => $paginatedIncidents,
                'total' => $total,
                'currentPage' => $page,
                'lastPage' => ceil($total / $perPage),
                'message' => $message
            ])->render();

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

    private function formatIncidents(array $incidents): array
{
    return array_map(function($incident) {
        // Parse timestamp as local Asia/Manila time, no conversion
        $timestamp = Carbon::parse($incident['timestamp'], 'Asia/Manila');
        $incident['date'] = $timestamp->toDateString(); // e.g., "2025-05-11"
        $incident['time'] = $timestamp->format('H:i:s'); // e.g., "17:58:08"
        $incident['status_class'] = str_replace(' ', '-', strtolower($incident['status']));
        return $incident;
    }, $incidents);
}

    private function buildFilters($filter, $searchId, $searchDate, $selectedYear)
    {
        $now = Carbon::now('Asia/Manila');
        $filters = [];

        switch ($filter) {
            case 'today':
                $start = Carbon::today('Asia/Manila')->startOfDay()->format('Y-m-d H:i:s');
                $end = Carbon::today('Asia/Manila')->endOfDay()->format('Y-m-d H:i:s');
                $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                break;
            case 'this_week':
                $startOfWeek = $now->startOfWeek(Carbon::MONDAY)->copy();
                $endOfWeek = $now->endOfWeek(Carbon::SUNDAY)->copy();
                $start = $startOfWeek->startOfDay()->format('Y-m-d H:i:s');
                $end = $endOfWeek->endOfDay()->format('Y-m-d H:i:s');
                $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                break;
            case 'this_month':
                $start = $now->copy()->startOfMonth()->format('Y-m-d H:i:s');
                $end = $now->copy()->endOfMonth()->endOfDay()->format('Y-m-d H:i:s');
                $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                break;
            case 'all_time':
                if ($selectedYear) {
                    $start = Carbon::create($selectedYear, 1, 1, 0, 0, 0, 'Asia/Manila')->startOfDay()->format('Y-m-d H:i:s');
                    $end = Carbon::create($selectedYear, 12, 31, 23, 59, 59, 'Asia/Manila')->endOfDay()->format('Y-m-d H:i:s');
                    $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                }
                break;
            default:
                $start = Carbon::today('Asia/Manila')->startOfDay()->format('Y-m-d H:i:s');
                $end = Carbon::today('Asia/Manila')->endOfDay()->format('Y-m-d H:i:s');
                $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
        }

        if ($searchId) {
            $searchId = trim($searchId);
            if (is_numeric($searchId)) {
                $filters['user_id'] = "eq.{$searchId}";
            } else {
                $filters['user_id'] = "ilike.%{$searchId}%";
            }
        }

        if ($searchDate) {
    $dateStart = Carbon::parse($searchDate, 'Asia/Manila')->startOfDay()->format('Y-m-d H:i:s');
    $dateEnd = Carbon::parse($searchDate, 'Asia/Manila')->endOfDay()->format('Y-m-d H:i:s');
    $dateFilter = "(timestamp.gte.{$dateStart},timestamp.lte.{$dateEnd})";
    $filters['and'] = $dateFilter;
}


        return $filters;
    }
}