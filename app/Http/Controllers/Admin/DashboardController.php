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
        $filters = [
            'select' => 'tourist_id'
        ];

        \Log::info('Filter: ' . $filter);

        switch ($filter) {
            case 'today':
                $start = $utcTodayStart->toIso8601String();
                $end = $utcTomorrowStart->toIso8601String();
                $filters['and'] = "(timestamp.gte.{$start},timestamp.lt.{$end})";
                break;

            case 'this_week':
                $localStartOfWeek = $now->copy()->startOfWeek();
                $localEndOfWeek = $localStartOfWeek->copy()->addWeek();
                $utcStartOfWeek = $localStartOfWeek->utc();
                $utcEndOfWeek = $localEndOfWeek->utc();
                $start = $utcStartOfWeek->toIso8601String();
                $end = $utcEndOfWeek->toIso8601String();
                $filters['and'] = "(timestamp.gte.{$start},timestamp.lt.{$end})";
                break;

            case 'this_month':
                $localStartOfMonth = $now->copy()->startOfMonth();
                $localEndOfMonth = $localStartOfMonth->copy()->addMonth();
                $utcStartOfMonth = $localStartOfMonth->utc();
                $utcEndOfMonth = $localEndOfMonth->utc();
                $start = $utcStartOfMonth->toIso8601String();
                $end = $utcEndOfMonth->toIso8601String();
                $filters['and'] = "(timestamp.gte.{$start},timestamp.lt.{$end})";
                break;

            case 'all_time':
                // No time filters
                break;

            default:
                return response()->json(['error' => 'Invalid filter'], 400);
        }

        \Log::info('Checkins Filters: ' . json_encode($filters));

        // Step 1: Fetch tourist_ids from checkins
        $checkinsData = $supabase->fetchTable('checkins', $filters, false);
        if ($checkinsData === null) {
            \Log::error('Failed to fetch checkins data');
            return response()->json(['error' => 'Failed to fetch checkins data'], 500);
        }

        // Extract tourist_ids
        $touristIds = array_column($checkinsData, 'tourist_id');
        $touristIds = array_filter($touristIds, fn($id) => !is_null($id)); // Remove null values
        if (empty($touristIds)) {
            return response()->json(['count' => 0]);
        }

        // Step 2: Fetch users with user_type = 'user' for the tourist_ids
        $userFilters = [
            'select' => 'user_id',
            'user_type' => 'eq.user',
            'user_id' => 'in.(' . implode(',', $touristIds) . ')'
        ];

        \Log::info('Users Filters: ' . json_encode($userFilters));

        $usersData = $supabase->fetchTable('users', $userFilters, false);
        if ($usersData === null) {
            \Log::error('Failed to fetch users data');
            return response()->json(['error' => 'Failed to fetch users data'], 500);
        }

        // Extract unique tourist_ids that are actual tourists
        $touristUserIds = array_column($usersData, 'user_id');
        $uniqueTouristIds = array_unique($touristUserIds);
        $count = count($uniqueTouristIds);

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

    public function getAccountCounts()
    {
        try {
            $supabase = new SupabaseService();
            $users = $supabase->fetchTable('users');
            
            if ($users === null || !is_array($users)) {
                Log::warning('Supabase returned null or invalid data for account counts');
                return response()->json(['touristCount' => 0, 'adminCount' => 0], 200);
            }

            $activeUsers = array_filter($users, fn($user) => $user['status'] !== 'locked');
            $touristCount = count(array_filter($activeUsers, fn($user) => $user['user_type'] === 'user'));
            $adminCount = count(array_filter($activeUsers, fn($user) => $user['user_type'] === 'admin'));

            Log::info('Account counts retrieved', ['touristCount' => $touristCount, 'adminCount' => $adminCount]);

            return response()->json([
                'touristCount' => $touristCount,
                'adminCount' => $adminCount
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in getAccountCounts: ' . $e->getMessage());
            return response()->json(['touristCount' => 0, 'adminCount' => 0, 'error' => 'Server error fetching account counts'], 500);
        }
    }
}