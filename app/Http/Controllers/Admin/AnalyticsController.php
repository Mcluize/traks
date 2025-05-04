<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        // Count active admin accounts (excluding locked ones)
        $adminAccounts = count(array_filter($users, fn($user) => 
            in_array($user['user_type'], ['admin', 'super_admin']) && 
            ($user['status'] ?? '') !== 'locked'
        ));

        // Count active admin accounts from the last month (excluding locked ones)
        $adminAccountsLastMonth = count(array_filter($users, fn($user) => 
            Carbon::parse($user['created_at'], 'Asia/Manila')->gte(now('Asia/Manila')->subMonth()) && 
            in_array($user['user_type'], ['admin', 'super_admin']) && 
            ($user['status'] ?? '') !== 'locked'
        ));

        $adminAccountsChange = $adminAccountsLastMonth ? round((($adminAccounts - $adminAccountsLastMonth) / $adminAccountsLastMonth) * 100) : 0;

        // Incident Status
        $incidentStatus = $this->supabaseService->fetchTable('emergency_reports');
        $incidentStatusCounts = array_count_values(array_column($incidentStatus, 'status'));
        $incidentStatusLabels = array_keys($incidentStatusCounts);
        $incidentStatusData = array_values($incidentStatusCounts);

        // Weekly Analytics
        $weeklyCheckins = $this->supabaseService->fetchTable('checkins', ['timestamp' => 'gte.' . now('Asia/Manila')->startOfWeek()->toIso8601String()]);
        $weeklyIncidents = $this->supabaseService->fetchTable('emergency_reports', ['timestamp' => 'gte.' . now('Asia/Manila')->startOfWeek()->toIso8601String()]);
        $weeklyLabels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $weeklyCheckinsData = array_fill(0, 7, 0);
        $weeklyIncidentsData = array_fill(0, 7, 0);

        foreach ($weeklyCheckins as $checkin) {
            $day = Carbon::parse($checkin['timestamp'], 'Asia/Manila')->format('l');
            $index = array_search($day, $weeklyLabels);
            if ($index !== false) $weeklyCheckinsData[$index]++;
        }

        foreach ($weeklyIncidents as $incident) {
            $day = Carbon::parse($incident['timestamp'], 'Asia/Manila')->format('l');
            $index = array_search($day, $weeklyLabels);
            if ($index !== false) $weeklyIncidentsData[$index]++;
        }

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
        $popularSpots = array_slice($spotVisits, 0, 5, true);
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

        // Total Activities
        $totalActivities = array_sum($weeklyCheckinsData);

        // Define change classes and icons
        $touristChangeClass = $touristChange > 0 ? 'positive' : ($touristChange < 0 ? 'negative' : 'neutral');
        $touristChangeIcon = $touristChange > 0 ? 'fa-arrow-up' : ($touristChange < 0 ? 'fa-arrow-down' : 'fa-equals');

        $incidentChangeClass = $incidentChange > 0 ? 'positive' : ($incidentChange < 0 ? 'negative' : 'neutral');
        $incidentChangeIcon = $incidentChange > 0 ? 'fa-arrow-up' : ($incidentChange < 0 ? 'fa-arrow-down' : 'fa-equals');

        $touristAccountsChangeClass = $touristAccountsChange > 0 ? 'positive' : ($touristAccountsChange < 0 ? 'negative' : 'neutral');
        $touristAccountsChangeIcon = $touristAccountsChange > 0 ? 'fa-arrow-up' : ($touristAccountsChange < 0 ? 'fa-arrow-down' : 'fa-equals');

        $adminAccountsChangeClass = $adminAccountsChange > 0 ? 'positive' : ($adminAccountsChange < 0 ? 'negative' : 'neutral');
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
        $dateFilter = now('Asia/Manila')->subMonth();
        if ($period === 'week') $dateFilter = now('Asia/Manila')->subWeek();
        elseif ($period === 'year') $dateFilter = now('Asia/Manila')->subYear();

        $incidentStatus = $this->supabaseService->fetchTable('emergency_reports', ['timestamp' => 'gte.' . $dateFilter->toIso8601String()]);
        $incidentStatusCounts = array_count_values(array_column($incidentStatus, 'status'));
        $incidentStatusLabels = array_keys($incidentStatusCounts);
        $incidentStatusData = array_values($incidentStatusCounts);

        return response()->json([
            'labels' => $incidentStatusLabels,
            'values' => $incidentStatusData
        ]);
    }

    public function getPopularSpots($filter)
    {
        $dateFilter = $this->getDateFilter($filter);
        $filters = [];
        if (is_array($dateFilter)) {
            $filters = [
                'timestamp' => 'gte.' . $dateFilter['gte'],
                'timestamp' => 'lt.' . $dateFilter['lt']
            ];
        } elseif ($dateFilter) {
            $filters = ['timestamp' => 'gte.' . $dateFilter->toIso8601String()];
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

        $touristSpots = $this->supabaseService->fetchTable('tourist_spots');
        $data = array_map(function($id) use ($spotVisits, $touristSpots) {
            $name = $touristSpots[array_search($id, array_column($touristSpots, 'spot_id'))]['name'] ?? 'Unknown';
            return ['spot' => $name, 'visits' => $spotVisits[$id] ?? 0];
        }, array_keys($spotVisits));
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
                return $now->startOfWeek();
            case 'this_month':
                return $now->startOfMonth();
            case 'all_time':
                return null;
            default:
                return $now->subMonth();
        }
    }
}