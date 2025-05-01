<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

    public function getCheckinsBySpot($filter)
    {
        $supabase = new SupabaseService();
        $now = Carbon::now();
        $localToday = Carbon::today();
        $localTomorrow = $localToday->copy()->addDay();
        $utcTodayStart = $localToday->copy()->utc();
        $utcTomorrowStart = $localTomorrow->copy()->utc();
        $filters = [];

        switch ($filter) {
            case 'today':
                $start = $utcTodayStart->toIso8601String();
                $end = $utcTomorrowStart->toIso8601String();
                $filters = [
                    'select' => '*, tourist_spots!inner(name, latitude, longitude)',
                    'and' => "(timestamp.gte.{$start},timestamp.lt.{$end})"
                ];
                break;
            case 'this_week':
                $localStartOfWeek = $now->copy()->startOfWeek();
                $localEndOfWeek = $localStartOfWeek->copy()->addWeek();
                $utcStartOfWeek = $localStartOfWeek->utc();
                $utcEndOfWeek = $localEndOfWeek->utc();
                $start = $utcStartOfWeek->toIso8601String();
                $end = $utcEndOfWeek->toIso8601String();
                $filters = [
                    'select' => '*, tourist_spots!inner(name, latitude, longitude)',
                    'and' => "(timestamp.gte.{$start},timestamp.lt.{$end})"
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
                    'select' => '*, tourist_spots!inner(name, latitude, longitude)',
                    'and' => "(timestamp.gte.{$start},timestamp.lt.{$end})"
                ];
                break;
            case 'all_time':
                $filters = [
                    'select' => '*, tourist_spots!inner(name, latitude, longitude)'
                ];
                break;
            default:
                return response()->json(['error' => 'Invalid filter'], 400);
        }

        $data = $supabase->fetchTable('checkins', $filters, false);
        if ($data === null) {
            return response()->json(['error' => 'Failed to fetch data'], 500);
        }

        $spots = [];
        foreach ($data as $checkin) {
            $spotId = $checkin['spot_id'];
            if (!isset($spots[$spotId])) {
                $spots[$spotId] = [
                    'name' => $checkin['tourist_spots']['name'],
                    'latitude' => $checkin['tourist_spots']['latitude'],
                    'longitude' => $checkin['tourist_spots']['longitude'],
                    'count' => 0
                ];
            }
            $spots[$spotId]['count']++;
        }

        return response()->json(array_values($spots));
    }

    public function getIncidentReports($filter)
    {
        $supabase = new SupabaseService();
        return $supabase->getIncidentReports($filter);
    }

    public function getLatestTourists()
    {
        try {
            $supabase = new SupabaseService();
            $filters = [
                'user_type' => 'eq.user', // Filter for tourists (user_type = 'user')
                'order' => 'created_at.desc', // Sort by creation date, latest first
                'limit' => 3, // Limit to 3 latest tourists
            ];

            Log::info('Fetching latest tourists with filters: ' . json_encode($filters));
            $data = $supabase->fetchTable('users', $filters, false);

            if ($data === null || !is_array($data)) {
                Log::warning('Supabase returned null or invalid data for latest tourists');
                return response()->json(['tourists' => [], 'message' => 'No tourists found'], 200);
            }

            Log::info('Raw data from Supabase: ' . json_encode($data));

            $ids = array_map(function ($user) {
                // Check for both 'id' and 'user_id' to handle schema variations
                $touristId = isset($user['user_id']) ? $user['user_id'] : (isset($user['id']) ? $user['id'] : null);
                if ($touristId === null) {
                    Log::warning('User record missing ID field: ' . json_encode($user));
                }
                return $touristId;
            }, $data);

            // Filter out null values and ensure we have valid IDs
            $validIds = array_filter($ids, fn($id) => $id !== null);

            Log::info('Processed tourist IDs: ' . json_encode($validIds));

            return response()->json([
                'tourists' => $validIds,
                'message' => empty($validIds) ? 'No valid tourist IDs found' : 'Latest tourists retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in getLatestTourists: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Server error fetching tourists: ' . $e->getMessage()], 500);
        }
    }
}