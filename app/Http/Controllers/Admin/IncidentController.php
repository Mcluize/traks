<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    protected $supabaseService;

    // Inject SupabaseService via constructor
    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    public function index(Request $request)
    {
        // Fetch all data from emergency_reports table using the service
        $allIncidents = $this->supabaseService->fetchTable('emergency_reports');
        
        // Sort incidents by timestamp (latest first)
        usort($allIncidents, function($a, $b) {
            return strtotime($b['timestamp'] ?? 0) - strtotime($a['timestamp'] ?? 0);
        });
        
        // Get filter and search parameters
        $filterType = $request->input('filter', 'all');
        $searchId = $request->input('search_id', null);
        
        // Apply time filter
        $filteredIncidents = $this->applyTimeFilter($allIncidents, $filterType);
        
        // Apply ID search filter if provided
        if ($searchId) {
            $filteredIncidents = array_filter($filteredIncidents, function($incident) use ($searchId) {
                return stripos($incident['user_id'] ?? '', $searchId) !== false;
            });
        }
        
        // Determine the message to display
        $message = null;
        if (empty($filteredIncidents)) {
            if ($searchId) {
                $message = "No incidents found for Tourist ID: $searchId";
            } else {
                $message = 'No incidents found';
            }
        }
        
        // Pass data to view
        return view('vendor.backpack.ui.incident-detection', [
            'incidents' => $filteredIncidents,
            'filter' => $filterType,
            'search_id' => $searchId,
            'message' => $message
        ]);
    }

    public function tableData(Request $request)
    {
        try {
            // Fetch all data from emergency_reports table using the service
            $allIncidents = $this->supabaseService->fetchTable('emergency_reports');
            
            // Sort incidents by timestamp (latest first)
            usort($allIncidents, function($a, $b) {
                return strtotime($b['timestamp'] ?? 0) - strtotime($a['timestamp'] ?? 0);
            });
            
            // Get filter and search parameters
            $filterType = $request->input('filter', 'all');
            $searchId = $request->input('search_id', null);
            
            // Apply time filter
            $filteredIncidents = $this->applyTimeFilter($allIncidents, $filterType);
            
            // Apply ID search filter if provided
            if ($searchId) {
                $filteredIncidents = array_filter($filteredIncidents, function($incident) use ($searchId) {
                    return stripos($incident['user_id'] ?? '', $searchId) !== false;
                });
            }
            
            // Determine the message to display
            $message = null;
            if (empty($filteredIncidents)) {
                if ($searchId) {
                    $message = "No incidents found for Tourist ID: $searchId";
                } else {
                    $message = 'No incidents found';
                }
            }
            
            // Return partial view with data and message
            return view('vendor.backpack.ui.incident-table-partial', [
                'incidents' => $filteredIncidents,
                'total' => count($filteredIncidents),
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    private function applyTimeFilter($incidents, $filterType)
    {
        if ($filterType == 'all') {
            return $incidents;
        }
        
        $now = time();
        return array_filter($incidents, function($incident) use ($filterType, $now) {
            $timestamp = strtotime($incident['timestamp'] ?? 0);
            $diff = $now - $timestamp;
            
            switch ($filterType) {
                case 'daily':
                    return $diff <= 86400; // 24 hours
                case 'weekly':
                    return $diff <= 604800; // 7 days
                case 'monthly':
                    return $diff <= 2592000; // 30 days
                default:
                    return true;
            }
        });
    }
}