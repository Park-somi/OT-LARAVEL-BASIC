<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate(); // 기본 로그인 인증

        $request->session()->regenerate(); // 세션 재설정

        $user = Auth::user();

        // Google OTP 인증이 필요한 사용자인 경우
        if ($user->is_google2fa_enabled) {
            session(['google2fa_authenticated' => false]);
            
            // Google OTP 인증 페이지로 이동
            return redirect()->route('google2fa.login');
        }

        session(['google2fa_authenticated' => true]);

        // 5. OTP 비활성화 상태 -> 대시보드로 이동
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
