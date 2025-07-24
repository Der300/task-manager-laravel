<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $start = microtime(true);

        $status = Password::sendResetLink($request->only('email'));

        // Thời gian xử lý tối thiểu (ví dụ 2 giây)
        $minResponseTime = 2.0; // seconds

        $duration = microtime(true) - $start;

        // Nếu thời gian xử lý thực tế nhỏ hơn minResponseTime thì delay thêm
        if ($duration < $minResponseTime) {
            usleep(($minResponseTime - $duration) * 1e6); // usleep tính bằng micro giây
        }

        // Luôn trả về thông báo success chung chung, ko tiết lộ email tồn tại hay không
        return redirect('login')->with('success', 'If your email address exists in our system, you will receive a password reset link shortly.');
    }
}
