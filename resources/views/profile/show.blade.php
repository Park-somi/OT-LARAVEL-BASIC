<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="text-center">
                <h1 class="text-center text-2xl font-bold"> {{ $user->name }} </h1>
                <div>
                    게시글 {{ $user->articles->count() }}
                    구독자 {{ $user->followers->count() }}
                </div>
                <!--
                    로그인되어있고, 
                    로그인한 사용자와 프로필 주인과 다른 경우 버튼 생성 
                -->
                @if(Auth::check() && Auth::id() != $user->id)
                    <div>
                        <!-- 로그인되어있고, 팔로워하고 있다면 -->
                        <!-- 
                            로그인여부 확인 후 true면 조건문 실행
                            false면 조건문 전체가 실행이 되지 않음
                        -->
                        @if(Auth::check() && Auth::user()->isFollowing($user))
                            <form method="POST" action="{{ route('unfollow', ['user' => $user->username]) }}">
                                @csrf
                                @method('delete')
                                <x-danger-button class="mt-2">구독해지</x-danger-button>
                            </form>
                        <!-- 팔로워하고 있지 않다면 -->
                        @else
                            <form method="POST" action="{{ route('follow', ['user' => $user->username]) }}">
                                @csrf
                                <x-primary-button class="mt-2">구독하기</x-primary-button>
                            </form>
                        @endif
                    </div>
                @endif
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
