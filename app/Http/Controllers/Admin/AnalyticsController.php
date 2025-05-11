<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    protected $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    public function index()
    {
        // Stat Cards
        $todayCheckins = $this->supabaseService->fetchTable('checkins', [
            'timestamp' => 'gte.' . Carbon::today('Asia/Manila')->startOfDay()->toIso8601String(),
            'timestamp' => 'lt.' . Carbon::today('Asia/Manila')->endOfDay()->toIso8601String()
        ]);
        Log::info('Today checkins', ['checkins' => $todayCheckins]);
        $touristArrivals = count(array_unique(array_column($todayCheckins, 'tourist_id')));

        $yesterdayCheckins = $this->supabaseService->fetchTable('checkins', [
            'timestamp' => 'gte.' . Carbon::yesterday('Asia/Manila')->startOfDay()->toIso8601String(),
            'timestamp' => 'lt.' . Carbon::today('Asia/Manila')->startOfDay()->toIso8601String()
        ]);
        $touristArrivalsYesterday = count(array_unique(array_column($yesterdayCheckins, 'tourist_id')));
        $touristChange = $touristArrivalsYesterday ? round((($touristArrivals - $touristArrivalsYesterday) / $touristArrivalsYesterday) * 100) : 0;

        $incidentReports = $this->supabaseService->fetchTable('emergency_reports', [], true);
        $incidentReportsLastWeek = $this->supabaseService->fetchTable('emergency_reports', ['timestamp' => 'gte.' . now('Asia/Manila')->subWeek()->toIso8601String()], true);
        $incidentChange = $incidentReportsLastWeek ? round((($incidentReports - $incidentReportsLastWeek) / $incidentReportsLastWeek) * 100) : 0;

        $users = $this->supabaseService->fetchTable('users');
        $touristAccounts = count(array_filter($users, fn($user) => $user['user_type'] === 'user'));
        $touristAccountsLastMonth = count(array_filter($users, fn($user) => Carbon::parse($user['created_at'], 'Asia/Manila')->gte(now('Asia/Manila')->subMonth()) && $user['user_type'] === 'user'));
        $touristAccountsChange = $touristAccountsLastMonth ? round((($touristAccounts - $touristAccountsLastMonth) / $touristAccountsLastMonth) * 100) : 0;

        $adminAccounts = count(array_filter($users, fn($user) => 
            in_array($user['user_type'], ['admin', 'super_admin']) && 
            ($user['status'] ?? '') !== 'locked'
        ));
        $adminAccountsLastMonth = count(array_filter($users, fn($user) => 
            Carbon::parse($user['created_at'], 'Asia/Manila')->gte(now('Asia/Manila')->subMonth()) && 
            in_array($user['user_type'], ['admin', 'super_admin']) && 
            ($user['status'] ?? '') !== 'locked'
        ));
        $adminAccountsChange = $adminAccountsLastMonth ? round((($adminAccounts - $adminAccountsLastMonth) / $adminAccountsLastMonth) * 100) : 0;

        // Incident Status
        $incidentStatus = $this->supabaseService->fetchTable('emergency_reports');
        $incidentStatusCounts = array_count_values(array_map('strtolower', array_column($incidentStatus, 'status')));
        $incidentStatusLabels = array_map('ucfirst', array_keys($incidentStatusCounts));
        $incidentStatusData = array_values($incidentStatusCounts);

        // Tourist Activities (formerly Weekly Analytics)
        $touristActivitiesData = $this->getTouristActivities(new Request(['period' => 'this_week']))->getData(true);
        $weeklyLabels = $touristActivitiesData['labels'];
        $weeklyCheckinsData = $touristActivitiesData['checkins'];
        $weeklyIncidentsData = $touristActivitiesData['incidents'];
        $totalActivities = $touristActivitiesData['totalActivities'];

        // User Growth
        $userGrowth = $this->supabaseService->fetchTable('users');
        $userGrowthByDate = [];
        foreach ($userGrowth as $user) {
            $date = Carbon::parse($user['created_at'], 'Asia/Manila')->format('Y-m-d');
            $userGrowthByDate[$date] = ($userGrowthByDate[$date] ?? 0) + 1;
        }
        ksort($userGrowthByDate);
        $userGrowthLabels = array_keys($userGrowthByDate);
        $userGrowthData = array_values($userGrowthByDate);

        // User Type Distribution
        $userTypeCounts = array_count_values(array_column($users, 'user_type'));
        $userTypeLabels = array_keys($userTypeCounts);
        $userTypeData = array_values($userTypeCounts);

        // Popular Tourist Spots
        $checkins = $this->supabaseService->fetchTable('checkins');
        $touristSpots = $this->supabaseService->fetchTable('tourist_spots');
        $spotVisits = [];
        foreach ($checkins as $checkin) {
            $spotId = $checkin['spot_id'];
            $spotVisits[$spotId] = ($spotVisits[$spotId] ?? 0) + 1;
        }
        arsort($spotVisits);
        $popularSpots = array_slice($spotVisits, 0, 4, true);
        $popularSpotsLabels = array_map(function($id) use ($touristSpots) {
            $index = array_search($id, array_column($touristSpots, 'spot_id'));
            return $index !== false ? $touristSpots[$index]['name'] : 'Unknown';
        }, array_keys($popularSpots));
        $popularSpotsData = array_values($popularSpots);

        // Map Data
        $touristSpots = $this->supabaseService->fetchTable('tourist_spots');
        $incidents = $this->supabaseService->fetchTable('emergency_reports');
        $checkins = $this->supabaseService->fetchTable('checkins');
        $users = $this->supabaseService->fetchTable('users');

        // Latest Incidents
        $latestIncidents = array_slice($incidents, 0, 5);

        // Define change classes and icons
        $touristChangeClass = $touristChange > 0 ? 'increase' : ($touristChange < 0 ? 'decrease' : 'neutral');
        $touristChangeIcon = $touristChange > 0 ? 'fa-arrow-up' : ($touristChange < 0 ? 'fa-arrow-down' : 'fa-equals');

        $incidentChangeClass = $incidentChange > 0 ? 'increase' : ($incidentChange < 0 ? 'decrease' : 'neutral');
        $incidentChangeIcon = $incidentChange > 0 ? 'fa-arrow-up' : ($incidentChange < 0 ? 'fa-arrow-down' : 'fa-equals');

        $touristAccountsChangeClass = $touristAccountsChange > 0 ? 'increase' : ($touristAccountsChange < 0 ? 'decrease' : 'neutral');
        $touristAccountsChangeIcon = $touristAccountsChange > 0 ? 'fa-arrow-up' : ($touristAccountsChange < 0 ? 'fa-arrow-down' : 'fa-equals');

        $adminAccountsChangeClass = $adminAccountsChange > 0 ? 'increase' : ($adminAccountsChange < 0 ? 'decrease' : 'neutral');
        $adminAccountsChangeIcon = $adminAccountsChange > 0 ? 'fa-arrow-up' : ($adminAccountsChange < 0 ? 'fa-arrow-down' : 'fa-equals');

        return view('vendor.backpack.ui.analytics', compact(
            'touristArrivals', 'touristChange', 'touristChangeClass', 'touristChangeIcon',
            'incidentReports', 'incidentChange', 'incidentChangeClass', 'incidentChangeIcon',
            'touristAccounts', 'touristAccountsChange', 'touristAccountsChangeClass', 'touristAccountsChangeIcon',
            'adminAccounts', 'adminAccountsChange', 'adminAccountsChangeClass', 'adminAccountsChangeIcon',
            'incidentStatusLabels', 'incidentStatusData',
            'weeklyLabels', 'weeklyCheckinsData', 'weeklyIncidentsData',
            'userGrowthLabels', 'userGrowthData',
            'userTypeLabels', 'userTypeData',
            'popularSpotsLabels', 'popularSpotsData',
            'touristSpots', 'incidents', 'checkins', 'users',
            'latestIncidents', 'totalActivities'
        ));
    }

    public function getIncidentStatus(Request $request)
    {
        $period = $request->query('period', 'month');
        $year = $request->query('year');

        if ($period === 'today') {
            $dateFilter = Carbon::today('Asia/Manila');
            $filters = [
                'timestamp' => 'gte.' . $dateFilter->startOfDay()->toIso8601String(),
                'timestamp' => 'lt.' . $dateFilter->endOfDay()->toIso8601String()
            ];
        } elseif ($period === 'custom_year' && $year) {
            $dateFilter = Carbon::createFromDate($year, 1, 1, 'Asia/Manila');
            $filters = [
                'timestamp' => 'gte.' . $dateFilter->startOfYear()->toIso8601String(),
                'timestamp' => 'lt.' . $dateFilter->endOfYear()->toIso8601String()
            ];
        } elseif ($period === 'all_time') {
            $filters = [];
        } else {
            $dateFilter = now('Asia/Manila')->subMonth();
            if ($period === 'week') $dateFilter = now('Asia/Manila')->subWeek();
            elseif ($period === 'year') $dateFilter = now('Asia/Manila')->subYear();
            $filters = ['timestamp' => 'gte.' . $dateFilter->toIso8601String()];
        }

        $incidentStatus = $this->supabaseService->fetchTable('emergency_reports', $filters);
        $incidentStatusCounts = array_count_values(array_map('strtolower', array_column($incidentStatus, 'status')));
        $incidentStatusLabels = array_map('ucfirst', array_keys($incidentStatusCounts));
        $incidentStatusData = array_values($incidentStatusCounts);

        return response()->json([
            'labels' => $incidentStatusLabels,
            'values' => $incidentStatusData
        ]);
    }

    public function getPopularSpots($filter)
    {
        $cacheKey = 'popular_spots_' . $filter;
        $data = Cache::remember($cacheKey, 60, function () use ($filter) {
            $dateFilter = $this->getDateFilter($filter);
            $filters = [];
            if (is_array($dateFilter)) {
                $filters = [
                    'timestamp' => 'gte.' . $dateFilter['gte'],
                    'timestamp' => 'lt.' . $dateFilter['lt']
                ];
            }
            $checkins = $this->supabaseService->fetchTable('checkins', $filters);
            Log::info('Fetched checkins for filter', ['filter' => $filter, 'checkins' => $checkins]);

            $spotVisits = [];
            if ($filter === 'today') {
                $today = Carbon::today('Asia/Manila')->toDateString();
                $uniqueTouristsPerSpot = [];
                foreach ($checkins as $checkin) {
                    $spotId = $checkin['spot_id'];
                    $touristId = $checkin['tourist_id'];
                    $checkinDate = Carbon::parse($checkin['timestamp'], 'Asia/Manila')->toDateString();
                    if ($checkinDate === $today) {
                        if (!isset($uniqueTouristsPerSpot[$spotId])) {
                            $uniqueTouristsPerSpot[$spotId] = [];
                        }
                        if (!in_array($touristId, $uniqueTouristsPerSpot[$spotId])) {
                            $uniqueTouristsPerSpot[$spotId][] = $touristId;
                            $spotVisits[$spotId] = ($spotVisits[$spotId] ?? 0) + 1;
                        }
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

            arsort($spotVisits);
            $topSpotIds = array_slice(array_keys($spotVisits), 0, 4);

            $touristSpots = $this->supabaseService->fetchTable('tourist_spots');
            $data = [];
            foreach ($topSpotIds as $id) {
                $name = $touristSpots[array_search($id, array_column($touristSpots, 'spot_id'))]['name'] ?? 'Unknown';
                $data[] = ['spot' => $name, 'visits' => $spotVisits[$id] ?? 0];
            }
            return $data;
        });

        return response()->json($data);
    }

    private function getDateFilter($filter)
    {
        $now = Carbon::now('Asia/Manila');
        switch ($filter) {
            case 'today':
                return [
                    'gte' => $now->startOfDay()->toIso8601String(),
                    'lt' => $now->endOfDay()->toIso8601String()
                ];
            case 'this_week':
                return [
                    'gte' => $now->startOfWeek()->toIso8601String(),
                    'lt' => $now->endOfWeek()->toIso8601String()
                ];
            case 'this_month':
                return [
                    'gte' => $now->startOfMonth()->toIso8601String(),
                    'lt' => $now->endOfMonth()->toIso8601String()
                ];
            case 'all_time':
                return null;
            default:
                return [
                    'gte' => $now->subMonth()->startOfMonth()->toIso8601String(),
                    'lt' => $now->subMonth()->endOfMonth()->toIso8601String()
                ];
        }
    }

    public function getTouristActivities(Request $request)
    {
        $period = $request->query('period', 'this_week');
        $now = now('Asia/Manila');

        // Determine start and end dates based on period
        if ($period === 'this_week') {
            $startDate = $now->startOfWeek();
            $endDate = $now->endOfWeek();
            $periodLabel = 'This Week';
            $groupBy = 'day';
        } elseif ($period === 'this_month') {
            $startDate = $now->startOfMonth();
            $endDate = $now->endOfMonth();
            $periodLabel = 'This Month';
            $groupBy = 'week';
        } elseif ($period === 'this_year') {
            $startDate = $now->startOfYear();
            $endDate = $now->endOfYear();
            $periodLabel = 'This Year';
            $groupBy = 'month';
        } elseif ($period === 'custom_year') {
            $year = $request->query('year');
            if (!$year || !is_numeric($year) || strlen($year) != 4) {
                return response()->json(['error' => 'Invalid year'], 400);
            }
            $startDate = Carbon::createFromDate($year, 1, 1, 'Asia/Manila')->startOfDay();
            $endDate = Carbon::createFromDate($year, 12, 31, 'Asia/Manila')->endOfDay();
            $periodLabel = "Year $year";
            $groupBy = 'month';
        } else {
            return response()->json(['error' => 'Invalid period'], 400);
        }

        // Fetch data from Supabase
        $filters = [
            'timestamp' => 'gte.' . $startDate->toIso8601String(),
            'timestamp' => 'lt.' . $endDate->toIso8601String()
        ];
        $checkins = $this->supabaseService->fetchTable('checkins', $filters);
        $incidents = $this->supabaseService->fetchTable('emergency_reports', $filters);

        // Prepare labels and data based on grouping
        if ($groupBy === 'day') {
            $labels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $checkinsData = array_fill(0, 7, 0);
            $incidentsData = array_fill(0, 7, 0);

            foreach ($checkins as $checkin) {
                $day = Carbon::parse($checkin['timestamp'], 'Asia/Manila')->format('l');
                $index = array_search($day, $labels);
                if ($index !== false) $checkinsData[$index]++;
            }
            foreach ($incidents as $incident) {
                $day = Carbon::parse($incident['timestamp'], 'Asia/Manila')->format('l');
                $index = array_search($day, $labels);
                if ($index !== false) $incidentsData[$index]++;
            }
        } elseif ($groupBy === 'week') {
            $numWeeks = ceil($endDate->day / 7);
            $labels = [];
            for ($i = 1; $i <= $numWeeks; $i++) {
                $labels[] = "Week $i";
            }
            $checkinsData = array_fill(0, $numWeeks, 0);
            $incidentsData = array_fill(0, $numWeeks, 0);

            foreach ($checkins as $checkin) {
                $date = Carbon::parse($checkin['timestamp'], 'Asia/Manila');
                $week = ceil($date->day / 7);
                if ($week >= 1 && $week <= $numWeeks) {
                    $checkinsData[$week - 1]++;
                }
            }
            foreach ($incidents as $incident) {
                $date = Carbon::parse($incident['timestamp'], 'Asia/Manila');
                $week = ceil($date->day / 7);
                if ($week >= 1 && $week <= $numWeeks) {
                    $incidentsData[$week - 1]++;
                }
            }
        } elseif ($groupBy === 'month') {
            $labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            $checkinsData = array_fill(0, 12, 0);
            $incidentsData = array_fill(0, 12, 0);

            foreach ($checkins as $checkin) {
                $month = Carbon::parse($checkin['timestamp'], 'Asia/Manila')->month;
                $checkinsData[$month - 1]++;
            }
            foreach ($incidents as $incident) {
                $month = Carbon::parse($incident['timestamp'], 'Asia/Manila')->month;
                $incidentsData[$month - 1]++;
            }
        }

        $totalActivities = array_sum($checkinsData);

        return response()->json([
            'labels' => $labels,
            'checkins' => $checkinsData,
            'incidents' => $incidentsData,
            'totalActivities' => $totalActivities,
            'period' => $periodLabel
        ]);
    }
}