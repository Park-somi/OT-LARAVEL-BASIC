<div class="flex flex-row">
    @can('update', $article)
    <p class="mr-1">
        <button class="text-sm mr-2 text-indigo-500">
            <a href="{{ route('articles.edit', ['article' => $article->id]) }}">
                수정
            </a>
        </button>
    </p>
    @endcan
    @can('delete', $article)
    <form action="{{ route('articles.destroy', ['article' => $article->id]) }}" method="POST">
        @csrf
        @method('DELETE')
        <button class="text-sm text-gray-500">삭제</button>
    </form>
    @endcan
</div>