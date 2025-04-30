<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getTouristArrivals($filter)
    {
        $supabase = new SupabaseService();
        $now = Carbon::now();
        $localToday = Carbon::today(); // Start of today in local timezone
        $localTomorrow = $localToday->copy()->addDay();
        $utcTodayStart = $localToday->copy()->utc();
        $utcTomorrowStart = $localTomorrow->copy()->utc();
        $filters = [];

        \Log::info('Filter: ' . $filter);

        switch ($filter) {
            case 'today':
                $start = $utcTodayStart->toIso8601String(); // e.g., 2025-04-30T00:00:00+00:00
                $end = $utcTomorrowStart->toIso8601String(); // e.g., 2025-05-01T00:00:00+00:00
                $filters = [
                    'and' => "(timestamp.gte.{$start},timestamp.lt.{$end})",
                ];
                break;

            case 'this_week':
                $localStartOfWeek = $now->copy()->startOfWeek(); // Monday of current week
                $localEndOfWeek = $localStartOfWeek->copy()->addWeek(); // Monday of next week
                $utcStartOfWeek = $localStartOfWeek->utc();
                $utcEndOfWeek = $localEndOfWeek->utc();
                $start = $utcStartOfWeek->toIso8601String();
                $end = $utcEndOfWeek->toIso8601String();
                $filters = [
                    'and' => "(timestamp.gte.{$start},timestamp.lt.{$end})",
                ];
                break;

            case 'this_month':
                $localStartOfMonth = $now->copy()->startOfMonth();
                $localEndOfMonth = $localStartOfMonth->copy()->addMonth();
                $utcStartOfMonth = $localStartOfMonth->utc();
                $utcEndOfMonth = $localEndOfMonth->utc();
                $start = $utcStartOfMonth->toIso8601String();
                $end = $utcEndOfMonth->toIso8601String();
                $filters = [
                    'and' => "(timestamp.gte.{$start},timestamp.lt.{$end})",
                ];
                break;

            case 'all_time':
                // No filters needed
                $filters = [];
                break;

            default:
                return response()->json(['error' => 'Invalid filter'], 400);
        }

        \Log::info('Filters: ' . json_encode($filters));

        $count = $supabase->fetchTable('checkins', $filters, true);
        if ($count === null) {
            return response()->json(['error' => 'Failed to fetch data'], 500);
        }
        return response()->json(['count' => $count]);
    }

    public function getIncidentReports($filter)
{
    $supabase = new SupabaseService();
    return $supabase->getIncidentReports($filter);
}
}