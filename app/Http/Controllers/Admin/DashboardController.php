<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    public function getTouristArrivals($filter)
    {
        $now = date('Y-m-d H:i:s');
        $todayDate = date('Y-m-d');
        $filters = ['select' => 'tourist_id'];

        \Log::info('Filter: ' . $filter);
        \Log::info('Current DateTime (Raw): ' . $now);
        \Log::info('Today Date (Raw): ' . $todayDate);

        if (strpos($filter, 'custom_year:') === 0) {
            $year = substr($filter, strlen('custom_year:'));
            if (is_numeric($year) && strlen($year) == 4) {
                $start = "$year-01-01 00:00:00";
                $end = "$year-12-31 23:59:59";
                $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                \Log::info('Custom Year Range - Start: ' . $start . ', End: ' . $end);
            } else {
                return response()->json(['error' => 'Invalid year'], 400);
            }
        } else {
            switch ($filter) {
                case 'today':
                    $start = "$todayDate 00:00:00";
                    $end = "$todayDate 23:59:59";
                    $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                    \Log::info('Today Range - Start: ' . $start . ', End: ' . $end);
                    break;
                case 'this_week':
                    $startOfWeek = date('Y-m-d', strtotime('monday this week'));
                    $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
                    $start = "$startOfWeek 00:00:00";
                    $end = "$endOfWeek 23:59:59";
                    $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                    \Log::info('This Week Range - Start: ' . $start . ', End: ' . $end);
                    break;
                case 'this_month':
                    $startOfMonth = date('Y-m-01');
                    $endOfMonth = date('Y-m-t');
                    $start = "$startOfMonth 00:00:00";
                    $end = "$endOfMonth 23:59:59";
                    $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                    \Log::info('This Month Range - Start: ' . $start . ', End: ' . $end);
                    break;
                case 'this_year':
                    $year = date('Y');
                    $start = "$year-01-01 00:00:00";
                    $end = "$year-12-31 23:59:59";
                    $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                    \Log::info('This Year Range - Start: ' . $start . ', End: ' . $end);
                    break;
                case 'all_time':
                    break;
                default:
                    return response()->json(['error' => 'Invalid filter'], 400);
            }
        }

        \Log::info('Checkins Filters: ' . json_encode($filters));

        $checkinsData = $this->supabaseService->fetchTable('checkins', $filters, false);
        if ($checkinsData === null) {
            \Log::error('Failed to fetch checkins data');
            return response()->json(['error' => 'Failed to fetch checkins data'], 500);
        }

        $touristIds = array_column($checkinsData, 'tourist_id');
        $touristIds = array_filter($touristIds, fn($id) => !is_null($id));
        if (empty($touristIds)) {
            return response()->json(['count' => 0, 'touristIds' => []]);
        }

        $userFilters = [
            'select' => 'user_id',
            'user_type' => 'eq.user',
            'user_id' => 'in.(' . implode(',', $touristIds) . ')'
        ];

        \Log::info('Users Filters: ' . json_encode($userFilters));

        $usersData = $this->supabaseService->fetchTable('users', $userFilters, false);
        if ($usersData === null) {
            \Log::error('Failed to fetch users data');
            return response()->json(['error' => 'Failed to fetch users data'], 500);
        }

        $touristUserIds = array_column($usersData, 'user_id');
        $uniqueTouristIds = array_unique($touristUserIds);
        $count = count($uniqueTouristIds);

        return response()->json([
            'count' => $count,
            'touristIds' => $uniqueTouristIds
        ]);
    }

    public function getCheckinsBySpot($filter)
    {
        $now = date('Y-m-d H:i:s');
        $todayDate = date('Y-m-d');
        $filters = [];

        \Log::info('Filter: ' . $filter);
        \Log::info('Current DateTime (Raw): ' . $now);
        \Log::info('Today Date (Raw): ' . $todayDate);

        if (strpos($filter, 'custom_year:') === 0) {
            $year = substr($filter, strlen('custom_year:'));
            if (is_numeric($year) && strlen($year) == 4) {
                $start = "$year-01-01T00:00:00";
                $end = "$year-12-31T23:59:59";
                $filters = [
                    'select' => '*, tourist_spots!inner(name, latitude, longitude)',
                    'and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"
                ];
                \Log::info('Custom Year Range - Start: ' . $start . ', End: ' . $end);
            } else {
                return response()->json(['error' => 'Invalid year'], 400);
            }
        } else {
            switch ($filter) {
                case 'today':
                    $start = "$todayDate\T00:00:00";
                    $end = "$todayDate\T23:59:59";
                    $filters = [
                        'select' => '*, tourist_spots!inner(name, latitude, longitude)',
                        'and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"
                    ];
                    \Log::info('Today Range - Start: ' . $start . ', End: ' . $end);
                    break;
                case 'this_week':
                    $startOfWeek = date('Y-m-d', strtotime('monday this week'));
                    $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
                    $start = "$startOfWeek\T00:00:00";
                    $end = "$endOfWeek\T23:59:59";
                    $filters = [
                        'select' => '*, tourist_spots!inner(name, latitude, longitude)',
                        'and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"
                    ];
                    \Log::info('This Week Range - Start: ' . $start . ', End: ' . $end);
                    break;
                case 'this_month':
                    $startOfMonth = date('Y-m-01');
                    $endOfMonth = date('Y-m-t');
                    $start = "$startOfMonth\T00:00:00";
                    $end = "$endOfMonth\T23:59:59";
                    $filters = [
                        'select' => '*, tourist_spots!inner(name, latitude, longitude)',
                        'and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"
                    ];
                    \Log::info('This Month Range - Start: ' . $start . ', End: ' . $end);
                    break;
                case 'this_year':
                    $year = date('Y');
                    $start = "$year-01-01T00:00:00";
                    $end = "$year-12-31T23:59:59";
                    $filters = [
                        'select' => '*, tourist_spots!inner(name, latitude, longitude)',
                        'and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"
                    ];
                    \Log::info('This Year Range - Start: ' . $start . ', End: ' . $end);
                    break;
                case 'all_time':
                    $filters = [
                        'select' => '*, tourist_spots!inner(name, latitude, longitude)'
                    ];
                    break;
                default:
                    return response()->json(['error' => 'Invalid filter'], 400);
            }
        }

        $data = $this->supabaseService->fetchTable('checkins', $filters, false);
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
        return $this->supabaseService->getIncidentReports($filter);
    }

    public function getLatestTourists()
    {
        try {
            $filters = [
                'user_type' => 'eq.user',
                'order' => 'created_at.desc',
                'limit' => 3,
            ];

            Log::info('Fetching latest tourists with filters: ' . json_encode($filters));
            $data = $this->supabaseService->fetchTable('users', $filters, false);

            if ($data === null || !is_array($data)) {
                Log::warning('Supabase returned null or invalid data for latest tourists');
                return response()->json(['tourists' => [], 'message' => 'No tourists found'], 200);
            }

            Log::info('Raw data from Supabase: ' . json_encode($data));

            $ids = array_map(function ($user) {
                $touristId = $user['user_id'] ?? $user['id'] ?? null;
                if ($touristId === null) {
                    Log::warning('User record missing ID field: ' . json_encode($user));
                }
                return $touristId;
            }, $data);

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
            $users = $this->supabaseService->fetchTable('users');

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

    public function getPopularSpots($filter)
    {
        $cacheKey = 'popular_spots_' . $filter;
        $data = Cache::remember($cacheKey, 60, function () use ($filter) {
            $now = date('Y-m-d H:i:s');
            $todayDate = date('Y-m-d');
            $filters = ['select' => 'spot_id,tourist_id'];

            \Log::info('Filter: ' . $filter);
            \Log::info('Current DateTime (Raw): ' . $now);
            \Log::info('Today Date (Raw): ' . $todayDate);

            if (strpos($filter, 'custom_year:') === 0) {
                $year = substr($filter, strlen('custom_year:'));
                if (is_numeric($year) && strlen($year) == 4) {
                    $start = "$year-01-01T00:00:00";
                    $end = "$year-12-31T23:59:59";
                    $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                    \Log::info('Custom Year Range - Start: ' . $start . ', End: ' . $end);
                } else {
                    throw new \Exception('Invalid year');
                }
            } else {
                switch ($filter) {
                    case 'today':
                        $start = "$todayDate\T00:00:00";
                        $end = "$todayDate\T23:59:59";
                        $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                        \Log::info('Today Range - Start: ' . $start . ', End: ' . $end);
                        break;
                    case 'this_week':
                        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
                        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
                        $start = "$startOfWeek\T00:00:00";
                        $end = "$endOfWeek\T23:59:59";
                        $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                        \Log::info('This Week Range - Start: ' . $start . ', End: ' . $end);
                        break;
                    case 'this_month':
                        $startOfMonth = date('Y-m-01');
                        $endOfMonth = date('Y-m-t');
                        $start = "$startOfMonth\T00:00:00";
                        $end = "$endOfMonth\T23:59:59";
                        $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                        \Log::info('This Month Range - Start: ' . $start . ', End: ' . $end);
                        break;
                    case 'this_year':
                        $year = date('Y');
                        $start = "$year-01-01T00:00:00";
                        $end = "$year-12-31T23:59:59";
                        $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                        \Log::info('This Year Range - Start: ' . $start . ', End: ' . $end);
                        break;
                    case 'all_time':
                        break;
                    default:
                        $startOfLastMonth = date('Y-m-01', strtotime('first day of last month'));
                        $endOfLastMonth = date('Y-m-t', strtotime('last day of last month'));
                        $start = "$startOfLastMonth\T00:00:00";
                        $end = "$endOfLastMonth\T23:59:59";
                        $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                        \Log::info('Default Range - Start: ' . $start . ', End: ' . $end);
                        break;
                }
            }

            $checkins = $this->supabaseService->fetchTable('checkins', $filters);

            $spotVisits = [];
            if ($filter === 'today' || strpos($filter, 'custom_year:') === 0) {
                $uniqueTouristsPerSpot = [];
                foreach ($checkins as $checkin) {
                    $spotId = $checkin['spot_id'];
                    $touristId = $checkin['tourist_id'];
                    if (!isset($uniqueTouristsPerSpot[$spotId])) {
                        $uniqueTouristsPerSpot[$spotId] = [];
                    }
                    if (!array_key_exists($touristId, $uniqueTouristsPerSpot[$spotId])) {
                        $uniqueTouristsPerSpot[$spotId][$touristId] = true;
                        $spotVisits[$spotId] = ($spotVisits[$spotId] ?? 0) + 1;
                    }
                }
            } else {
                foreach ($checkins as $checkin) {
                    $spotId = $checkin['spot_id'];
                    if ($spotId) {
                        $spotVisits[$spotId] = ($spotVisits[$spotId] ?? 0) + 1;
                    }
                }
            }

            $touristSpots = Cache::remember('tourist_spots', 3600, function () {
                return $this->supabaseService->fetchTable('tourist_spots', ['select' => 'spot_id,name']);
            });

            $spotIdToName = array_column($touristSpots, 'name', 'spot_id');
            $data = array_map(function($id) use ($spotVisits, $spotIdToName) {
                $name = $spotIdToName[$id] ?? 'Unknown';
                return ['spot' => $name, 'visits' => $spotVisits[$id] ?? 0];
            }, array_keys($spotVisits));

            return $data;
        });

        return response()->json($data);
    }

    private function getDateFilter($filter)
    {
        $now = date('Y-m-d H:i:s');
        $todayDate = date('Y-m-d');

        switch ($filter) {
            case 'today':
                $start = "$todayDate\T00:00:00";
                $end = "$todayDate\T23:59:59";
                \Log::info('getDateFilter Today Range - Start: ' . $start . ', End: ' . $end);
                return ['and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"];
            case 'this_week':
                $startOfWeek = date('Y-m-d', strtotime('monday this week'));
                $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
                $start = "$startOfWeek\T00:00:00";
                $end = "$endOfWeek\T23:59:59";
                \Log::info('getDateFilter This Week Range - Start: ' . $start . ', End: ' . $end);
                return ['and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"];
            case 'this_month':
                $startOfMonth = date('Y-m-01');
                $endOfMonth = date('Y-m-t');
                $start = "$startOfMonth\T00:00:00";
                $end = "$endOfMonth\T23:59:59";
                \Log::info('getDateFilter This Month Range - Start: ' . $start . ', End: ' . $end);
                return ['and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"];
            case 'this_year':
                $year = date('Y');
                $start = "$year-01-01T00:00:00";
                $end = "$year-12-31T23:59:59";
                \Log::info('getDateFilter This Year Range - Start: ' . $start . ', End: ' . $end);
                return ['and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"];
            case 'all_time':
                return [];
            default:
                $startOfLastMonth = date('Y-m-01', strtotime('first day of last month'));
                $endOfLastMonth = date('Y-m-t', strtotime('last day of last month'));
                $start = "$startOfLastMonth\T00:00:00";
                $end = "$endOfLastMonth\T23:59:59";
                \Log::info('getDateFilter Default Range - Start: ' . $start . ', End: ' . $end);
                return ['and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"];
        }
    }
}