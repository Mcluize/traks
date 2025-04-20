<?php

use Illuminate\Support\Facades\Route;
use App\Services\SupabaseService;
use App\Http\Controllers\Admin\MyAccountController;
use App\Http\Controllers\UserPinController;
Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () {
    // Tracking page
    Route::get('tracking', function () {
        return view('vendor.backpack.ui.tracking'); 
    })->name('backpack.tracking');
    
    Route::get('manage-tourists', function (SupabaseService $supabase) {
        $users = $supabase->fetchTable('users');
        return view('vendor.backpack.ui.manage-tourists', compact('users'));
    });

    Route::get('incident-detection', function () {
        return view('vendor.backpack.ui.incident-detection');
    })->name('backpack.incident-detection');

    Route::get('analytics', function () {
        return view('vendor.backpack.ui.analytics');
    })->name('backpack.analytics');

    Route::get('notification', function () {
        return view('vendor.backpack.ui.notification');
    })->name('notifications.index');
    
    
    Route::get('setting', function () {
        return view('vendor.backpack.ui.setting');
    })->name('backpack.setting');

    
    Route::get('edit-account-info', 'MyAccountController@getAccountInfoForm')->name('backpack.account.info');
    Route::post('edit-account-info', 'MyAccountController@postAccountInfoForm')->name('backpack.account.info.store');
    Route::post('change-password', 'MyAccountController@postChangePasswordForm')->name('backpack.account.password');
    Route::post('/pin/verify', [UserPinController::class, 'verify']);
    Route::post('/pin/update', [UserPinController::class, 'update']);  
    Route::post('create-admin-account', 'AdminAccountController@create')->name('backpack.create-admin-account');
});

