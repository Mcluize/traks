<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    protected $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    public function index(Request $request)
    {
        $allIncidents = $this->supabaseService->fetchTable('emergency_reports');
        
        usort($allIncidents, function($a, $b) {
            return strtotime($b['timestamp'] ?? 0) - strtotime($a['timestamp'] ?? 0);
        });
        
        $filterType = $request->input('filter', 'all');
        $searchId = $request->input('search_id', null);
        
        $filteredIncidents = $this->applyTimeFilter($allIncidents, $filterType);
        
        if ($searchId) {
            $filteredIncidents = array_filter($filteredIncidents, function($incident) use ($searchId) {
                return stripos($incident['user_id'] ?? '', $searchId) !== false;
            });
        }
        
        $message = empty($filteredIncidents) ? 'No incidents found' : null;
        
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
            $allIncidents = $this->supabaseService->fetchTable('emergency_reports');
            
            usort($allIncidents, function($a, $b) {
                return strtotime($b['timestamp'] ?? 0) - strtotime($a['timestamp'] ?? 0);
            });
            
            $filterType = $request->input('filter', 'all');
            $searchId = $request->input('search_id', null);
            
            $filteredIncidents = $this->applyTimeFilter($allIncidents, $filterType);
            
            if ($searchId) {
                $filteredIncidents = array_filter($filteredIncidents, function($incident) use ($searchId) {
                    return stripos($incident['user_id'] ?? '', $searchId) !== false;
                });
            }
            
            $message = empty($filteredIncidents) ? 'No incidents found' : null;
            
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