<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;

/**
 * @brief 댓글을 위한 Controller
 * @detail 댓글 목록 조회, 작성, 수정, 삭제
 * @author Parksomi
 * @date 2024-12-12
 * @version 1.0.0
 */
class CommentController extends Controller
{
    /**
     * @brief Comment 클래스의 생성자
     * @details store 메소드의 경우, 인증된 사용자만 접근할 수 있도록 제한
     */    
    public function __construct(){
        $this->middleware('auth')->only('store');
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    /**
     * @brief 댓글을 저장하는 메소드
     * @details 로그인한 사용자의 경우, 유효성 검사 후 댓글 저장
     */
    public function store(StoreCommentRequest $request)
    {
        $input = $request->validated();

        Comment::create([
            'article_id' => $input['article_id'],
            'user_id' => $request->user()->id,
            'body' => $input['body']
        ]);

        return redirect()->route('articles.show', ['article' => $input['article_id']]);
    }

    public function show(Comment $comment)
    {
        //
    }

    public function edit(Comment $comment)
    {
        //
    }

    /**
     * @brief 댓글을 수정하는 메소드
     * @details 수정 버튼 클릭 시, 기존 댓글 정보를 불러오고 유효성 검사 후 수정
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $input = $request->validated();

        $comment->body = $input['body'];
        $comment->updated_at = now();
        $comment->save();

        // JSON 형식으로 응답 반환
        return response()->json([
            'success' => true,
            'message' => '댓글이 수정되었습니다.',
            'data' => $comment
        ]);
    }

    /**
     * @brief 댓글을 삭제할 수 있는 메소드
     * @details 삭제버튼 클릭 시, 삭제 후 새로고침
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->back();
    }
}
