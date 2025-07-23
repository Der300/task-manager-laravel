<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class SendResetPasswordLinkController extends Controller
{
    public function sendResetLink(User $user): RedirectResponse
    {
        $currentUser = Auth::user();

        if ($currentUser->hasRole('manager') && $currentUser->department !== $user->department) {
            abort(403, 'You do not have permission to send a reset link to this user.');
        }
        $status = Password::sendResetLink([
            'email' => $user->email,
        ]);

        return back()->with('status', __($status));
    }
}
