<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        //Gửi tự động 1 lần khi truy cập trong 1 session
        if (!session()->has('verification_mail_sent')) {
            $request->user()->sendEmailVerificationNotification();
            session()->put('verification_mail_sent', true);
        }

        return view('auth.verify-email');
    }

    public function statusView()
    {
        return view('auth.verify-email-status');
    }
}
