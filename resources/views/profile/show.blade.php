<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="text-center">
                <h1 class="text-center text-2xl font-bold"> {{ $user->name }} </h1>
                <div>
                    게시글 {{ $user->articles->count() }}
                </div>
            </div>

            <div>
                @forelse($user->articles as $article)
                    <x-list-article-item :article=$article />
                @empty
                    <p>게시글이 없습니다.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
