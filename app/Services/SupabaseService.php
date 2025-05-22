<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Log;

class SupabaseService
{
    protected $url;
    protected $key;

    public function __construct()
    {
        $this->url = config('services.supabase.url');
        $this->key = config('services.supabase.key');
    }

    public function fetchTable($table, $filters = [], $count = false, $select = '*')
    {
        Log::info("Supabase fetchTable for {$table}", ['filters' => $filters, 'select' => $select]);

        $headers = [
            'apikey' => $this->key,
            'Authorization' => 'Bearer ' . $this->key
        ];

        if ($count) {
            $headers['Prefer'] = 'count=exact';
        }

        if ($table === 'warning_zones' && !isset($filters['status']) && !isset($filters['and'])) {
            $filters['status'] = 'eq.active';
        }

        $queryParams = array_merge(['select' => $select], $filters);

        $query = Http::withHeaders($headers)
            ->get("{$this->url}/rest/v1/{$table}", $queryParams);

        if ($query->successful()) {
            if ($count) {
                $contentRange = $query->header('Content-Range');
                if ($contentRange) {
                    [, $total] = explode('/', $contentRange);
                    return (int) $total;
                }
                return 0;
            }
            $result = $query->json();
            Log::info("Supabase fetchTable result count for {$table}", ['count' => count($result)]);
            return $result;
        } else {
            Log::error('Supabase fetchTable failed', [
                'table' => $table,
                'status' => $query->status(),
                'body' => $query->body(),
                'headers' => $query->headers()
            ]);
            return $count ? 0 : [];
        }
    }

    public function insertIntoTable($table, $data)
    {
        try {
            Log::info("Connecting to Supabase: {$this->url}/rest/v1/{$table}");

            if ($table === 'warning_zones') {
                $data['status'] = 'active';
            }

            $response = Http::withHeaders([
                'apikey' => $this->key,
                'Authorization' => 'Bearer ' . $this->key,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation'
            ])->post("{$this->url}/rest/v1/{$table}", $data);

            Log::info('Supabase response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Supabase error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Supabase connection error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function updateTable($table, $column, $value, $data)
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->key,
                'Authorization' => 'Bearer ' . $this->key,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation'
            ])->patch("{$this->url}/rest/v1/{$table}?{$column}=eq.{$value}", $data);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Supabase error: ' . $response->status() . ' - ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Supabase update error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function fetchMinMax($table, $column)
    {
        $headers = [
            'apikey' => $this->key,
            'Authorization' => 'Bearer ' . $this->key
        ];

        $minQuery = Http::withHeaders($headers)
            ->get("{$this->url}/rest/v1/{$table}", [
                'select' => $column,
                'order' => "$column.asc",
                'limit' => 1
            ]);

        $maxQuery = Http::withHeaders($headers)
            ->get("{$this->url}/rest/v1/{$table}", [
                'select' => $column,
                'order' => "$column.desc",
                'limit' => 1
            ]);

        if ($minQuery->successful() && $maxQuery->successful()) {
            $minResult = $minQuery->json();
            $maxResult = $maxQuery->json();
            Log::info('fetchMinMax min result', ['minResult' => $minResult]);
            Log::info('fetchMinMax max result', ['maxResult' => $maxResult]);
            if (!empty($minResult) && !empty($maxResult)) {
                return [
                    'min' => $minResult[0][$column] ?? null,
                    'max' => $maxResult[0][$column] ?? null
                ];
            }
        } else {
            Log::error('Supabase fetchMinMax failed', [
                'minStatus' => $minQuery->status(),
                'maxStatus' => $maxQuery->status(),
                'minBody' => $minQuery->body(),
                'maxBody' => $maxQuery->body()
            ]);
        }
        return ['min' => null, 'max' => null];
    }

    public function getIncidentReports($filter)
    {
        $now = date('Y-m-d H:i:s');
        $todayDate = date('Y-m-d');
        $filters = [
            'select' => 'user_id, latitude, longitude, timestamp, status'
        ];

        if (strpos($filter, 'custom_year:') === 0) {
            $year = substr($filter, strlen('custom_year:'));
            if (is_numeric($year) && strlen($year) == 4) {
                $start = "$year-01-01T00:00:00";
                $end = "$year-12-31T23:59:59";
                $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
            } else {
                return response()->json(['error' => 'Invalid year'], 400);
            }
        } else {
            switch ($filter) {
                case 'today':
                    $start = "$todayDate 00:00:00";
                    $end = "$todayDate 23:59:59";
                    $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                    break;
                case 'this_week':
                    $startOfWeek = date('Y-m-d', strtotime('monday this week'));
                    $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
                    $start = "$startOfWeek 00:00:00";
                    $end = "$endOfWeek 23:59:59";
                    $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                    break;
                case 'this_month':
                    $startOfMonth = date('Y-m-01');
                    $endOfMonth = date('Y-m-t');
                    $start = "$startOfMonth 00:00:00";
                    $end = "$endOfMonth 23:59:59";
                    $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                    break;
                case 'this_year':
                    $year = date('Y');
                    $start = "$year-01-01 00:00:00";
                    $end = "$year-12-31 23:59:59";
                    $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
                    break;
                case 'all_time':
                    break;
                default:
                    return response()->json(['error' => 'Invalid filter'], 400);
            }
        }

        Log::info("Incident report filters for {$filter}", [
            'start' => $filters['and'] ?? 'all_time',
            'filter' => $filter
        ]);

        $incidents = $this->fetchTable('emergency_reports', $filters, false);
        if ($incidents === null) {
            return response()->json(['error' => 'Failed to fetch data'], 500);
        }

        return response()->json([
            'count' => count($incidents),
            'incidents' => $incidents
        ]);
    }

    public function getMinMaxYears()
    {
        $checkinsRange = $this->fetchMinMax('checkins', 'timestamp');
        $incidentsRange = $this->fetchMinMax('emergency_reports', 'timestamp');

        $minYear = null;
        $maxYear = null;

        if ($checkinsRange['min']) {
            $minYear = date('Y', strtotime($checkinsRange['min']));
        }
        if ($incidentsRange['min'] && (!$minYear || date('Y', strtotime($incidentsRange['min'])) < $minYear)) {
            $minYear = date('Y', strtotime($incidentsRange['min']));
        }

        if ($checkinsRange['max']) {
            $maxYear = date('Y', strtotime($checkinsRange['max']));
        }
        if ($incidentsRange['max'] && (!$maxYear || date('Y', strtotime($incidentsRange['max'])) > $maxYear)) {
            $maxYear = date('Y', strtotime($incidentsRange['max']));
        }

        return [
            'min' => $minYear,
            'max' => $maxYear
        ];
    }

    public function fetchUserZones()
    {
        return $this->fetchTable('user_zones', [], false, 'zone_id, user_id, type, description, latitude, longitude, total_weight, status, created_at');
    }

    public function updateUserZoneStatus($zoneId, $status)
    {
        return $this->updateTable('user_zones', 'zone_id', $zoneId, ['status' => $status]);
    }

    public function fetchCheckins($touristId, $filter)
    {
        $filters = [
            'select' => 'timestamp, tourist_spots:spot_id (spot_id, name, latitude, longitude)',
            'tourist_id' => "eq.$touristId",
            'order' => 'timestamp.asc'
        ];

        $now = new DateTime('now', new DateTimeZone('UTC'));
        Log::info("Current server date in fetchCheckins: " . $now->format('Y-m-d H:i:s'));

        if ($filter === 'today') {
            $start = $now->format('Y-m-d 00:00:00');
            $end = $now->format('Y-m-d 23:59:59');
            $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
        } elseif ($filter === 'this_week') {
            $dayOfWeek = $now->format('w');
            $daysToMonday = ($dayOfWeek == 0) ? 6 : $dayOfWeek - 1;
            $startOfWeek = (clone $now)->modify("-{$daysToMonday} days");
            $endOfWeek = (clone $startOfWeek)->modify('+6 days');
            $start = $startOfWeek->format('Y-m-d 00:00:00');
            $end = $endOfWeek->format('Y-m-d 23:59:59');
            $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
        } elseif ($filter === 'this_month') {
            $start = $now->format('Y-m-01 00:00:00');
            $end = $now->format('Y-m-t 23:59:59');
            $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
        } elseif ($filter === 'this_year') {
            $year = $now->format('Y');
            $start = "$year-01-01 00:00:00";
            $end = "$year-12-31 23:59:59";
            $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
        } elseif ($filter === 'custom_year' && isset($_GET['year']) && is_numeric($_GET['year'])) {
            $year = (int)$_GET['year'];
            $start = "$year-01-01 00:00:00";
            $end = "$year-12-31 23:59:59";
            $filters['and'] = "(timestamp.gte.{$start},timestamp.lte.{$end})";
        }

        Log::info("Check-in filter range for {$filter}", [
            'start' => $start ?? 'N/A',
            'end' => $end ?? 'N/A',
            'tourist_id' => $touristId
        ]);

        $checkins = $this->fetchTable('checkins', $filters, false);
        if ($checkins === null) {
            return response()->json(['error' => 'Failed to fetch check-ins'], 500);
        }

        return response()->json([
            'count' => count($checkins),
            'checkins' => $checkins
        ]);
    }

    public function fetchZoneVoters($zoneId)
    {
        $filters = [
            'zone_id' => "eq.$zoneId",
            'select' => 'user_id, trust_score, created_at',
            'order' => 'created_at.asc'
        ];
        $voters = $this->fetchTable('user_zone_reports', $filters, false);
        if ($voters === null) {
            return response()->json(['error' => 'Failed to fetch voters'], 500);
        }
        return response()->json(['voters' => $voters]);
    }

    public function getDecryptedUser($userId)
{
    $userId = (int) $userId;
    $rows = $this->fetchTable('users', ['user_id' => "eq.$userId"]);

    if (empty($rows)) {
        \Log::warning("No user found with ID $userId");
        return null;
    }

    $user = $rows[0];

    try {
        // Laravel decryption attempt
        $user['full_name'] = \Crypt::decryptString($user['full_name']);
        $user['contact_details'] = \Crypt::decryptString($user['contact_details']);
        if (!empty($user['address'])) {
            $user['address'] = \Crypt::decryptString($user['address']);
        }

        \Log::info("Laravel decryption succeeded for user ID $userId");
        return [$user];
    } catch (\Exception $laravelException) {
        \Log::warning("Laravel decryption failed for user ID $userId", [
            'error' => $laravelException->getMessage()
        ]);

        // Supabase RPC fallback
        $headers = [
            'apikey' => $this->key,
            'Authorization' => 'Bearer ' . $this->key,
            'Content-Type' => 'application/json',
        ];

        $response = \Http::withHeaders($headers)
            ->post("{$this->url}/rest/v1/rpc/get_decrypted_user", [
                'p_user_id' => $userId
            ]);

        if ($response->successful()) {
            $fallback = $response->json();
            if (!empty($fallback)) {
                \Log::info("Supabase fallback decryption succeeded for user ID $userId");
                return $fallback;
            }
        }

        \Log::error("All decryption methods failed for user ID $userId", [
            'status' => $response->status() ?? 'n/a',
            'body' => $response->body() ?? 'empty',
        ]);
        return null;
    }
}
}