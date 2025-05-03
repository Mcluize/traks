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
        $checkins = $this->supabaseService->fetchTable('checkins');
        $touristArrivals = count(array_unique(array_column($checkins, 'tourist_id')));
        $checkinsLastWeek = $this->supabaseService->fetchTable('checkins', ['timestamp' => 'gte.' . now()->subWeek()->toIso8601String()]);
        $touristArrivalsLastWeek = count(array_unique(array_column($checkinsLastWeek, 'tourist_id')));
        $touristChange = $touristArrivalsLastWeek ? round((($touristArrivals - $touristArrivalsLastWeek) / $touristArrivalsLastWeek) * 100) : 0;

        $incidentReports = $this->supabaseService->fetchTable('emergency_reports', [], true);
        $incidentReportsLastWeek = $this->supabaseService->fetchTable('emergency_reports', ['timestamp' => 'gte.' . now()->subWeek()->toIso8601String()], true);
        $incidentChange = $incidentReportsLastWeek ? round((($incidentReports - $incidentReportsLastWeek) / $incidentReportsLastWeek) * 100) : 0;

        $users = $this->supabaseService->fetchTable('users');
        $touristAccounts = count(array_filter($users, fn($user) => $user['user_type'] === 'user'));
        $touristAccountsLastMonth = count(array_filter($users, fn($user) => Carbon::parse($user['created_at'])->gte(now()->subMonth()) && $user['user_type'] === 'user'));
        $touristAccountsChange = $touristAccountsLastMonth ? round((($touristAccounts - $touristAccountsLastMonth) / $touristAccountsLastMonth) * 100) : 0;

        $adminAccounts = count(array_filter($users, fn($user) => in_array($user['user_type'], ['admin', 'super_admin'])));
        $adminAccountsLastMonth = count(array_filter($users, fn($user) => Carbon::parse($user['created_at'])->gte(now()->subMonth()) && in_array($user['user_type'], ['admin', 'super_admin'])));
        $adminAccountsChange = $adminAccountsLastMonth ? round((($adminAccounts - $adminAccountsLastMonth) / $adminAccountsLastMonth) * 100) : 0;

        // Incident Status
        $incidentStatus = $this->supabaseService->fetchTable('emergency_reports');
        $incidentStatusCounts = array_count_values(array_column($incidentStatus, 'status'));
        $incidentStatusLabels = array_keys($incidentStatusCounts);
        $incidentStatusData = array_values($incidentStatusCounts);

        // Weekly Analytics
        $weeklyCheckins = $this->supabaseService->fetchTable('checkins', ['timestamp' => 'gte.' . now()->startOfWeek()->toIso8601String()]);
        $weeklyIncidents = $this->supabaseService->fetchTable('emergency_reports', ['timestamp' => 'gte.' . now()->startOfWeek()->toIso8601String()]);
        $weeklyLabels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $weeklyCheckinsData = array_fill(0, 7, 0);
        $weeklyIncidentsData = array_fill(0, 7, 0);

        foreach ($weeklyCheckins as $checkin) {
            $day = Carbon::parse($checkin['timestamp'])->format('l');
            $index = array_search($day, $weeklyLabels);
            if ($index !== false) $weeklyCheckinsData[$index]++;
        }

        foreach ($weeklyIncidents as $incident) {
            $day = Carbon::parse($incident['timestamp'])->format('l');
            $index = array_search($day, $weeklyLabels);
            if ($index !== false) $weeklyIncidentsData[$index]++;
        }

        // User Growth
        $userGrowth = $this->supabaseService->fetchTable('users');
        $userGrowthByDate = [];
        foreach ($userGrowth as $user) {
            $date = Carbon::parse($user['created_at'])->format('Y-m-d');
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
            'touristSpots', 'incidents', 'checkins',
            'latestIncidents', 'totalActivities'
        ));
    }

    public function getIncidentStatus(Request $request)
    {
        $period = $request->query('period', 'month');
        $dateFilter = now()->subMonth();
        if ($period === 'week') $dateFilter = now()->subWeek();
        elseif ($period === 'year') $dateFilter = now()->subYear();

        $incidentStatus = $this->supabaseService->fetchTable('emergency_reports', ['timestamp' => 'gte.' . $dateFilter->toIso8601String()]);
        $incidentStatusCounts = array_count_values(array_column($incidentStatus, 'status'));
        $incidentStatusLabels = array_keys($incidentStatusCounts);
        $incidentStatusData = array_values($incidentStatusCounts);

        return response()->json([
            'labels' => $incidentStatusLabels,
            'values' => $incidentStatusData
        ]);
    }
}