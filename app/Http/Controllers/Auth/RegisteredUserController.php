<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Mail\SendEmail;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'postcode' => ['required', 'string'],
            'address' => ['required', 'string'],
            'detailAddress' => ['required', 'string']
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->name,
            'password' => Hash::make($request->password),
            'postcode' => $request->postcode,
            'address' => $request->address,
            'detailAddress' => $request->detailAddress,
        ]);

        $request->session()->forget(['email_check']);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    public function email(Request $request)
    {
        $email = $request->input('email');
        $pass = rand(100000, 999999); // 인증코드 생성을 위한 6자리 랜덤변수
        Log::info('Received email: '.$email.$pass);
        $request->session()->put('pass', $pass); // 세션션에 생성된 인증코드 저장
        $storedPass = $request->session()->get('pass'); // 세션에 저장된 인증코드 가져오기

        Log::info('Stored pass in session: ' . $storedPass);
        try {
            // 메일 전송
            Mail::to($email)->send(new SendEmail($email, $pass));
            Log::info('Email sent successfully to: ' . $email);
            return response()->json(['success' => true, 'message' => 'Email sent successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send email'], 500);
        }

    }

    public function verify(Request $request)
    {
        $code = $request->input('code'); // 사용자가 입력한 코드
        Log::info('입력코드'.$code);
        $sessionCode = $request->session()->get('pass'); // 세션에 저장된 인증코드

        if($code == $sessionCode){ // 같을 경우
            $request->session()->put('email_check', true);
            return response()->json(['success' => true, 'message' => '인증에 성공하였습니다.']);
        } else { // 다를 경우
            $request->session()->put('email_check', false);
            return response()->json(['success' => false, 'message' => '인증에 실패하였습니다.']);
        }
    }

    public function emailCheck(Request $request)
    {
        $isVerified = $request->session()->get('email_check');

        return response()->json(['is_verified' => $isVerified]);
    }
}
 