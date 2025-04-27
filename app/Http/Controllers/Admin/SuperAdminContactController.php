<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\SupabaseService;
use Alert;

class SuperAdminContactController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->middleware(backpack_middleware());
        $this->supabase = $supabase;
    }

    /**
     * Fetch the super admin's contact details from Supabase.
     *
     * @return string|null
     */
    public function getContactDetails()
    {
        $filters = [
            'select' => 'contact_details',
            'user_type' => 'eq.super admin',
        ];
        try {
            $supabaseData = $this->supabase->fetchTable('users', $filters);
            return $supabaseData ? ($supabaseData[0]['contact_details'] ?? null) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Update the super admin's contact details in Supabase.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateContactDetails(Request $request)
    {
        $request->validate([
            'super_admin_contact' => 'required|string|max:255',
        ]);

        try {
            $response = $this->supabase->updateTable('users', 'user_type', 'super admin', ['contact_details' => $request->super_admin_contact]);

            // Check if any rows were updated
            if (!empty($response)) {
                // Fetch the updated data to ensure the value changed
                $updatedData = $this->supabase->fetchTable('users', [
                    'select' => 'contact_details',
                    'user_type' => 'eq.super admin',
                ]);

                if (!empty($updatedData) && $updatedData[0]['contact_details'] === $request->super_admin_contact) {
                    Alert::success('Super admin contact updated successfully.')->flash();
                } else {
                    Alert::error('Update failed: No changes applied.')->flash();
                }
            } else {
                Alert::error('Update failed: No rows were updated.')->flash();
            }
        } catch (\Exception $e) {
            Alert::error('Failed to update super admin contact: ' . $e->getMessage())->flash();
        }

        return redirect()->back();
    }
}