<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;

class DashboardController extends Controller
{
    protected $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index()
    {
        $users = $this->supabase->fetchTable('users'); // table from Supabase
        $analytics = $this->supabase->fetchTable('analytics'); // another table

        return view('admin.dashboard', compact('users', 'analytics'));
    }
}
