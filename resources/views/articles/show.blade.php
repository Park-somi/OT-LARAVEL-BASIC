<x-app-layout>
    <div class="container p-5 mx-auto">
        <div class="border rounded p-4">
            <p>{{ $article->body }}</p>
            <p>{{ $article->user->name }}</p>
            <p class="text-xs text-gray-500">
                <a href="{{ route('articles.show', ['article' => $article->id]) }}">
                    {{ $article->created_at->diffForHumans() }}
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
                    <div>
                        <p>{{ $comment->body }}</p>
                        <p class="text-sm text-gray-500">{{ $comment->user->name }} {{ $article->created_at->diffForHumans() }}</p>
                    </div>
                @endforeach
             </div>
            <!-- 댓글 목록 끝 -->
        </div>
        <!-- 댓글 영역 끝 -->
    </div>
</x-app-layout>