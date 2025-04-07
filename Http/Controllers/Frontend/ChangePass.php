<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChangePass extends Controller
{
    public function index()
    {
        return view('auth.passwords.confirm');
    }

    public function update(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed',  // This checks if 'new_password' matches 'new_password_confirmation'
        ]);

        // Check if the current password matches the logged-in user's password
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }
        // Update the password
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();
        return back()->with('success', 'Password changed successfully!');
    }

}
