<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function store(User $user) : RedirectResponse
    {
        // 로그인한 사용자와 프로필 주인과의 관계
        Auth::user()->followings()->attach($user->id);

        return back();
    }

    public function destroy(User $user) : RedirectResponse
    {
        Auth::user()->followings()->detach($user->id);

        return back();
    }
}
