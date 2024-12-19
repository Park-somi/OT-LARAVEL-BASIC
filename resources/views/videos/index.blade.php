<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                동영상
            </h2>
            <div class="flex">
                
            </div>
        </div>
    </x-slot>

    <div class="container p-5 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @foreach($videos as $video)
            <x-list-video-item :video=$video />
        @endforeach
    </div>
    
    <div class="flex justify-center p-5">
        {{ $videos->links() }}        
    </div>
</x-app-layout>