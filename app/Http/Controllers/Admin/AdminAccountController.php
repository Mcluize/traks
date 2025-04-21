<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SupabaseService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'contact_details' => 'required|string|max:255',
            'address' => 'required|string'
        ]);
        
        // Generate a unique admin ID (now as an integer)
        $adminId = mt_rand(100, 999);
        
        // Default PIN is 1234
        $defaultPin = '1234';
        $pinHash = Hash::make($defaultPin);
        
        // Prepare data for Supabase
        $adminData = [
            'user_id' => $adminId,  // Now it's an integer
            'full_name' => $validated['full_name'],
            'contact_details' => $validated['contact_details'],
            'address' => $validated['address'],
            'created_at' => now()->toISOString(),
            'user_type' => 'admin',
            'pin_hash' => $pinHash,
            'consent_preferences' => true,
            'encrypted_qr_code' => null,
            'onboarded' => false
        ];
        
        \Log::info('Attempting to create admin account', ['data' => $adminData]);
        
        // Insert into Supabase
        $result = $this->supabase->insertIntoTable('users', $adminData);
        
        \Log::info('Admin account created successfully', ['result' => $result]);
        
        return response()->json([
            'success' => true,
            'message' => 'Admin account created successfully',
            'user' => $adminData
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation error when creating admin account', [
            'errors' => $e->errors()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Failed to create admin account', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to create admin account: ' . $e->getMessage()
        ], 500);
    }
}
public function manageTourists(SupabaseService $supabase)
{
    // Fetch all users
    $users = $supabase->fetchTable('users');
    
    // Ensure it returns JSON with the correct structure
    return response()->json(['users' => $users]);
}

}