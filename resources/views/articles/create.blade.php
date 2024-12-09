<x-app-layout> <!-- 공통 laytout 적용 -->
    <x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            글쓰기
        </h2>
    </div>
    </x-slot>

    <div class="container p-5 max-w-7xl mx-auto">
        <form action="/articles" method="POST" class="mt-5">
            @csrf
            <textarea name='body' class="block w-full mb-2 rounded h-40" value="{{ old('body') }}"></textarea>
            @error('body')
                <p class="text-xs text-red-500 mb-3"> {{ $message }} </p>
            @enderror
            <div class="flex">
                <x-primary-button class="h-11 ml-auto mt-2 text-lg">저장하기</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>