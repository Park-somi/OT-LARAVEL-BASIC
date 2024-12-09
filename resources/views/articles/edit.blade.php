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
            <div class="flex items-center mb-4">
                <div id="title" class="mr-4">제목</div>
                <input type="text" id="title" name="title" class="flex-grow p-3 border rounded" placeholder="제목을 입력하세요" value="{{ old('title') ?? $article->title }}">
            </div>
            <div class="flex items-start mb-4">
                <div id="내용" class="mr-4 mt-2">내용</div>
                <div class="flex-grow">
                    <textarea name='body' class="block w-full p-3 border rounded h-60" placeholder="내용을 입력하세요">{{ old('body') ?? $article->body }}</textarea>
                    @error('body')
                        <p class="text-xs text-red-500 mb-3"> {{ $message }} </p>
                    @enderror
                </div>
            </div>
            <div class="flex">
                <button class="py-3 px-5 bg-gray-500 text-white rounded hover:bg-gray-700 ml-auto" type="submit">수정하기</button>
            </div>
        </form>
    </div>
</x-app-layout>