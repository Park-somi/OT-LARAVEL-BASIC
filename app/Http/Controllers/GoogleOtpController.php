<?php

namespace App\Http\Controllers;

use BaconQrCode\Writer;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

class GoogleOtpController extends Controller
{
    public function create(Request $request)
    {
        $google2fa = new Google2FA();
        $user = Auth::user();
    
        // Secret Key가 세션에 없으면 새로 생성
        if (!session()->has('google2fa_secret')) {
            session(['google2fa_secret' => $google2fa->generateSecretKey()]);
        }
    
        $secret = session('google2fa_secret');
    
        // QR 코드 생성
        $qrCodeData = "otpauth://totp/" . config('app.name') . ":" . $user->email .
                    "?secret=" . $secret .
                    "&issuer=" . config('app.name');
    
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $QR_Image = $writer->writeString($qrCodeData);
    
        return view('profile/google2fa/create', [
            'QR_Image' => $QR_Image,
            'secret' => $secret, // 세션에 저장된 Secret Key 사용
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'numeric', 'digits:6'],
        ]);

        $google2fa = new Google2FA();
        $user = Auth::user();

        $secret = session('google2fa_secret');
        if(!$secret){
            return response()->json(['message' => 'Google Secret Key가 없습니다'], 400);
        } 

        $isValid = $google2fa->verifyKey($secret, $request->otp);

        if($isValid){
            $user->google2fa_secret = $secret;
            $user->is_google2fa_enabled = true; // Google OTP 인증 성공 시 OTP 활성화
            $user->save();

            session()->forget('google2fa_secret');

            return response()->json(['success' => true, 'message' => 'Google OTP가 활성화되었습니다.']);
        }

        return response()->json(['success' => false,'message' => 'OTP가 유효하지 않습니다. 다시 시도해주세요.']);
    }

    public function loginVerify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'numeric', 'digits:6'],
        ]);

        $google2fa = new Google2FA();
        $user = Auth::user();

        $secret = $user->google2fa_secret;

        $isValid = $google2fa->verifyKey($secret, $request->otp);

        if($isValid){
            session(['google2fa_authenticated' => true]);

            return response()->json(['success' => true, 'message' => 'OTP 인증에 성공하셨습니다.']);
        }

        return response()->json(['success' => false,'message' => 'OTP 인증에 실패하셨습니다.']);
    }

    public function toggle(Request $request)
    {
        $user = Auth::user();
        $user->is_google2fa_enabled = !$user->is_google2fa_enabled;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Google OTP 설정이 변경되었습니다.']);
    }

    public function login()
    {
        return view('profile/google2fa/login');
    }

    public function reset(Request $request)
    {
        $google2fa = new Google2FA();
        $user = Auth::user();
    
        // Secret Key가 세션에 없으면 새로 생성
        if (!session()->has('google2fa_secret')) {
            session(['google2fa_secret' => $google2fa->generateSecretKey()]);
        }
    
        $secret = session('google2fa_secret');
    
        // QR 코드 생성
        $qrCodeData = "otpauth://totp/" . config('app.name') . ":" . $user->email .
                    "?secret=" . $secret .
                    "&issuer=" . config('app.name');
    
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $QR_Image = $writer->writeString($qrCodeData);
    
        return view('profile/google2fa/reset', [
            'QR_Image' => $QR_Image,
            'secret' => $secret, 
        ]);
    }
}
