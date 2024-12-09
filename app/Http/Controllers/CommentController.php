<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;

class CommentController extends Controller
{
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

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->back();
    }
}
