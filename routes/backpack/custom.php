<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MyAccountController;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Admin\IncidentController;
use App\Http\Controllers\Admin\SuperAdminContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\UserPinController;

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
], function () {

    // Tracking (static view)
    Route::get('tracking', function () {
        return view('vendor.backpack.ui.tracking');
    })->name('backpack.tracking');

    // Manage Tourists
    Route::get('manage-tourists', [AdminAccountController::class, 'manageTourists'])
         ->name('admin.manage-tourists');

    // Incidents listing
    Route::get('incidents', [IncidentController::class, 'index'])
         ->name('incidents.index');

    // **Analytics dashboard**
    Route::get('analytics', [AnalyticsController::class, 'index'])
         ->name('backpack.analytics');

    // AJAX endpoint for incident-status filters
    Route::get('analytics/incident-status', [AnalyticsController::class, 'getIncidentStatus'])
         ->name('analytics.incident-status');

    // Popular Spots endpoint using query parameters
    Route::get('analytics/popular-spots', [AnalyticsController::class, 'getPopularSpots'])
         ->name('analytics.popular-spots');

    // Tourist Growth endpoint
    Route::get('analytics/tourist-growth', [AnalyticsController::class, 'getTouristGrowth'])
    ->name('analytics.tourist-growth');

    // Notifications (static view)
    Route::get('notification', function () {
        return view('vendor.backpack.ui.notification');
    })->name('notifications.index');

    // Settings redirect → account info
    Route::get('setting', function () {
        return redirect()->route('backpack.account.info');
    })->name('backpack.setting');

    // Profile & password
    Route::get('edit-account-info', [MyAccountController::class, 'getAccountInfoForm'])
         ->name('backpack.account.info');
    Route::post('edit-account-info', [MyAccountController::class, 'postAccountInfoForm'])
         ->name('backpack.account.info.store');
    Route::post('change-password', [MyAccountController::class, 'postChangePasswordForm'])
         ->name('backpack.account.password');

    // Super-admin contacts
    Route::post('update-super-admin-contact', [SuperAdminContactController::class, 'updateContactDetails'])
         ->name('superadmin.contact.update');

    // PIN operations
    Route::post('pin/verify', [UserPinController::class, 'verify'])
         ->name('pin.verify');
    Route::post('pin/update', [UserPinController::class, 'update'])
         ->name('pin.update');

    // Admin account creation & locking
    Route::post('create-admin-account', [AdminAccountController::class, 'create'])
         ->name('backpack.create-admin-account');
    Route::patch('admin/lock/{userId}', [AdminAccountController::class, 'lockAccount'])
         ->name('admin.lock');

    // Incident table data (for AJAX datatables, etc.)
    Route::get('admin/incidents/table-data', [IncidentController::class, 'tableData'])
         ->name('admin.incidents.table-data');

    // Dashboard API endpoints
    Route::get('api/tourist-arrivals/{filter}', [DashboardController::class, 'getTouristArrivals']);
    Route::get('api/incident-reports/{filter}', [DashboardController::class, 'getIncidentReports']);
    Route::get('api/popular-spots/{filter}', [DashboardController::class, 'getPopularSpots']);
    Route::get('api/latest-tourists',            [DashboardController::class, 'getLatestTourists']);
    Route::get('api/checkins-by-spot/{filter}',  [DashboardController::class, 'getCheckinsBySpot']);
    Route::get('api/accounts/count',             [DashboardController::class, 'getAccountCounts']);
    Route::get('api/account-counts',             [AdminAccountController::class, 'getAccountCounts']);
    Route::get('/admin/analytics/map-data/{activityFilter}/{timeFilter}', [AnalyticsController::class, 'getMapData']);
    Route::get('analytics/tourist-activities', [AnalyticsController::class, 'getTouristActivities'])
         ->name('analytics.tourist-activities');
     Route::get('analytics/stats', [AnalyticsController::class, 'stats'])->name('admin.analytics.stats');
     Route::post('/admin/user/decrypted-details', [AdminAccountController::class, 'getDecryptedUserDetails']);
     Route::get('api/user-details/{userId}', [AdminAccountController::class, 'getUserDetails'])->name('admin.user.details');
});