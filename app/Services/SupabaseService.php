<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseService
{
    protected $url;
    protected $key;

    public function __construct()
    {
        $this->url = config('services.supabase.url');
        $this->key = config('services.supabase.key');
    }

    public function fetchTable($table, $filters = [])
    {
        \Log::info("Final Supabase URL: {$this->url}/rest/v1/{$table}");

        $query = Http::withHeaders([
            'apikey' => $this->key,
            'Authorization' => 'Bearer ' . $this->key
        ])
        ->get("{$this->url}/rest/v1/{$table}", $filters);

        return $query->successful() ? $query->json() : null;
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

    public function updateTable($table, $userId, $data)
{
    try {
        $response = Http::withHeaders([
            'apikey' => $this->key,
            'Authorization' => 'Bearer ' . $this->key,
            'Content-Type' => 'application/json',
            'Prefer' => 'return=representation'
        ])->patch("{$this->url}/rest/v1/{$table}?user_id=eq.{$userId}", $data);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Supabase error: ' . $response->status() . ' - ' . $response->body());
    } catch (\Exception $e) {
        \Log::error('Supabase update error', ['error' => $e->getMessage()]);
        throw $e;
    }
}

}