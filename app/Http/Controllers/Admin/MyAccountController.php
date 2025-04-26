<?php

namespace App\Http\Controllers\Admin;

use Alert;
use Backpack\CRUD\app\Http\Requests\AccountInfoRequest;
use Backpack\CRUD\app\Http\Requests\ChangePasswordRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MyAccountController extends Controller
{
    protected $data = [];

    public function __construct()
    {
        $this->middleware(backpack_middleware());
    }

    /**
     * Show the user a form to change their personal information & password.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function getAccountInfoForm()
    {
        $this->data['title'] = trans('backpack::base.my_account');
        
        // Ensure the user is authenticated and retrieve the user data
        $this->data['user'] = backpack_auth()->user(); 

        // Return the custom view
        return view('vendor.backpack.my_account', $this->data);
    }

    /**
     * Save the modified personal information for a user.
     */
    public function postAccountInfoForm(AccountInfoRequest $request)
    {
        $user = $this->guard()->user();

        // Handle profile image upload if provided
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $imagePath; 
        }

        // Update the user's information
        $result = $user->update($request->except(['_token', 'profile_image']));

        if ($result) {
            Alert::success(trans('backpack::base.account_updated'))->flash();
        } else {
            Alert::error(trans('backpack::base.error_saving'))->flash();
        }

        return redirect()->back();
    }

    /**
     * Save the new password for a user.
     */
    public function postChangePasswordForm(ChangePasswordRequest $request)
    {
        $user = $this->guard()->user();

        // Check if the old password matches
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'The current password is incorrect.']);
        }

        // Update the password
        $user->password = Hash::make($request->new_password);

        if ($user->save()) {
            Alert::success(trans('backpack::base.account_updated'))->flash();
        } else {
            Alert::error(trans('backpack::base.error_saving'))->flash();
        }

        // If the AuthenticateSessions middleware is being used
        $this->guard()->logoutOtherDevices($request->new_password);

        // If the AuthenticateSession middleware was used until now,
        // update the password hash in the session
        if ($request->session()->has('password_hash_'.backpack_guard_name())) {
            $request->session()->put([
                'password_hash_'.backpack_guard_name() => $user->getAuthPassword(),
            ]);
        }

        return redirect()->back();
    }

    /**
     * Get the guard to be used for account manipulation.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return backpack_auth();
    }
}