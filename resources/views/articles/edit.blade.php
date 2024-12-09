<x-app-layout>
    <x-slot name="header">  
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            글수정
        </h2>
    </x-slot>

    <div class="container p-5 max-w-7xl mx-auto">
        <!-- get이나 post 이외의 메서드를 폼에다가 지정해줄 수 없음 -->
        <form action="{{ route('articles.update', ['article' => $article->id]) }}" method="POST" class="mt-5">
            @csrf
            <!-- <input type="hidden" name="_method" value="PUT"> PUT 메서드 지정 -->
            @method('PATCH')
            <textarea name='body' class="block w-full mb-2 rounded h-40">{{ old('body') ?? $article->body }}</textarea>
            @error('body')
                <p class="text-xs text-red-500 mb-3"> {{ $message }} </p>
            @enderror
            <div class="flex">
                <x-primary-button class="h-11 ml-auto mt-2 text-lg">저장하기</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>