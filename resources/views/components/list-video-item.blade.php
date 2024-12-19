<div class="background-white border rounded mb-3 p-5 flex justify-between">
    <div>
        <!-- 줄바꿈을 적용하여 출력 -->
        <!-- nl2br을 사용하여 텍스트의 줄바꿈 문자를 <br> 태그로 변환 -->
        <p class="text-lg flex items-center">
            <a href="{{ route('videos.show', ['video' => $video->id]) }}" target="_blank">
                <span>{{ $video->title }}</span>
            </a>
                <span class="text-sm text-red-500 ml-2 bg-red-100 px-1 rounded">@if($video->is_recent) new @endif</span>
        </p>
        <p class="text-gray-500 mt-1">
            <a href="{{ route('profile', ['user' => $video->user->username]) }}">{{ $video->user->name }}</a>
        </p>
        <p class="text-xs text-gray-500">
            {{ $video->created_at->diffForHumans() }}
        </p>
    </div>
</div>