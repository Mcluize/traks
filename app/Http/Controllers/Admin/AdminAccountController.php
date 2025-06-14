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

            // Fetch only necessary fields
            $users = $this->supabase->fetchTable('users', [], false, 'name_hash, contact_hash, user_type, user_id');

            // Check for duplicate name using hash
            $newNameHash = $this->supabase->hashName($validated['full_name']);
            foreach ($users as $user) {
                if ($user['name_hash'] === $newNameHash) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Full name already exists. Please choose a different name.'
                    ], 422);
                }
            }

            // Check for duplicate contact using hash
            $newContactHash = $this->supabase->hashContact($validated['contact_details']);
            foreach ($users as $user) {
                if ($user['contact_hash'] === $newContactHash) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Contact details already exist. Please use a different contact.'
                    ], 422);
                }
            }

            // Generate admin ID
            $adminUsers = array_filter($users, fn($user) => $user['user_type'] === 'admin');
            $maxAdminId = $adminUsers ? max(array_map(fn($user) => (float)$user['user_id'], $adminUsers)) : 0;
            $adminId = ceil($maxAdminId) + 1;

            $defaultPin = '1234';
            $salt = random_bytes(16);
            $saltString = rtrim(strtr(base64_encode($salt), '+/', '-_'), '=');
            $saltedPin = $defaultPin . $saltString;
            $pinHash = hash('sha256', $saltedPin);

            // Encrypt and hash data
            $encryptedFullName = $this->supabase->encryptData($validated['full_name']);
            $encryptedContactDetails = $this->supabase->encryptData($validated['contact_details']);
            $nameHash = $newNameHash;
            $contactHash = $newContactHash;

            $createdAt = now()->toDateTimeString();

            $adminData = [
                'user_id' => $adminId,
                'full_name' => $encryptedFullName,
                'contact_details' => $encryptedContactDetails,
                'name_hash' => $nameHash,
                'contact_hash' => $contactHash,
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

        // Map admin addresses as before
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

        // Counts based on above logic
        $activeAdmins = array_filter($users, function($user) {
            return isset($user['user_type'], $user['status']) 
                && strtolower(trim($user['user_type'])) === 'admin'
                && strtolower(trim($user['status'])) === 'active';
        });

        $adminAccounts = count($activeAdmins);

        $touristAccounts = count(array_filter($users, function($user) {
            return isset($user['user_type']) && strtolower(trim($user['user_type'])) === 'user';
        }));

        $totalAccounts = $touristAccounts + $adminAccounts;

        Log::info("Total Accounts: $totalAccounts");
        Log::info("Tourist Accounts: $touristAccounts");
        Log::info("Admin Accounts: $adminAccounts");

        return view('vendor.backpack.ui.manage-tourists', compact('totalAccounts', 'touristAccounts', 'adminAccounts', 'users'));
    }

    public function deactivateAccount(Request $request, $userId)
    {
        try {
            Log::info('Attempting to deactivate account', ['user_id' => $userId]);
            $data = ['status' => 'deactivated'];
            $result = $this->supabase->updateTable('users', 'user_id', $userId, $data);
            Log::info('Account deactivated successfully', ['result' => $result]);
            return response()->json(['success' => true, 'message' => 'Account deactivated successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to deactivate account', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Error deactivating account: ' . $e->getMessage()], 500);
        }
    }

    public function activateAccount(Request $request, $userId)
    {
        try {
            Log::info('Attempting to activate account', ['user_id' => $userId]);
            $data = ['status' => 'active'];
            $result = $this->supabase->updateTable('users', 'user_id', $userId, $data);
            Log::info('Account activated successfully', ['result' => $result]);
            return response()->json(['success' => true, 'message' => 'Account activated successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to activate account', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Error activating account: ' . $e->getMessage()], 500);
        }
    }

    public function deleteAccount(Request $request, $userId)
    {
        try {
            Log::info('Attempting to delete account', ['user_id' => $userId]);
            $result = $this->supabase->deleteFromTable('users', 'user_id', $userId);
            Log::info('Account deleted successfully', ['result' => $result]);
            return response()->json(['success' => true, 'message' => 'Account deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to delete account', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Error deleting account: ' . $e->getMessage()], 500);
        }
    }

    public function getAccountCounts()
    {
        // Tourists count without status filter (all tourists)
        $touristAccounts = $this->supabase->fetchTable('users', [
            'user_type' => 'eq.user',
        ], true);

        // Admins count only active ones
        $adminAccounts = $this->supabase->fetchTable('users', [
            'status' => 'eq.active',
            'user_type' => 'eq.admin',
        ], true);

        // Total accounts = sum of all tourists + active admins
        $totalAccounts = $touristAccounts + $adminAccounts;

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

            $decryptedUser = $this->supabase->getDecryptedUser($userId);
            if (!$decryptedUser || empty($decryptedUser)) {
                \Log::warning("No decrypted user data returned for ID: {$userId}");
                return response()->json(['error' => 'User not found or decryption failed'], 404);
            }

            $user = $decryptedUser[0];
            if ($user['user_type'] === 'admin') {
                $spots = $this->supabase->fetchTable('tourist_spots', ['created_by' => "eq.$userId"]);
                $user['address'] = !empty($spots) ? ($spots[0]['address'] ?? 'N/A') : 'N/A';
            }

            return response()->json($user);
        } catch (\Exception $e) {
            \Log::error("Error fetching user details for ID: {$userId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to fetch user details'], 500);
        }
    }

    public function getMembers($userId)
    {
        try {
            $members = $this->supabase->fetchTable('members', ['user_id' => "eq.$userId"], false, 'member_id');
            return response()->json($members);
        } catch (\Exception $e) {
            Log::error('Failed to fetch members', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch members'], 500);
        }
    }

    public function getMemberDetails($memberId)
    {
        try {
            $members = $this->supabase->fetchTable('members', ['member_id' => "eq.$memberId"], false, 'member_id, full_name, created_at');
            if (empty($members)) {
                return response()->json(['error' => 'Member not found'], 404);
            }
            $member = $members[0];
            $decrypted = $this->supabase->decryptData($member['full_name']);
            $member['full_name'] = is_array($decrypted) && isset($decrypted['decrypted_text']) ? $decrypted['decrypted_text'] : $decrypted;
            return response()->json($member);
        } catch (\Exception $e) {
            Log::error('Failed to fetch member details', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch member details'], 500);
        }
    }
}