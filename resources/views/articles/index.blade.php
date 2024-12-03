<x-app-layout>
    <x-slot name="header">  
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            글목록
        </h2>
    </x-slot>

    <div class="container p-5 mx-auto">
        @foreach($articles as $article)
            <x-list-article-item :article=$article />
        @endforeach
    </div>
    
    <div class="container p-5">
        {{ $articles->links() }}        
    </div>
</x-app-layout>