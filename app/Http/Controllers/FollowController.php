<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @brief 구독과 관련된 Controller
 * @detail 구독 설정, 해지
 * @author Parksomi
 * @data 2024-12-12
 * @version 1.0.0
 */
class FollowController extends Controller
{
    /**
     * @brief 사용자가 다른 사용자를 구독(팔로우)하도록 설정하는 메서드
     * @details 로그인한 사용자와 전달된 사용자($user)를 구독(팔로우)목록에 추가
     */
    public function store(User $user) : RedirectResponse
    {
        // 로그인한 사용자와 프로필 주인과의 관계
        Auth::user()->followings()->attach($user->id);

        return back();
    }

    /**
     * @brief 사용자가 다른 사용자를 구독(팔로우)을 해지하도록 설정하는 메서드
     * @details 로그인한 사용자와 전달된 사용자($user)를 구독(팔로우)목록에서 삭제
     */    
    public function destroy(User $user) : RedirectResponse
    {
        Auth::user()->followings()->detach($user->id);

        return back();
    }
}
