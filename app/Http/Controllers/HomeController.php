<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

/**
 * @brief 메인페이지를 위한 단일액션 Controller
 * @detail 로그인 여부와 사용자에 따라 다른 글목록 출력
 * @author Parksomi
 * @data 2024-12-12
 * @version 1.0.0
 */
class HomeController extends Controller
{

    /**
     * @brief 게시글 목록을 조회하는 메소드
     * @detail 로그인 안한 사용자의 경우, 모든 게시글 목록 출력
     * @detail 로그인 한 사용자의 경우, 본인이 작성한 글과 구독한 사용자의 글목록 출력
     */
    public function __invoke(Request $request)
    {
        // 로그인된 사용자 중 Google OTP 인증이 완료되지 않은 경우 로그아웃
        if (Auth::check() && session('google2fa_authenticated') === false) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
        }

        $q = $request->input('q');
        $type = $request->input('type', '제목+내용');
        $sort = $request->input('sort', 'newest');

        $articles = Article::with('user') // Eloquent 관계
        ->withCount('comments') // 댓글 수 표시
        ->withExists(['comments as recent_comments_exists' => function($query){ // 24시간이 안지난 댓글이 존재하는지
            $query->where('created_at', '>', Carbon::now()->subDay());
        }])
        // 조건적 where절 
        // 로그인한 경우에만 조건 추가
        // 이 글을 쓴 user의 id가 로그인한 사용자가 팔로워하는 user의 id를 포함해야 함
        // ->when(Auth::check(), function($query){
        //     $query->whereHas('user', function(Builder $query){
        //         $query->whereIn('id', Auth::user()->followings->pluck('id')->push(Auth::id()));
        //     });
        // })
        ->when($q, function($query, $q) use ($type){
            switch ($type){
                case '제목':
                    $query->where('title', 'like', "%$q%");

                case '제목+내용':
                    $query->where(function($queryBuilder) use ($q){
                        $queryBuilder->where('title', 'like', "%$q%")
                                    ->orwhere('body', 'like', "%$q%");
                    });
                

                case '작성자':
                    $query->orWhereHas('user', function(Builder $queryBuilder) use ($q) {
                        $queryBuilder->where('username', 'like', "%$q%");
                    });
                    break;
            }
        })
        ->when($sort === 'newest', function ($query) {
            $query->latest(); // 최신순 정렬
        })
        ->when($sort === 'oldest', function ($query) {
            $query->oldest(); // 오래된순 정렬
        })
        ->when($sort === 'comments', function ($query) {
            $query->orderBy('comments_count', 'desc'); // 댓글 수 순 정렬
        })
        ->latest()
        ->paginate(5);

        // 페이지네이션 데이터에 "최근 글" 여부 추가
        foreach ($articles as $article) {
            $article->is_recent = $article->created_at > Carbon::now()->subDay();
        }

        // 1. 'view'로의 데이터 전달 방식 : 배열
        return view('articles.index', 
        [
            'articles' => $articles, // 뷰에 'articles' 데이터 전달
            'q' => $q,
            'sort' => $sort // 현재 정렬 기준 전달
            // 'results' => $results
            // 'totalCount' => $totalCount,
            // 'page' => $page,
            // 'perPage' => $perPage
        ]);
    }
}
