<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserPinController extends Controller
{
    
    public function verify(Request $request)
    {
        
        $admin = \App\Models\User::find(1); 
        if (!$admin || !Hash::check($request->pin, $admin->pin)) {
            return response()->json(['error' => 'Incorrect PIN'], 401);
        }

        return response()->json(['message' => 'PIN verified']);
    }

    // Update the PIN securely
    public function update(Request $request)
    {
        $request->validate([
            'current_pin' => 'required',
            'new_pin' => 'required|digits:6',
        ]);

        
        $admin = \App\Models\User::find(1); 

        if (!$admin || !Hash::check($request->current_pin, $admin->pin)) {
            return response()->json(['error' => 'Incorrect current PIN'], 401);
        }

        $admin->pin = bcrypt($request->new_pin);
        $admin->save();

        return response()->json(['message' => 'PIN updated']);
    }
}