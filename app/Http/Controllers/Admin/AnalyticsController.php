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
        $now = Carbon::now('Asia/Manila');

        // Tourist Arrivals (Today vs Yesterday)
        $todayStart = $now->copy()->startOfDay()->format('Y-m-d\TH:i:s');
        $todayEnd = $now->copy()->endOfDay()->format('Y-m-d\TH:i:s');
        $todayCheckins = $this->supabaseService->fetchTable('checkins', [
            'and' => "(timestamp.gte.{$todayStart},timestamp.lte.{$todayEnd})"
        ]);
        Log::info('Today checkins', ['checkins' => count($todayCheckins)]);
        $touristArrivals = count(array_unique(array_column($todayCheckins, 'tourist_id')));

        $yesterdayStart = $now->copy()->subDay()->startOfDay()->format('Y-m-d\TH:i:s');
        $yesterdayEnd = $now->copy()->subDay()->endOfDay()->format('Y-m-d\TH:i:s');
        $yesterdayCheckins = $this->supabaseService->fetchTable('checkins', [
            'and' => "(timestamp.gte.{$yesterdayStart},timestamp.lte.{$yesterdayEnd})"
        ]);
        $touristArrivalsYesterday = count(array_unique(array_column($yesterdayCheckins, 'tourist_id')));
        $touristChange = $touristArrivalsYesterday ? round((($touristArrivals - $touristArrivalsYesterday) / $touristArrivalsYesterday) * 100) : ($touristArrivals > 0 ? 100 : 0);

        // Incident Reports (This Week so far vs Last Week)
        $thisWeekStart = $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $thisWeekStartStr = $thisWeekStart->format('Y-m-d\TH:i:s');
        $thisWeekEndStr = $now->format('Y-m-d\TH:i:s');
        $incidentReportsThisWeek = $this->supabaseService->fetchTable('emergency_reports', [
            'and' => "(timestamp.gte.{$thisWeekStartStr},timestamp.lte.{$thisWeekEndStr})"
        ], true);

        $lastWeekStart = $thisWeekStart->copy()->subWeek();
        $lastWeekEnd = $lastWeekStart->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
        $lastWeekStartStr = $lastWeekStart->format('Y-m-d\TH:i:s');
        $lastWeekEndStr = $lastWeekEnd->format('Y-m-d\TH:i:s');
        $incidentReportsLastWeek = $this->supabaseService->fetchTable('emergency_reports', [
            'and' => "(timestamp.gte.{$lastWeekStartStr},timestamp.lte.{$lastWeekEndStr})"
        ], true);

        $incidentReports = $incidentReportsThisWeek;
        $incidentChange = $incidentReportsLastWeek > 0 ? round((($incidentReportsThisWeek - $incidentReportsLastWeek) / $incidentReportsLastWeek) * 100) : ($incidentReportsThisWeek > 0 ? 100 : 0);

        // Tourist Accounts (This Month vs Last Month)
        $users = $this->supabaseService->fetchTable('users');
        $thisMonthStart = $now->copy()->startOfMonth()->startOfDay();
        $lastMonthStart = $thisMonthStart->copy()->subMonth();
        $lastMonthEnd = $thisMonthStart->copy()->subDay()->endOfDay();

        $newThisMonth = count(array_filter($users, fn($user) => 
            Carbon::parse($user['created_at'], 'Asia/Manila')->gte($thisMonthStart) && 
            $user['user_type'] === 'user'
        ));
        $newLastMonth = count(array_filter($users, fn($user) => 
            Carbon::parse($user['created_at'], 'Asia/Manila')->gte($lastMonthStart) && 
            Carbon::parse($user['created_at'], 'Asia/Manila')->lte($lastMonthEnd) && 
            $user['user_type'] === 'user'
        ));
        $touristAccounts = $newThisMonth;
        $touristAccountsChange = $newLastMonth > 0 ? round((($newThisMonth - $newLastMonth) / $newLastMonth) * 100) : ($newThisMonth > 0 ? 100 : 0);

        // Admin Accounts (Total only)
        $adminAccounts = count(array_filter($users, fn($user) => 
            in_array($user['user_type'], ['admin', 'super_admin']) && 
            ($user['status'] ?? '') !== 'locked'
        ));

        // Incident Status for Today
        $filters = [
            'and' => "(timestamp.gte.{$todayStart},timestamp.lte.{$todayEnd})"
        ];
        $todayIncidentStatus = $this->supabaseService->fetchTable('emergency_reports', $filters);
        $incidentStatusCounts = array_count_values(array_map('strtolower', array_column($todayIncidentStatus, 'status')));
        $incidentStatusLabels = array_map('ucfirst', array_keys($incidentStatusCounts));
        $incidentStatusData = array_values($incidentStatusCounts);

        // Tourist Activities
        $touristActivitiesData = $this->getTouristActivities(new Request(['period' => 'this_week']))->getData(true);
        $weeklyLabels = $touristActivitiesData['labels'];
        $weeklyCheckinsData = $touristActivitiesData['checkins'];
        $weeklyIncidentsData = $touristActivitiesData['incidents'];
        $totalActivities = $touristActivitiesData['totalActivities'];
        $totalIncidents = $touristActivitiesData['totalIncidents'];
        $initialPeriod = $touristActivitiesData['period'];

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

        // Get min and max years for custom year validation
        $minMaxYears = $this->supabaseService->getMinMaxYears();
        $minYear = $minMaxYears['min'] ?? 1900;
        $maxYear = $minMaxYears['max'] ?? $now->year;

        return view('vendor.backpack.ui.analytics', compact(
            'touristArrivals', 'touristChange', 'touristChangeClass', 'touristChangeIcon',
            'incidentReports', 'incidentChange', 'incidentChangeClass', 'incidentChangeIcon',
            'touristAccounts', 'touristAccountsChange', 'touristAccountsChangeClass', 'touristAccountsChangeIcon',
            'adminAccounts',
            'incidentStatusLabels', 'incidentStatusData',
            'weeklyLabels', 'weeklyCheckinsData', 'weeklyIncidentsData',
            'userTypeLabels', 'userTypeData',
            'popularSpotsLabels', 'popularSpotsData',
            'touristSpots', 'incidents', 'checkins', 'users',
            'latestIncidents', 'totalActivities',
            'totalIncidents', 'initialPeriod',
            'minYear', 'maxYear'
        ));
    }

    // New method to fetch stats card data
    public function stats()
    {
        $now = Carbon::now('Asia/Manila');

        // Tourist Arrivals
        $todayStart = $now->copy()->startOfDay()->format('Y-m-d\TH:i:s');
        $todayEnd = $now->copy()->endOfDay()->format('Y-m-d\TH:i:s');
        $todayCheckins = $this->supabaseService->fetchTable('checkins', [
            'and' => "(timestamp.gte.{$todayStart},timestamp.lte.{$todayEnd})"
        ]);
        $touristArrivals = count(array_unique(array_column($todayCheckins, 'tourist_id')));

        $yesterdayStart = $now->copy()->subDay()->startOfDay()->format('Y-m-d\TH:i:s');
        $yesterdayEnd = $now->copy()->subDay()->endOfDay()->format('Y-m-d\TH:i:s');
        $yesterdayCheckins = $this->supabaseService->fetchTable('checkins', [
            'and' => "(timestamp.gte.{$yesterdayStart},timestamp.lte.{$yesterdayEnd})"
        ]);
        $touristArrivalsYesterday = count(array_unique(array_column($yesterdayCheckins, 'tourist_id')));
        $touristChange = $touristArrivalsYesterday ? round((($touristArrivals - $touristArrivalsYesterday) / $touristArrivalsYesterday) * 100) : ($touristArrivals > 0 ? 100 : 0);
        $touristChangeClass = $touristChange > 0 ? 'increase' : ($touristChange < 0 ? 'decrease' : 'neutral');
        $touristChangeIcon = $touristChange > 0 ? 'fa-arrow-up' : ($touristChange < 0 ? 'fa-arrow-down' : 'fa-equals');

        // Incident Reports
        $thisWeekStart = $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $thisWeekStartStr = $thisWeekStart->format('Y-m-d\TH:i:s');
        $thisWeekEndStr = $now->format('Y-m-d\TH:i:s');
        $incidentReportsThisWeek = $this->supabaseService->fetchTable('emergency_reports', [
            'and' => "(timestamp.gte.{$thisWeekStartStr},timestamp.lte.{$thisWeekEndStr})"
        ], true);

        $lastWeekStart = $thisWeekStart->copy()->subWeek();
        $lastWeekEnd = $lastWeekStart->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
        $lastWeekStartStr = $lastWeekStart->format('Y-m-d\TH:i:s');
        $lastWeekEndStr = $lastWeekEnd->format('Y-m-d\TH:i:s');
        $incidentReportsLastWeek = $this->supabaseService->fetchTable('emergency_reports', [
            'and' => "(timestamp.gte.{$lastWeekStartStr},timestamp.lte.{$lastWeekEndStr})"
        ], true);

        $incidentReports = $incidentReportsThisWeek;
        $incidentChange = $incidentReportsLastWeek > 0 ? round((($incidentReportsThisWeek - $incidentReportsLastWeek) / $incidentReportsLastWeek) * 100) : ($incidentReportsThisWeek > 0 ? 100 : 0);
        $incidentChangeClass = $incidentChange > 0 ? 'increase' : ($incidentChange < 0 ? 'decrease' : 'neutral');
        $incidentChangeIcon = $incidentChange > 0 ? 'fa-arrow-up' : ($incidentChange < 0 ? 'fa-arrow-down' : 'fa-equals');

        // Tourist Accounts
        $users = $this->supabaseService->fetchTable('users');
        $thisMonthStart = $now->copy()->startOfMonth()->startOfDay();
        $lastMonthStart = $thisMonthStart->copy()->subMonth();
        $lastMonthEnd = $thisMonthStart->copy()->subDay()->endOfDay();

        $newThisMonth = count(array_filter($users, fn($user) => 
            Carbon::parse($user['created_at'], 'Asia/Manila')->gte($thisMonthStart) && 
            $user['user_type'] === 'user'
        ));
        $newLastMonth = count(array_filter($users, fn($user) => 
            Carbon::parse($user['created_at'], 'Asia/Manila')->gte($lastMonthStart) && 
            Carbon::parse($user['created_at'], 'Asia/Manila')->lte($lastMonthEnd) && 
            $user['user_type'] === 'user'
        ));
        $touristAccounts = $newThisMonth;
        $touristAccountsChange = $newLastMonth > 0 ? round((($newThisMonth - $newLastMonth) / $newLastMonth) * 100) : ($newThisMonth > 0 ? 100 : 0);
        $touristAccountsChangeClass = $touristAccountsChange > 0 ? 'increase' : ($touristAccountsChange < 0 ? 'decrease' : 'neutral');
        $touristAccountsChangeIcon = $touristAccountsChange > 0 ? 'fa-arrow-up' : ($touristAccountsChange < 0 ? 'fa-arrow-down' : 'fa-equals');

        // Admin Accounts
        $adminAccounts = count(array_filter($users, fn($user) => 
            in_array($user['user_type'], ['admin', 'super_admin']) && 
            ($user['status'] ?? '') !== 'locked'
        ));

        return response()->json([
            'touristArrivals' => $touristArrivals,
            'touristChange' => $touristChange,
            'touristChangeClass' => $touristChangeClass,
            'touristChangeIcon' => $touristChangeIcon,
            'incidentReports' => $incidentReports,
            'incidentChange' => $incidentChange,
            'incidentChangeClass' => $incidentChangeClass,
            'incidentChangeIcon' => $incidentChangeIcon,
            'touristAccounts' => $touristAccounts,
            'touristAccountsChange' => $touristAccountsChange,
            'touristAccountsChangeClass' => $touristAccountsChangeClass,
            'touristAccountsChangeIcon' => $touristAccountsChangeIcon,
            'adminAccounts' => $adminAccounts
        ]);
    }

    public function getTouristGrowth(Request $request)
    {
        $period = $request->query('period', 'this_month');
        $year = $request->query('year');
        $now = Carbon::now('Asia/Manila');

        if ($period === 'custom_year') {
            if (!$year || !is_numeric($year) || strlen($year) != 4) {
                return response()->json(['error' => 'Invalid year'], 400);
            }
            $startDate = Carbon::createFromDate($year, 1, 1, 'Asia/Manila')->startOfDay();
            $endDate = Carbon::createFromDate($year, 12, 31, 'Asia/Manila')->endOfDay();
            $groupBy = 'month';
        } else {
            if ($period === 'this_week') {
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                $groupBy = 'day';
            } elseif ($period === 'this_month') {
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                $groupBy = 'week';
            } elseif ($period === 'this_year') {
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                $groupBy = 'month';
            } elseif ($period === 'all_time') {
                $startDate = null;
                $endDate = null;
                $groupBy = 'month';
            } else {
                return response()->json(['error' => 'Invalid period'], 400);
            }
        }

        if ($period === 'all_time') {
            $filters = ['user_type' => 'eq.user'];
            $users = $this->supabaseService->fetchTable('users', $filters, false, 'created_at');
        } else {
            $start = $startDate->format('Y-m-d\TH:i:s');
            $end = $endDate->format('Y-m-d\TH:i:s');
            $filters = [
                'user_type' => 'eq.user',
                'and' => "(created_at.gte.{$start},created_at.lte.{$end})"
            ];
            $users = $this->supabaseService->fetchTable('users', $filters, false, 'created_at');
        }

        Log::info('Fetched tourists for tourist growth', [
            'period' => $period,
            'tourist_count' => count($users),
            'filters' => $filters
        ]);

        if ($period === 'this_week') {
            $labels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $touristGrowthByDay = array_fill(0, 7, 0);
            foreach ($users as $user) {
                $date = Carbon::parse($user['created_at'], 'Asia/Manila');
                $day = $date->format('l');
                $index = array_search($day, $labels);
                if ($index !== false) {
                    $touristGrowthByDay[$index]++;
                }
            }
            $data = $touristGrowthByDay;
        } elseif ($period === 'this_month') {
            $numWeeks = ceil($endDate->day / 7);
            $labels = [];
            for ($i = 1; $i <= $numWeeks; $i++) {
                $labels[] = "Week $i";
            }
            $touristGrowthByWeek = array_fill(0, $numWeeks, 0);
            foreach ($users as $user) {
                $date = Carbon::parse($user['created_at'], 'Asia/Manila');
                if ($date->month == $now->month && $date->year == $now->year) {
                    $week = ceil($date->day / 7);
                    if ($week >= 1 && $week <= $numWeeks) {
                        $touristGrowthByWeek[$week - 1]++;
                    }
                }
            }
            $data = $touristGrowthByWeek;
        } elseif ($period === 'this_year' || $period === 'custom_year') {
            $labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            $touristGrowthByMonth = array_fill(0, 12, 0);
            $yearToCheck = $period === 'custom_year' ? (int)$year : $now->year;

            foreach ($users as $user) {
                $date = Carbon::parse($user['created_at'], 'Asia/Manila');
                if ($date->year == $yearToCheck) {
                    $month = $date->month;
                    $touristGrowthByMonth[$month - 1]++;
                }
            }
            $data = $touristGrowthByMonth;
        } elseif ($period === 'all_time') {
            $touristGrowthByMonth = [];
            foreach ($users as $user) {
                $date = Carbon::parse($user['created_at'], 'Asia/Manila');
                $dateStr = $date->format('Y-m');
                $touristGrowthByMonth[$dateStr] = ($touristGrowthByMonth[$dateStr] ?? 0) + 1;
            }
            ksort($touristGrowthByMonth);
            $labels = array_map(function($date) {
                return Carbon::createFromFormat('Y-m', $date, 'Asia/Manila')->format('M Y');
            }, $labels);
            $data = array_values($touristGrowthByMonth);
        }

        Log::info('Returning tourist growth data', [
            'labels' => $labels,
            'data' => $data
        ]);

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    public function getIncidentStatus(Request $request)
    {
        $period = $request->query('period', 'month');
        $year = $request->query('year');
        $now = Carbon::now('Asia/Manila');

        if ($period === 'today') {
            $start = $now->copy()->startOfDay()->format('Y-m-d\TH:i:s');
            $end = $now->copy()->endOfDay()->format('Y-m-d\TH:i:s');
            $filters = [
                'and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"
            ];
        } elseif ($period === 'week') {
            $start = $now->copy()->startOfWeek()->startOfDay()->format('Y-m-d\TH:i:s');
            $end = $now->copy()->endOfWeek()->endOfDay()->format('Y-m-d\TH:i:s');
            $filters = [
                'and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"
            ];
        } elseif ($period === 'month') {
            $start = $now->copy()->startOfMonth()->startOfDay()->format('Y-m-d\TH:i:s');
            $end = $now->copy()->endOfMonth()->endOfDay()->format('Y-m-d\TH:i:s');
            $filters = [
                'and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"
            ];
        } elseif ($period === 'year') {
            $start = $now->copy()->startOfYear()->startOfDay()->format('Y-m-d\TH:i:s');
            $end = $now->copy()->endOfYear()->endOfDay()->format('Y-m-d\TH:i:s');
            $filters = [
                'and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"
            ];
        } elseif ($period === 'custom_year' && $year) {
            if (!is_numeric($year) || strlen($year) != 4) {
                return response()->json(['error' => 'Invalid year'], 400);
            }
            $start = Carbon::createFromDate($year, 1, 1, 'Asia/Manila')->startOfDay()->format('Y-m-d\TH:i:s');
            $end = Carbon::createFromDate($year, 12, 31, 'Asia/Manila')->endOfDay()->format('Y-m-d\TH:i:s');
            $filters = [
                'and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"
            ];
        } elseif ($period === 'all_time') {
            $filters = [];
        } else {
            return response()->json(['error' => 'Invalid period'], 400);
        }

        Log::info('Incident Status Filters', [
            'period' => $period,
            'filters' => $filters
        ]);

        $incidentStatus = $this->supabaseService->fetchTable('emergency_reports', $filters);
        $incidentStatusCounts = array_count_values(array_map('strtolower', array_column($incidentStatus, 'status')));
        $incidentStatusLabels = array_map('ucfirst', array_keys($incidentStatusCounts));
        $incidentStatusData = array_values($incidentStatusCounts);

        return response()->json([
            'labels' => $incidentStatusLabels,
            'values' => $incidentStatusData
        ]);
    }

    public function getPopularSpots(Request $request)
    {
        $filter = $request->query('filter', 'all_time');
        $year = $request->query('year');
        $all = $request->query('all', 0);
        $page = $request->query('page', 1);
        $perPage = 5;
        $isModal = $request->query('modal', false);

        $cacheKey = 'popular_spots_' . $filter . ($year ? '_' . $year : '') . ($all ? '_all_page_' . $page : '') . ($isModal ? '_modal' : '');
        $data = Cache::remember($cacheKey, 60, function () use ($filter, $year, $all, $page, $perPage) {
            if ($filter === 'custom_year') {
                if (!$year || !is_numeric($year) || strlen($year) != 4) {
                    return ['error' => 'Invalid year'];
                }
                $minMaxYears = $this->supabaseService->getMinMaxYears();
                $minYear = $minMaxYears['min'] ?? 1900;
                $maxYear = $minMaxYears['max'] ?? now('Asia/Manila')->year;
                if ($year < $minYear || $year > $maxYear) {
                    return ['error' => "Year out of range ($minYear - $maxYear)"];
                }
                $startDate = Carbon::createFromDate($year, 1, 1, 'Asia/Manila')->startOfDay();
                $endDate = Carbon::createFromDate($year, 12, 31, 'Asia/Manila')->endOfDay();
                $filters = [
                    'and' => "(timestamp.gte.{$startDate->format('Y-m-d\TH:i:s')},timestamp.lte.{$endDate->format('Y-m-d\TH:i:s')})"
                ];
                $checkins = $this->supabaseService->fetchTable('checkins', $filters);
            } else {
                $dateFilter = $this->getDateFilter($filter);
                $filters = $dateFilter ?: [];
                $checkins = $this->supabaseService->fetchTable('checkins', $filters);
            }

            Log::info('Checkins Fetched for Popular Spots', [
                'filter' => $filter,
                'filters' => $filters,
                'checkins_count' => count($checkins)
            ]);

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

            $touristSpots = $this->supabaseService->fetchTable('tourist_spots');
            if ($all) {
                $data = [];
                foreach ($touristSpots as $spot) {
                    $spotId = $spot['spot_id'];
                    $visits = $spotVisits[$spotId] ?? 0;
                    if ($visits > 0) {
                        $data[] = ['spot' => $spot['name'], 'visits' => $visits];
                    }
                }
                usort($data, fn($a, $b) => $b['visits'] <=> $a['visits']);
                $total = count($data);
                $paginatedData = array_slice($data, ($page - 1) * $perPage, $perPage);
                return [
                    'data' => $paginatedData,
                    'total' => $total,
                    'page' => $page,
                    'per_page' => $perPage
                ];
            } else {
                arsort($spotVisits);
                $topSpotIds = array_slice(array_keys($spotVisits), 0, 4);
                $data = [];
                foreach ($topSpotIds as $id) {
                    $index = array_search($id, array_column($touristSpots, 'spot_id'));
                    $name = $index !== false ? $touristSpots[$index]['name'] : 'Unknown';
                    $data[] = ['spot' => $name, 'visits' => $spotVisits[$id] ?? 0];
                }
                return $data;
            }
        });

        return response()->json($data);
    }

    private function getDateFilter($filter)
    {
        $now = Carbon::now('Asia/Manila');
        $filters = null;

        switch ($filter) {
            case 'today':
                $start = $now->copy()->startOfDay()->format('Y-m-d\TH:i:s');
                $end = $now->copy()->endOfDay()->format('Y-m-d\TH:i:s');
                $filters = [
                    'and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"
                ];
                break;
            case 'this_week':
                $start = $now->copy()->startOfWeek()->startOfDay()->format('Y-m-d\TH:i:s');
                $end = $now->copy()->endOfWeek()->endOfDay()->format('Y-m-d\TH:i:s');
                $filters = [
                    'and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"
                ];
                break;
            case 'this_month':
                $start = $now->copy()->startOfMonth()->startOfDay()->format('Y-m-d\TH:i:s');
                $end = $now->copy()->endOfMonth()->endOfDay()->format('Y-m-d\TH:i:s');
                $filters = [
                    'and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"
                ];
                break;
            case 'this_year':
                $start = $now->copy()->startOfYear()->startOfDay()->format('Y-m-d\TH:i:s');
                $end = $now->copy()->endOfYear()->endOfDay()->format('Y-m-d\TH:i:s');
                $filters = [
                    'and' => "(timestamp.gte.{$start},timestamp.lte.{$end})"
                ];
                break;
            case 'all_time':
                $filters = null;
                break;
            default:
                $filters = null;
        }

        Log::info('Applied Date Filter', [
            'filter' => $filter,
            'filters' => $filters,
            'start' => $filters && isset($filters['and']) ? explode(',', str_replace(['(timestamp.gte.', 'timestamp.lte.', ')'], '', $filters['and']))[0] ?? null : null,
            'end' => $filters && isset($filters['and']) ? explode(',', str_replace(['(timestamp.gte.', 'timestamp.lte.', ')'], '', $filters['and']))[1] ?? null : null
        ]);

        return $filters;
    }

    public function getTouristActivities(Request $request)
    {
        $period = $request->query('period', 'this_week');
        $now = Carbon::now('Asia/Manila');

        if ($period === 'custom_year') {
            $year = $request->query('year');
            if (!$year || !is_numeric($year) || strlen($year) != 4) {
                return response()->json(['error' => 'Invalid year'], 200);
            }
            $minMaxYears = $this->supabaseService->getMinMaxYears();
            $minYear = $minMaxYears['min'] ?? 1900;
            $maxYear = $minMaxYears['max'] ?? now('Asia/Manila')->year;
            if ($year < $minYear || $year > $maxYear) {
                return response()->json(['error' => 'Year out of range'], 200);
            }
            $startDate = Carbon::createFromDate($year, 1, 1, 'Asia/Manila')->startOfDay();
            $endDate = Carbon::createFromDate($year, 12, 31, 'Asia/Manila')->endOfDay();
            $filters = [
                'and' => "(timestamp.gte.{$startDate->format('Y-m-d\TH:i:s')},timestamp.lte.{$endDate->format('Y-m-d\TH:i:s')})"
            ];
            $checkins = $this->supabaseService->fetchTable('checkins', $filters);
            $incidents = $this->supabaseService->fetchTable('emergency_reports', $filters);
            $periodLabel = "Year $year";
            $groupBy = 'month';
        } elseif ($period === 'all_time') {
            $filters = [];
            $checkins = $this->supabaseService->fetchTable('checkins', $filters);
            $incidents = $this->supabaseService->fetchTable('emergency_reports', $filters);
            
            $checkinsByYearMonth = [];
            $incidentsByYearMonth = [];

            foreach ($checkins as $checkin) {
                $date = Carbon::parse($checkin['timestamp'], 'Asia/Manila');
                $yearMonth = $date->format('Y-m');
                $checkinsByYearMonth[$yearMonth] = ($checkinsByYearMonth[$yearMonth] ?? 0) + 1;
            }

            foreach ($incidents as $incident) {
                $date = Carbon::parse($incident['timestamp'], 'Asia/Manila');
                $yearMonth = $date->format('Y-m');
                $incidentsByYearMonth[$yearMonth] = ($incidentsByYearMonth[$yearMonth] ?? 0) + 1;
            }

            $allYearMonths = array_unique(array_merge(array_keys($checkinsByYearMonth), array_keys($incidentsByYearMonth)));
            sort($allYearMonths);

            $labels = array_map(function($ym) {
                return Carbon::createFromFormat('Y-m', $ym, 'Asia/Manila')->format('M Y');
            }, $allYearMonths);

            $checkinsData = array_map(function($ym) use ($checkinsByYearMonth) {
                return $checkinsByYearMonth[$ym] ?? 0;
            }, $allYearMonths);

            $incidentsData = array_map(function($ym) use ($incidentsByYearMonth) {
                return $incidentsByYearMonth[$ym] ?? 0;
            }, $allYearMonths);

            $totalActivities = array_sum($checkinsData);
            $totalIncidents = array_sum($incidentsData);
            $periodLabel = 'All Time';
        } elseif ($period === 'all_time_by_month') {
            $filters = [];
            $checkins = $this->supabaseService->fetchTable('checkins', $filters);
            $incidents = $this->supabaseService->fetchTable('emergency_reports', $filters);

            $checkinsByMonth = array_fill(1, 12, 0);
            $incidentsByMonth = array_fill(1, 12, 0);

            foreach ($checkins as $checkin) {
                $month = Carbon::parse($checkin['timestamp'], 'Asia/Manila')->month;
                $checkinsByMonth[$month]++;
            }

            foreach ($incidents as $incident) {
                $month = Carbon::parse($incident['timestamp'], 'Asia/Manila')->month;
                $incidentsByMonth[$month]++;
            }

            $labels = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];

            $checkinsData = array_values($checkinsByMonth);
            $incidentsData = array_values($incidentsByMonth);

            $totalActivities = array_sum($checkinsData);
            $totalIncidents = array_sum($incidentsData);
            $periodLabel = 'All Time (By Month)';
        } else {
            if ($period === 'this_week') {
                $startDate = $now->copy()->startOfWeek()->startOfDay();
                $endDate = $now->copy()->endOfWeek()->endOfDay();
                $periodLabel = 'This Week';
                $groupBy = 'day';
            } elseif ($period === 'this_month') {
                $startDate = $now->copy()->startOfMonth()->startOfDay();
                $endDate = $now->copy()->endOfMonth()->endOfDay();
                $periodLabel = 'This Month';
                $groupBy = 'week';
            } elseif ($period === 'this_year') {
                $startDate = $now->copy()->startOfYear()->startOfDay();
                $endDate = $now->copy()->endOfYear()->endOfDay();
                $periodLabel = 'This Year';
                $groupBy = 'month';
            } else {
                return response()->json(['error' => 'Invalid period'], 400);
            }
            $filters = [
                'and' => "(timestamp.gte.{$startDate->format('Y-m-d\TH:i:s')},timestamp.lte.{$endDate->format('Y-m-d\TH:i:s')})"
            ];
            $checkins = $this->supabaseService->fetchTable('checkins', $filters);
            $incidents = $this->supabaseService->fetchTable('emergency_reports', $filters);
        }

        Log::info('Tourist Activities Data', [
            'period' => $period,
            'filters' => $filters ?? 'No filters',
            'checkins_count' => count($checkins),
            'incidents_count' => count($incidents)
        ]);

        if (isset($groupBy) && $groupBy === 'day') {
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
        } elseif (isset($groupBy) && $groupBy === 'week') {
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
        } elseif (isset($groupBy) && $groupBy === 'month') {
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
        $totalIncidents = array_sum($incidentsData);

        return response()->json([
            'labels' => $labels,
            'checkins' => $checkinsData,
            'incidents' => $incidentsData,
            'totalActivities' => $totalActivities,
            'totalIncidents' => $totalIncidents,
            'period' => $periodLabel
        ]);
    }
}