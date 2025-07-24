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

        if ($currentUser->id === $user->id) {
            return back()->with('error', 'You cannot send a reset link to yourself.');
        }

        if ($currentUser->hasAnyRole(['manager', 'leader']) && $currentUser->department !== $user->department) {
            abort(403, 'You do not have permission to send a reset link to this user.');
        }

        $status = Password::sendResetLink([
            'email' => $user->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', __($status));
        }

        return back()->with(['error', __($status)]);
    }
}
