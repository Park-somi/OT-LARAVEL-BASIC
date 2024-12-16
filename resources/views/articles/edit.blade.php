<x-app-layout>
    <x-slot name="header">  
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            글수정
        </h2>
    </x-slot>

    <div class="container p-5 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- get이나 post 이외의 메서드를 폼에다가 지정해줄 수 없음 -->
        <form action="{{ route('articles.update', ['article' => $article->id]) }}" method="POST" class="mt-5" enctype="multipart/form-data">
            @csrf
            <!-- <input type="hidden" name="_method" value="PUT"> PUT 메서드 지정 -->
            @method('PATCH')
            <div class="flex items-center mb-4">
                <label for="title" class="mr-6">제목</label>
                <input type="text" id="title" name="title" class="flex-grow p-3 border rounded" placeholder="제목을 입력하세요" value="{{ old('title') ?? $article->title }}">
            </div>
            <div class="flex items-start mb-4">
                <label for="body" class="mr-6 mt-2">내용</label>
                <div class="flex-grow">
                    <textarea id="body" name="body" class="block w-full p-3 border rounded h-60" placeholder="내용을 입력하세요">{{ old('body') ?? $article->body }}</textarea>
                    @error('body')
                        <p class="text-xs text-red-500 mt-2"> {{ $message }} </p>
                    @enderror
                </div>
            </div>
            <div class="flex items-center mb-4">
                <label for="file" class="mr-6">파일</label>
                <input type="file" id="file" name="file" class="flex-grow">
                @if (!empty($article->file_name))
                    <div class="mt-2">
                        <span class="text-sm text-gray-500 mr-2 bg-gray-100 p-2 rounded">현재 파일</span>
                        <span><a href="{{ route('articles.download', ['article' => $article->id]) }}" class="text-indigo-500">{{ $article->file_name }}</a></span>
                    </div>
                @endif          
            </div>
            <div class="flex">
                <button class="py-3 px-5 bg-gray-500 text-white rounded hover:bg-gray-700 ml-auto" type="submit">저장하기</button>
            </div>
        </form>
    </div>
</x-app-layout>