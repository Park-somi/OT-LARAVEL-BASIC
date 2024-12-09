<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                글목록
            </h2>
            <div>
                <form method="GET" action="{{ route('articles.index') }}">
                    <input type="text" name="q" class="rounded border-gray-200" placeholder="{{ $q ?? '검색' }}" />
                </form>
            </div>
        </div>
    </x-slot>

    <div class="container p-5 max-w-7xl mx-auto">
        @foreach($articles as $article)
            <x-list-article-item :article=$article />
        @endforeach
    </div>
    
    <div class="container p-5">
        {{ $articles->links() }}        
    </div>
</x-app-layout>