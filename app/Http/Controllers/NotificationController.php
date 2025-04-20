<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        // Sample notifications
        $notifications = [
            'Notification 1 Details',
            'Notification 2 Details',
            'Notification 3 Details'
        ];

        // Passing the notifications to the view
        return view('vendor.backpack.ui.notification', compact('notifications'));
    }
}
