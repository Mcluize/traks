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

    public function fetchTable($table, $filters = [], $count = false)
    {
        \Log::info("Final Supabase URL: {$this->url}/rest/v1/{$table}");

        $headers = [
            'apikey' => $this->key,
            'Authorization' => 'Bearer ' . $this->key
        ];

        if ($count) {
            $headers['Prefer'] = 'count=exact';
        }

        $query = Http::withHeaders($headers)
            ->get("{$this->url}/rest/v1/{$table}", $filters);

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
        }
        return null;
    }
    
    public function insertIntoTable($table, $data)
    {
        try {
            \Log::info("Connecting to Supabase: {$this->url}/rest/v1/{$table}");
            
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
    public function getIncidentReports($filter)
{
    $now = Carbon::now();
    $filters = [];

    switch ($filter) {
        case 'today':
            $localStart = Carbon::today(); // Start of today in local timezone
            $localEnd = $localStart->copy()->addDay(); // Start of tomorrow
            $start = $localStart->toIso8601String();
            $end = $localEnd->toIso8601String();
            break;

        case 'this_week':
            $localStart = $now->copy()->startOfWeek(); // Monday of current week
            $localEnd = $localStart->copy()->addWeek(); // Monday of next week
            $start = $localStart->toIso8601String();
            $end = $localEnd->toIso8601String();
            break;

        case 'this_month':
            $localStart = $now->copy()->startOfMonth(); // First day of current month
            $localEnd = $localStart->copy()->addMonth(); // First day of next month
            $start = $localStart->toIso8601String();
            $end = $localEnd->toIso8601String();
            break;

        case 'all_time':
            $start = null;
            $end = null;
            break;

        default:
            return response()->json(['error' => 'Invalid filter'], 400);
    }

    if ($start && $end) {
        $filters = [
            'and' => "(timestamp.gte.{$start},timestamp.lt.{$end})",
        ];
    }

    $count = $this->fetchTable('emergency_reports', $filters, true);
    if ($count === null) {
        return response()->json(['error' => 'Failed to fetch data'], 500);
    }
    return response()->json(['count' => $count]);
}
}