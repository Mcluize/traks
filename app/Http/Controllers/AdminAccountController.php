<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AdminAccountController extends Controller
{
    // Handle Tourist Accounts Pagination
    public function manageTourists()
    {
        $client = new Client();
        $supabaseUrl = 'https://your_supabase_url';  // Your Supabase URL
        $supabaseKey = 'your_supabase_key';  // Your Supabase API key

        // Get page number from query params (default is page 1)
        $page = request()->get('page', 1);
        $limit = 10;  // Number of records per page

        // Calculate offset and limit for pagination
        $offset = ($page - 1) * $limit;

        // Make API request to Supabase (adjust this according to your API structure)
        $response = $client->request('GET', "$supabaseUrl/rest/v1/users", [
            'headers' => [
                'Authorization' => 'Bearer ' . $supabaseKey,
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'user_type' => 'user',  // Only get Tourist accounts
                'limit' => $limit,
                'offset' => $offset,
            ]
        ]);

        // Decode the response to get users data
        $users = json_decode($response->getBody()->getContents(), true);

        // Get the total number of users (for pagination)
        $totalUsersResponse = $client->request('GET', "$supabaseUrl/rest/v1/users", [
            'headers' => [
                'Authorization' => 'Bearer ' . $supabaseKey,
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'user_type' => 'user',  // Only get Tourist accounts
            ]
        ]);

        $totalUsers = count(json_decode($totalUsersResponse->getBody()->getContents(), true));

        // Return view with paginated users and total count
        return view('vendor.backpack.ui.manage-tourists', [
            'users' => collect($users),
            'totalUsers' => $totalUsers,
        ]);
    }

    // Handle Admin Accounts Pagination
    public function manageAdmins()
    {
        $client = new Client();
        $supabaseUrl = 'https://your_supabase_url';  // Your Supabase URL
        $supabaseKey = 'your_supabase_key';  // Your Supabase API key

        // Get page number from query params (default is page 1)
        $page = request()->get('page', 1);
        $limit = 10;  // Number of records per page

        // Calculate offset and limit for pagination
        $offset = ($page - 1) * $limit;

        // Make API request to Supabase (adjust this according to your API structure)
        $response = $client->request('GET', "$supabaseUrl/rest/v1/users", [
            'headers' => [
                'Authorization' => 'Bearer ' . $supabaseKey,
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'user_type' => 'admin',  // Only get Admin accounts
                'limit' => $limit,
                'offset' => $offset,
            ]
        ]);

        // Decode the response to get users data
        $users = json_decode($response->getBody()->getContents(), true);

        // Get the total number of users (for pagination)
        $totalUsersResponse = $client->request('GET', "$supabaseUrl/rest/v1/users", [
            'headers' => [
                'Authorization' => 'Bearer ' . $supabaseKey,
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'user_type' => 'admin',  // Only get Admin accounts
            ]
        ]);

        $totalUsers = count(json_decode($totalUsersResponse->getBody()->getContents(), true));

        // Return view with paginated users and total count
        return view('vendor.backpack.ui.manage-tourists', [
            'users' => collect($users),
            'totalUsers' => $totalUsers,
        ]);
    }
}
