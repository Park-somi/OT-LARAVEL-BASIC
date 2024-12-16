<x-app-layout> <!-- 공통 laytout 적용 -->
    <x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            글쓰기
        </h2>
    </div>
    </x-slot>

    <div class="container p-5 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form action="/articles" method="POST" class="mt-5" enctype="multipart/form-data">
            @csrf
            <div class="flex items-center mb-4">
                <label for="title" class="mr-6">제목</label>
                <input type="text" id="title" name="title" class="flex-grow p-3 border rounded" placeholder="제목을 입력하세요">
            </div>
            <div class="flex items-start mb-4">
                <label for="body" class="mr-6 mt-2">내용</label>
                <div class="flex-grow">
                    <textarea id="body" name="body" class="block w-full p-3 border rounded h-60" placeholder="내용을 입력하세요">{{ old('body') }}</textarea>
                    @error('body')
                        <p class="text-xs text-red-500 mt-2"> {{ $message }} </p>
                    @enderror
                </div>
            </div>
            <div class="flex items-center mb-4">
                <label for="file" class="mr-6">파일</label>
                <input type="file" id="file" name="file" class="flex-grow">
            </div>           
            <div class="flex">
                <button class="py-3 px-5 bg-gray-500 text-white rounded hover:bg-gray-700 ml-auto" type="submit">저장하기</button>
            </div>
        </form>
    </div>
</x-app-layout>