<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'], //current_password => tự động lấy user từ $request->user() và so sánh giá trị nhập vào với Hash::check().
            'password' => ['required', 'string', 'min:6', 'confirmed'], //confirmed => check trùng với password_confirmation
        ]);
        try {   
            $user = $request->user();
            $user->password = Hash::make($validated['password']);
            $user->save();

            return back()->with('success', 'Password updated!');
        } catch (\Exception $e) {
            return back()->with(['error' => 'There was an error updating the password. Please try again.']);
        }
    }
}
