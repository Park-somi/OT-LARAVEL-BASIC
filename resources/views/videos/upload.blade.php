<x-app-layout> <!-- 공통 laytout 적용 -->
    <x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            업로드
        </h2>
    </div>
    </x-slot>

    <div class="container p-5 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('videos.uploadFile') }}" method="POST" class="mt-5" enctype="multipart/form-data">
            @csrf
            <div class="flex items-center mb-4">
                <label for="file" class="mr-6">동영상</label>
                <input type="file" id="file" name="file" class="flex-grow">
            </div>           
            <div class="flex">
                <button class="py-3 px-5 bg-gray-500 text-white rounded hover:bg-gray-700 ml-auto" type="submit">업로드</button>
            </div>
        </form>
    </div>
</x-app-layout>