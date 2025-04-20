<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseService
{
    protected $url;
    protected $key;

    public function __construct()
    {
        $this->url = env('SUPABASE_URL');
        $this->key = env('SUPABASE_API_KEY');
    }

    public function fetchTable($table, $filters = [])
    {
        $query = Http::withHeaders([
            'apikey' => $this->key,
            'Authorization' => 'Bearer ' . $this->key
        ])
        ->get("{$this->url}/rest/v1/{$table}", $filters);

        return $query->successful() ? $query->json() : null;
    }
}
