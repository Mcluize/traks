<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

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
        \Log::info("Final Supabase URL: {$this->url}/rest/v1/{$table}", ['filters' => $filters, 'select' => $select]);

        $headers = [
            'apikey' => $this->key,
            'Authorization' => 'Bearer ' . $this->key
        ];

        if ($count) {
            $headers['Prefer'] = 'count=exact';
        }

        // Add status filter for warning_zones
        if ($table === 'warning_zones' && !isset($filters['status'])) {
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
            return $query->json();
        } else {
            \Log::error('Supabase fetchTable failed', [
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
            \Log::info("Connecting to Supabase: {$this->url}/rest/v1/{$table}");
            
            // Set default status for warning_zones
            if ($table === 'warning_zones') {
                $data['status'] = 'active';
            }

            $response = Http::withHeaders([
                'apikey' => $this->key,
                'Authorization' => 'Bearer ' . $this->key,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation'
            ])->post("{$this->url}/rest/v1/{$table}", $data);
            
            \Log::info('Supabase response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            throw new \Exception('Supabase error: ' . $response->body());
        } catch (\Exception $e) {
            \Log::error('Supabase connection error', [
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
            \Log::error('Supabase update error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function fetchMinMax($table, $column)
    {
        $headers = [
            'apikey' => $this->key,
            'Authorization' => 'Bearer ' . $this->key
        ];

        // Fetch earliest timestamp
        $minQuery = Http::withHeaders($headers)
            ->get("{$this->url}/rest/v1/{$table}", [
                'select' => $column,
                'order' => "$column.asc",
                'limit' => 1
            ]);

        // Fetch latest timestamp
        $maxQuery = Http::withHeaders($headers)
            ->get("{$this->url}/rest/v1/{$table}", [
                'select' => $column,
                'order' => "$column.desc",
                'limit' => 1
            ]);

        if ($minQuery->successful() && $maxQuery->successful()) {
            $minResult = $minQuery->json();
            $maxResult = $maxQuery->json();
            \Log::info('fetchMinMax min result', ['minResult' => $minResult]);
            \Log::info('fetchMinMax max result', ['maxResult' => $maxResult]);
            if (!empty($minResult) && !empty($maxResult)) {
                return [
                    'min' => $minResult[0][$column] ?? null,
                    'max' => $maxResult[0][$column] ?? null
                ];
            }
        } else {
            \Log::error('Supabase fetchMinMax failed', [
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
        $now = Carbon::now();
        $filters = [
            'select' => 'user_id, latitude, longitude, timestamp, status' 
        ];

        if (strpos($filter, 'custom_year:') === 0) {
            $year = substr($filter, strlen('custom_year:'));
            if (is_numeric($year) && strlen($year) == 4) {
                $localStart = Carbon::createFromDate($year, 1, 1, 'Asia/Manila');
                $localEnd = $localStart->copy()->addYear();
                $start = $localStart->toIso8601String();
                $end = $localEnd->toIso8601String();
                $filters['and'] = "(timestamp.gte.{$start},timestamp.lt.{$end})";
            } else {
                return response()->json(['error' => 'Invalid year'], 400);
            }
        } else {
            switch ($filter) {
                case 'today':
                    $localStart = Carbon::today();
                    $localEnd = $localStart->copy()->addDay();
                    $start = $localStart->toIso8601String();
                    $end = $localEnd->toIso8601String();
                    $filters['and'] = "(timestamp.gte.{$start},timestamp.lt.{$end})";
                    break;

                case 'this_week':
                    $localStart = $now->copy()->startOfWeek();
                    $localEnd = $localStart->copy()->addWeek();
                    $start = $localStart->toIso8601String();
                    $end = $localEnd->toIso8601String();
                    $filters['and'] = "(timestamp.gte.{$start},timestamp.lt.{$end})";
                    break;

                case 'this_month':
                    $localStart = $now->copy()->startOfMonth();
                    $localEnd = $localStart->copy()->addMonth();
                    $start = $localStart->toIso8601String();
                    $end = $localEnd->toIso8601String();
                    $filters['and'] = "(timestamp.gte.{$start},timestamp.lt.{$end})";
                    break;

                case 'this_year':
                    $localStart = $now->copy()->startOfYear();
                    $localEnd = $now->copy()->addYear()->startOfYear();
                    $start = $localStart->toIso8601String();
                    $end = $localEnd->toIso8601String();
                    $filters['and'] = "(timestamp.gte.{$start},timestamp.lt.{$end})";
                    break;

                case 'all_time':
                    break;

                default:
                    return response()->json(['error' => 'Invalid filter'], 400);
            }
        }

        $incidents = $this->fetchTable('emergency_reports', $filters, false);
        if ($incidents === null) {
            return response()->json(['error' => 'Failed to fetch data'], 500);
        }

        foreach ($incidents as &$incident) {
            $incident['timestamp'] = Carbon::parse($incident['timestamp'])->format('Y-m-d H:i:s');
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
            $minYear = Carbon::parse($checkinsRange['min'], 'Asia/Manila')->year;
        }
        if ($incidentsRange['min'] && (!$minYear || Carbon::parse($incidentsRange['min'], 'Asia/Manila')->year < $minYear)) {
            $minYear = Carbon::parse($incidentsRange['min'], 'Asia/Manila')->year;
        }

        if ($checkinsRange['max']) {
            $maxYear = Carbon::parse($checkinsRange['max'], 'Asia/Manila')->year;
        }
        if ($incidentsRange['max'] && (!$maxYear || Carbon::parse($incidentsRange['max'], 'Asia/Manila')->year > $maxYear)) {
            $maxYear = Carbon::parse($incidentsRange['max'], 'Asia/Manila')->year;
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
}