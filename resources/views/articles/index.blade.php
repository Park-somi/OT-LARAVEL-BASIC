<x-app-layout>
    <x-slot name="header">  
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            글목록
        </h2>
    </x-slot>

    <div class="container p-5 mx-auto">
        @foreach($articles as $article)
            <div class="background-white border rounded mb-3 p-3">
                <p>{{ $article->body }}</p>
                <p>{{ $article->user->name }}</p>
                <p><a href="{{ route('articles.show', ['article' => $article->id]) }}">{{ $article->created_at->diffForHumans() }}</p>
                
                <x-article-button-group :article=$article/>
            </div>
        @endforeach
    </div>
    
    <div class="container p-5">
        {{ $articles->links() }}        
    </div>
</x-app-layout>