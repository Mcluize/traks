<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SupabaseService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class AdminAccountController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function create(Request $request)
    {
        // Unchanged code remains as is
        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'contact_details' => 'required|string|max:255',
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

            $adminData = [
                'user_id' => $adminId,
                'full_name' => $validated['full_name'],
                'contact_details' => $validated['contact_details'],
                'address' => null,
                'created_at' => now()->toISOString(),
                'user_type' => 'admin',
                'pin_hash' => $pinHash . ':' . $saltString,
                'consent_preferences' => true,
                'encrypted_qr_code' => null,
                'onboarded' => false,
                'status' => 'active'
            ];

            \Log::info('Attempting to create admin account', ['data' => $adminData]);
            $result = $this->supabase->insertIntoTable('users', $adminData);
            \Log::info('Admin account created successfully', ['result' => $result]);

            return response()->json([
                'success' => true,
                'message' => 'Admin account created successfully',
                'user' => $adminData
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error when creating admin account', ['errors' => $e->errors()]);
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Failed to create admin account', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to create admin account: ' . $e->getMessage()], 500);
        }
    }

    public function manageTourists()
    {
        $users = $this->supabase->fetchTable('users');
        $touristSpots = $this->supabase->fetchTable('tourist_spots');

        // Create a mapping of admin user_id to address from tourist_spots
        $adminAddresses = [];
        foreach ($touristSpots as $spot) {
            if (isset($spot['created_by']) && !empty($spot['address'])) {
                $adminAddresses[$spot['created_by']] = $spot['address'];
            }
        }

        // Update admin users with the address from tourist_spots if available
        foreach ($users as &$user) {
            if ($user['user_type'] === 'admin' && isset($adminAddresses[$user['user_id']])) {
                $user['address'] = $adminAddresses[$user['user_id']];
            }
        }
        unset($user); // Unset reference to avoid side effects

        // Sort users in descending order by user_id
        usort($users, function($a, $b) {
            return $b['user_id'] <=> $a['user_id'];
        });
        
        $activeUsers = array_filter($users, function($user) {
            return $user['status'] !== 'locked';
        });
        
        $totalAccounts = count($activeUsers);
        $touristAccounts = count(array_filter($activeUsers, fn($user) => $user['user_type'] === 'user'));
        $adminAccounts = count(array_filter($activeUsers, fn($user) => $user['user_type'] === 'admin'));

        \Log::info("Total Active Accounts: $totalAccounts");
        \Log::info("Tourist Accounts: $touristAccounts");
        \Log::info("Admin Accounts: $adminAccounts");

        return view('vendor.backpack.ui.manage-tourists', compact('totalAccounts', 'touristAccounts', 'adminAccounts', 'users'));
    }

    public function lockAccount(Request $request, $userId)
    {
        // Unchanged code remains as is
        try {
            \Log::info('Attempting to lock account', ['user_id' => $userId]);
            
            $data = ['status' => 'locked'];
            $result = $this->supabase->updateTable('users', $userId, $data);
            
            \Log::info('Account locked successfully', ['result' => $result]);
            
            return response()->json([
                'success' => true,
                'message' => 'Account locked successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to lock account', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error locking account: ' . $e->getMessage()
            ], 500);
        }
    }
}