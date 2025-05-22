<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SupabaseService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class AdminAccountController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'contact_details' => 'required|string|max:255|regex:/^\+63(9\d{9})$/',
            ], [
                'contact_details.regex' => 'The contact details must start with +63 followed by 10 digits.',
            ]);

            $users = $this->supabase->fetchTable('users');
            $adminUsers = array_filter($users, fn($user) => $user['user_type'] === 'admin');
            $maxAdminId = $adminUsers ? max(array_map(fn($user) => (float)$user['user_id'], $adminUsers)) : 0;
            $adminId = ceil($maxAdminId) + 1;

            $defaultPin = '1234';
            $salt = random_bytes(16);
            $saltString = rtrim(strtr(base64_encode($salt), '+/', '-_'), '=');
            $saltedPin = $defaultPin . $saltString;
            $pinHash = hash('sha256', $saltedPin);

            $encryptedFullName = Crypt::encryptString($validated['full_name']);
            $encryptedContactDetails = Crypt::encryptString($validated['contact_details']);
            $nameHash = hash('sha256', $validated['full_name']);

            $createdAt = now()->toDateTimeString();

            $adminData = [
                'user_id' => $adminId,
                'full_name' => $encryptedFullName,
                'contact_details' => $encryptedContactDetails,
                'name_hash' => $nameHash,
                'address' => null,
                'created_at' => $createdAt,
                'user_type' => 'admin',
                'pin_hash' => $pinHash . ':' . $saltString,
                'consent_preferences' => true,
                'encrypted_qr_code' => null,
                'onboarded' => false,
                'status' => 'active'
            ];

            Log::info('Attempting to create admin account', ['data' => $adminData]);
            $result = $this->supabase->insertIntoTable('users', $adminData);
            Log::info('Admin account created successfully', ['result' => $result]);

            $adminData['full_name'] = $validated['full_name'];
            $adminData['contact_details'] = $validated['contact_details'];

            return response()->json([
                'success' => true,
                'message' => 'Admin account created successfully',
                'user' => $adminData
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error when creating admin account', ['errors' => $e->errors()]);
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create admin account', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to create admin account: ' . $e->getMessage()], 500);
        }
    }

    public function manageTourists()
    {
        $users = $this->supabase->fetchTable('users');
        $touristSpots = $this->supabase->fetchTable('tourist_spots');

        $adminAddresses = [];
        foreach ($touristSpots as $spot) {
            if (isset($spot['created_by']) && !empty($spot['address'])) {
                $adminAddresses[$spot['created_by']] = $spot['address'];
            }
        }

        foreach ($users as &$user) {
            if ($user['user_type'] === 'admin' && isset($adminAddresses[$user['user_id']])) {
                $user['address'] = $adminAddresses[$user['user_id']];
            }
        }
        unset($user);

        usort($users, function($a, $b) {
            return $b['user_id'] <=> $a['user_id'];
        });

        $activeUsers = array_filter($users, fn($user) => $user['status'] !== 'locked');
        $totalAccounts = count($activeUsers);
        $touristAccounts = count(array_filter($activeUsers, fn($user) => $user['user_type'] === 'user'));
        $adminAccounts = count(array_filter($activeUsers, fn($user) => $user['user_type'] === 'admin'));

        Log::info("Total Active Accounts: $totalAccounts");
        Log::info("Tourist Accounts: $touristAccounts");
        Log::info("Admin Accounts: $adminAccounts");

        return view('vendor.backpack.ui.manage-tourists', compact('totalAccounts', 'touristAccounts', 'adminAccounts', 'users'));
    }

    public function lockAccount(Request $request, $userId)
    {
        try {
            Log::info('Attempting to lock account', ['user_id' => $userId]);
            $data = ['status' => 'locked'];
            $result = $this->supabase->updateTable('users', 'user_id', $userId, $data);
            Log::info('Account locked successfully', ['result' => $result]);
            return response()->json(['success' => true, 'message' => 'Account locked successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to lock account', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Error locking account: ' . $e->getMessage()], 500);
        }
    }

    public function getAccountCounts()
    {
        $users = $this->supabase->fetchTable('users');
        $activeUsers = array_filter($users, fn($user) => $user['status'] !== 'locked');
        $totalAccounts = count($activeUsers);
        $touristAccounts = count(array_filter($activeUsers, fn($user) => $user['user_type'] === 'user'));
        $adminAccounts = count(array_filter($activeUsers, fn($user) => $user['user_type'] === 'admin'));

        return response()->json([
            'totalAccounts' => $totalAccounts,
            'touristAccounts' => $touristAccounts,
            'adminAccounts' => $adminAccounts,
        ]);
    }

    public function getUserDetails($userId)
{
    try {
        $userId = (int) $userId;
        \Log::info("Fetching decrypted user for ID: {$userId}");

        // Try Laravel decryption first
        $decryptedUser = $this->supabase->getDecryptedUser($userId);

        // If Laravel decryption fails, call Supabase RPC fallback
        if (!$decryptedUser || empty($decryptedUser)) {
            \Log::warning("Laravel decryption failed or returned empty. Falling back to Supabase RPC.");

            $headers = [
                'apikey' => config('services.supabase.key'),
                'Authorization' => 'Bearer ' . config('services.supabase.key'),
                'Content-Type' => 'application/json',
            ];

            $response = Http::withHeaders($headers)
                ->post(config('services.supabase.url') . '/rest/v1/rpc/get_decrypted_user', [
                    'p_user_id' => $userId
                ]);

            if ($response->successful()) {
                $result = $response->json();

                if (empty($result)) {
                    \Log::error("Supabase RPC returned empty.");
                    return response()->json(['error' => 'No user data found'], 404);
                }

                $user = $result[0];

                // Append address if admin
                if ($user['user_type'] === 'admin') {
                    $spots = $this->supabase->fetchTable('tourist_spots', ['created_by' => "eq.$userId"]);
                    $user['address'] = !empty($spots) ? ($spots[0]['address'] ?? 'N/A') : 'N/A';
                }

                return response()->json($user);
            } else {
                \Log::error("Supabase RPC failed", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['error' => 'Decryption fallback failed'], 500);
            }
        }

        $user = $decryptedUser[0];

        // For admin, attach address from spots
        if ($user['user_type'] === 'admin') {
            $spots = $this->supabase->fetchTable('tourist_spots', ['created_by' => "eq.$userId"]);
            $user['address'] = !empty($spots) ? ($spots[0]['address'] ?? 'N/A') : 'N/A';
        }

        return response()->json($user);

    } catch (\Throwable $e) {
        \Log::error("Exception in getUserDetails", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json(['error' => 'Internal Server Error'], 500);
    }
}

}