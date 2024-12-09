<div class="background-white border rounded mb-3 p-5 flex justify-between">
    <div>
        <!-- 줄바꿈을 적용하여 출력 -->
        <!-- nl2br을 사용하여 텍스트의 줄바꿈 문자를 <br> 태그로 변환 -->
        <p class="text-lg flex items-center">
            <span>{{ $article->title }}</span>
            <span class="text-sm text-red-500 ml-2 bg-red-100 px-1 rounded">@if($article->is_recent) new @endif</span>
        </p>
        <p class="text-gray-500 mt-1">
            <a href="{{ route('profile', ['user' => $article->user->username]) }}">{{ $article->user->name }}</a>
        </p>
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