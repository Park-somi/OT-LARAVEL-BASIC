<x-app-layout>
    <x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            글상세
        </h2>
    </div>
    </x-slot>
    <div class="container p-5 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="border rounded p-4 flex justify-between">
            <div>
                <p class="mb-3 text-lg flex items-center">
                    <span>{{ $article->title }}</span>
                    <span class="text-sm text-red-500 ml-2 bg-red-100 px-1 rounded">@if($article->is_recent) new @endif</span>
                </p>
                <p class="mb-3">{!! nl2br(e($article->body)) !!}</p>
                @if (!empty($article->file_name))
                    <div class="mt-2 mb-2">
                        <span class="text-sm text-gray-500 mr-2 bg-gray-100 p-2 rounded">현재 파일</span>
                        <span><a href="{{ route('articles.download', ['article' => $article->id]) }}" class="text-indigo-500">{{ $article->file_name }}</a></span>
                    </div>
                    
                    @if(Str::startsWith(mime_content_type(storage_path('app/public/file/' . $article->file_name)), 'image/'))
                        <img class="w-80" src="{{ asset('storage/file/'.$article->file_name) }}">
                    @endif

                @endif
                <p class="text-gray-500 mt-2">{{ $article->user->name }}</p>
                <p class="text-xs text-gray-500">
                    <a href="{{ route('articles.show', ['article' => $article->id]) }}">
                        {{ $article->updated_at->diffForHumans() }}
                        <span>댓글 {{ $article->comments_count }}</span>
                        <span class="text-red-500">@if($article->recent_comments_exists) (new) @endif</span>
                    </a>
                </p>
            </div>
    
            <x-article-button-group :article=$article/>
        </div>
        <!-- 댓글 영역 시작 -->
        <div class="mt-5">
            <!-- 댓글 작성 폼 시작 -->
            <form action="{{ route('comments.store') }}" method="POST">
                <div class="flex items-center">
                    @csrf
                    <input type="hidden" name="article_id" value="{{ $article->id }}" />
                    <x-text-input name="body" placeholder="댓글을 남겨보세요" class="mr-2 flex-grow h-12 text-gray-500 px-5"/>
                    <x-primary-button class="h-12">등록</x-primary-button>
                </div>
                @error('body')
                    <p class="text-xs text-red-500 mb-3 mt-3">{{ $message }}</p>
                @enderror
            </form>
            <!-- 댓글 작성 폼 끝 -->

            <!-- 댓글 목록 시작 -->
            <div class="mt-4 flex flex-col space-y-4">
                @foreach($article->comments as $comment)
                    <div id="comment_{{ $comment->id }}" class="border rounded p-5">
                        <div id="comment_view_{{ $comment->id }}" class="flex justify-between">
                            <div id="comment_body_{{ $comment->id }}">{{ $comment->body }}</div>
                            <div class="flex items-center">
                                @can('update', $comment)
                                <button class="text-sm mr-2 text-blue-500" onclick="editComment('{{ $comment->id }}')">수정</button>
                                @endcan
                                @can('delete', $comment)
                                <form action="{{ route('comments.destroy', ['comment' => $comment->id]) }}" class="flex items-center" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm text-gray-500">삭제</button>
                                </form>
                                @endcan
                            </div>
                        </div>

                        <div id="comment_edit_{{ $comment->id }}" class="hidden">
                            @csrf
                            @method('PUT')
                            <x-text-input id="edit_textarea_{{ $comment->id }}" class="border p-5 w-full h-12" value="{{ $comment->body }}"></x-text-input>
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