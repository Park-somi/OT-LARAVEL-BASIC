<x-app-layout>
    <div class="container p-5 mx-auto">
        <div class="border rounded p-4">
            <p>{!! nl2br(e($article->body)) !!}</p>
            <p>{{ $article->user->name }}</p>
            <p class="text-xs text-gray-500">
                <a href="{{ route('articles.show', ['article' => $article->id]) }}">
                    {{ $article->updated_at->diffForHumans() }}
                    <span>댓글 {{ $article->comments_count }}</span>
                </a>
            </p>
    
            <x-article-button-group :article=$article/>
        </div>
        <!-- 댓글 영역 시작 -->
        <div class="mt-3">
            <!-- 댓글 작성 폼 시작 -->
            <form action="{{ route('comments.store') }}" method="POST" class="flex">
                @csrf
                <input type="hidden" name="article_id" value="{{ $article->id }}" />
                <x-text-input name="body" class="mr-2"/>
                @error('body')
                    <x-input-error :messages=$messages />
                @enderror
                <x-primary-button>댓글 쓰기</x-primary-button>
            </form>
            <!-- 댓글 작성 폼 끝 -->

            <!-- 댓글 목록 시작 -->
            <div class="mt-4 flex flex-col space-y-4">
                @foreach($article->comments as $comment)
                    <div id="comment_{{ $comment->id }}" class="border rounded p-4">
                        <div id="comment_view_{{ $comment->id }}" class="flex justify-between">
                            <div id="comment_body_{{ $comment->id }}">{{ $comment->body }}</div>
                            <div class="flex items-center">
                                @can('update', $comment)
                                <button class="text-xs mr-2 text-gray-500" onclick="editComment('{{ $comment->id }}')">수정</button>
                                @endcan
                                @can('delete', $comment)
                                <form action="{{ route('comments.destroy', ['comment' => $comment->id]) }}" class="flex items-center" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-xs text-gray-500">삭제</button>
                                </form>
                                @endcan
                            </div>
                        </div>

                        <div id="comment_edit_{{ $comment->id }}" class="hidden">
                            @csrf
                            @method('PUT')
                            <textarea id="edit_textarea_{{ $comment->id }}" class="border p-2 w-full">{{ $comment->body }}</textarea>
                            <div class="flex justify-end mt-2">
                                <button class="text-xs mr-2 text-blue-500" onclick="saveComment('{{ $comment->id }}')">저장</button>
                                <button class="text-xs text-gray-500" onclick="cancelEdit('{{ $comment->id }}')">취소</button>
                            </div>
                        </div>

                        <p class="text-sm text-gray-500">{{ $comment->user->name }} {{ $comment->updated_at->diffForHumans() }}</p>
                    </div>
                @endforeach
             </div>
            <!-- 댓글 목록 끝 -->
        </div>
        <!-- 댓글 영역 끝 -->
    </div>
</x-app-layout>

<script>
    const updateCommentRoute = "{{ route('comments.update', ['comment' => '__COMMENT_ID__']) }}";

    function editComment(commentId) {
        // 수정 폼 표시
        document.getElementById(`comment_view_${commentId}`).classList.add('hidden');
        document.getElementById(`comment_edit_${commentId}`).classList.remove('hidden');
    }

    function cancelEdit(commentId) {
        // 수정 폼 숨기기 및 원래 상태 복원
        document.getElementById(`comment_view_${commentId}`).classList.remove('hidden');
        document.getElementById(`comment_edit_${commentId}`).classList.add('hidden');
    }

    function saveComment(commentId) {
        const newBody = document.getElementById(`edit_textarea_${commentId}`).value;

        // `__COMMENT_ID__`를 실제 commentId로 교체
        const url = updateCommentRoute.replace('__COMMENT_ID__', commentId);

        fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ body: newBody })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // 댓글 내용 업데이트
                document.getElementById(`comment_body_${commentId}`).innerText = newBody;

                // 수정 폼 숨기기 및 원래 상태 복원
                cancelEdit(commentId);
                alert(data.message);
            } else {
                alert('댓글 수정에 실패했습니다.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('댓글 수정 중 오류가 발생했습니다.');
        });
    }
</script>