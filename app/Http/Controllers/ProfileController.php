<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Article;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;

/**
 * @brief 프로필을 위한 Controller
 * @detail 프로필 상세 조회, 수정, 삭제 
 * @author Parksomi
 * @data 2024-12-12
 * @version 1.0.0
 */
class ProfileController extends Controller
{
    public function show(User $user) : View
    {
        $user->load('articles.user');
        $user->articles->loadCount('comments');
        $user->articles->loadExists(['comments as recent_comments_exists' => function($query){ // 24시간이 안지난 댓글이 존재하는지
            $query->where('created_at', '>', Carbon::now()->subDay());
        }]);

        foreach ($user->articles as $article) {
            $article->is_recent = $article->created_at > Carbon::now()->subDay();
        }    

        return view('profile.show', [
            'user' => $user,
            'article' => $article
        ]);
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * @brief 프로필을 수정하는 메소드
     * @details 로그인한 유저의 정보를 불러오고 수정 버튼 클릭 시, 유효성 검사 후 수정
     */   
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // ProfileUpdateRequest를 통해 유효성 검사된 데이터를 받아와 사용자의 프로필을 업데이트
        // 업데이트 후, 프로필 편집 페이지로 리디렉션하고 상태 메시지를 반환
        // 현재 인증된 사용자 객체를 가져오고, 검증된 데이터를 채움
        $request->user()->fill($request->validated());

        // 이메일 필드가 변경되었는지 확인
        if ($request->user()->isDirty('email')) {
            // 이메일이 변경된 경우, email_verified_at을 null로 설정하여 인증을 초기화
            $request->user()->email_verified_at = null;
        }

        // 변경된 데이터를 데이터베이스에 저장
        $request->user()->save();

        // 프로필 편집 페이지로 리디렉션하며, 상태 메시지('profile-updated') 전달
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
