<x-app-layout>
    <x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            동영상보기
        </h2>
    </div>
    </x-slot>
    
    <div class="container p-5 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex gap-6">

            <!-- 동영상 컨테이너 -->
            <div class="w-[70%]">
                <div id="video-title" class="text-2xl font-bold mt-2 mb-6 bg-gray-100 px-6 py-2 rounded-full inline-block">{{ $video->title }}</div>
                <video id="my-video" class="video-js vjs-big-play-centered"
                    data-setup='{"controls": true, "fluid": true, "autoplay": false, "muted": true, "playbackRates": [0.5, 1, 1.5, 2]}'
                    preload="auto" controlsList="nodownload">
                    <source src="{{ asset($full_path) }}" type="video/mp4">
                    브라우저가 동영상 태그를 지원하지 않습니다.
                </video>
            </div>

            <div class="w-[30%] p-4 mt-10 ml-5 border-l border-gray-500">
                <h2 class=" border rounded-lg p-2 text-lg font-bold mb-4 text-white bg-indigo-700 text-center">동영상 목록</h2>
                @foreach ($videos as $video)
                    <ul class="space-y-2">
                        <li class="border rounded bg-gray-100 hover:bg-indigo-100 p-2 text-center font-bold mt-5">
                            <button class="video-button" data-video-src="{{ asset($video->file_path) }}"  data-title="{{ $video->title }}">
                                <p class="text-lg">
                                    <i class="fa-regular fa-circle-check mr-1" style="color: green"></i>Laravel 실습
                                </p>
                                {{ $video->title }}
                            </button>
                        </li>
                        <!-- 필요에 따라 추가적인 목록 아이템을 추가할 수 있습니다 -->
                    </ul>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
<!-- Video.js CDN -->
<script src="https://vjs.zencdn.net/8.12.0/video.min.js"></script>
<!-- videojs-dynamic-watermark 플러그인 CDN -->
<script src="https://cdn.jsdelivr.net/npm/videojs-dynamic-watermark/dist/videojs-dynamic-watermark.min.js"></script>

<script>
    var player = videojs("my-video");

    // 모든 동영상 버튼에 클릭 이벤트 추가
    document.querySelectorAll('.video-button').forEach(button => {
        button.addEventListener('click', function(){
            // 선택한 동영상의 src와 제목 가져오기
            var videoSrc = this.getAttribute('data-video-src');
            var videoTitle = this.getAttribute('data-title');

            // 비디오 플레이어의 소스를 변경
            player.src({
                src: videoSrc,
                type: 'video/mp4'
            });
            player.load();

            // 제목 업데이트
            document.getElementById('video-title').textContent = videoTitle;
        })
    })
</script>
