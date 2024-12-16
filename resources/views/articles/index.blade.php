<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                글목록
            </h2>
            <div class="flex">
                <form method="GET">
                    <div class="flex items-center">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button type="button" class="inline-flex items-center px-4 py-3 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-gray-100 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div id="searchTypeText">
                                        {{ request('type', '제목+내용') }}
                                    </div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link href="#" onclick="setSearchType(event, '제목+내용')">
                                    {{ __('제목+내용') }}
                                </x-dropdown-link>

                                <x-dropdown-link href="#" onclick="setSearchType(event, '제목')">
                                    {{ __('제목') }}
                                </x-dropdown-link>

                                <x-dropdown-link href="#" onclick="setSearchType(event, '작성자')">
                                    {{ __('작성자') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>

                        <!-- 검색창 -->
                        <input type="text" name="q" class="rounded border-gray-200 ml-4" placeholder="{{ $q ?? '검색' }}" value="{{ $q ?? '' }}" />

                        <!-- 정렬 타입을 저장할 hidden 필드 -->
                        <input type="hidden" name="sort" value="{{ request('sort', 'newest') }}">

                        <!-- 검색 타입을 저장할 hidden 필드 -->
                        <input type="hidden" name="type" id="searchTypeInput" value="{{ request('type', '제목+내용') }}">
                        
                        <!-- 검색 버튼 -->
                        <button type="submit" class="bg-indigo-500 text-white rounded px-4 py-2">
                            검색
                        </button>
                    </div>                
                </form>
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>
                                    @if ($sort === 'newest')
                                        최신순
                                    @elseif ($sort === 'oldest')
                                        오래된순
                                    @elseif ($sort === 'comments')
                                        댓글순
                                    @else
                                        최신순
                                    @endif
                                </div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('articles.index', ['q' => request('q'), 'sort' => 'newest', 'type' => request('type')])">
                                {{ __('최신순') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('articles.index', ['q' => request('q'), 'sort' => 'oldest', 'type' => request('type')])">
                                {{ __('오래된순') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('articles.index', ['q' => request('q'), 'sort' => 'comments', 'type' => request('type')])">
                                {{ __('댓글순') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="container p-5 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @foreach($articles as $article)
            <x-list-article-item :article=$article />
        @endforeach
    </div>
    
    <div class="flex justify-center p-5">
        {{ $articles->links() }}        
    </div>
</x-app-layout>
<script>
    function setSearchType(event, typeText) {
        event.preventDefault();

        document.getElementById('searchTypeText').textContent = typeText;

        document.getElementById('searchTypeInput').value = typeText;
    }
</script>