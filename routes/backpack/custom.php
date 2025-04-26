<?php

use Illuminate\Support\Facades\Route;
use App\Services\SupabaseService;
use App\Http\Controllers\Admin\MyAccountController;
use App\Http\Controllers\UserPinController;
use App\Http\Controllers\Admin\AdminAccountController;

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
    
    Route::get('manage-tourists', [AdminAccountController::class, 'manageTourists']);

    Route::get('incident-detection', function () {
        return view('vendor.backpack.ui.incident-detection');
    })->name('backpack.incident-detection');

    Route::get('analytics', function () {
        return view('vendor.backpack.ui.analytics');
    })->name('backpack.analytics');

    Route::get('notification', function () {
        return view('vendor.backpack.ui.notification');
    })->name('notifications.index');
    
    // Updated setting route to redirect to edit-account-info
    Route::get('setting', function () {
        return redirect()->route('backpack.account.info');
    })->name('backpack.setting');

    Route::get('edit-account-info', 'MyAccountController@getAccountInfoForm')->name('backpack.account.info');
    Route::post('edit-account-info', 'MyAccountController@postAccountInfoForm')->name('backpack.account.info.store');
    Route::post('change-password', 'MyAccountController@postChangePasswordForm')->name('backpack.account.password');
    Route::post('/pin/verify', [UserPinController::class, 'verify']);
    Route::post('/pin/update', [UserPinController::class, 'update']);  
    Route::post('create-admin-account', 'AdminAccountController@create')->name('backpack.create-admin-account');
    Route::patch('/admin/lock/{userId}', [AdminAccountController::class, 'lockAccount'])->name('admin.lock');
});