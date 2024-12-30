<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                글목록
            </h2>
            <div class="flex">
                <form method="GET">
                    <div class="flex items-center">
                        <!-- Excel 다운받기 -->
                        <a href="{{ route('download.excel')}}" class="bg-green-700 text-white rounded px-4 py-2 mr-4" style="float: right;">
                            Excel
                        </a>
                        <!-- Excel csv 다운받기 -->
                        <a href="{{ route('download.excel.csv')}}" class="bg-blue-700 text-white rounded px-4 py-2 mr-4" style="float: right;">
                            CSV
                        </a>
                        <a href="javascript:void(0);" onclick="toggleFilter()" style="float: right;" class="bg-gray-700 text-white rounded px-4 py-2 mr-4">
                            필터
                        </a>
                        <a href="{{ route('download.fastExcel')}}" style="float: right;" class="bg-yellow-500 text-white rounded px-4 py-2 mr-4">
                            FastExcel
                        </a>

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
                        <input type="text" name="q" class="rounded border-gray-200 ml-4" placeholder="{{ $q ?? '검색' }}" value="{{ $q ?? '' }}">

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

    <!-- 필터 창 -->
    <div id="filter-panel" class="absolute inset-0 sm:left-auto z-20 shadow-xl duration-200 ease-in-out translate-x-full" style="transition: transform 0.3s; background: #f8fafc; border-left: 1px solid #e5e7eb; width: 400px; height: 100vh; display: none;">
        <div class="flex items-center justify-between p-6 border-b border-gray-300">
            <!-- 필터 조건 -->
            <h2 class="text-xl font-bold">필터 조건</h2>
            <!-- 닫기 버튼 -->
            <button class="group p-2" onclick="toggleFilter()">
                <svg class="w-4 h-4 fill-gray-500 group-hover:fill-gray-700" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                    <path d="m7.95 6.536 4.242-4.243a1 1 0 1 1 1.415 1.414L9.364 7.95l4.243 4.242a1 1 0 1 1-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 0 1-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 0 1 1.414-1.414L7.95 6.536Z" />
                </svg>
            </button>
        </div>
        <div class="p-6">
            <form id="filtering-form" method="POST" action="{{ route('download.excel.filtering') }}">
                @csrf
                <div class="space-y-4">
                    <!-- 시작일 -->
                    <div>
                        <label class="block mb-2">시작일<span class="text-rose-500"> *</span></label>
                        <input type="date" id="start_date" name="start_date" class="form-input w-full" required>
                    </div>

                    <!-- 종료일 -->
                    <div>
                        <label class="block mb-2">종료일<span class="text-rose-500"> *</span></label>
                        <input type="date" id="end_date" name="end_date" class="form-input w-full" required>
                    </div>

                    <!-- 작성자 -->
                    <div>
                        <label class="block mb-2">작성자<span class="text-rose-500"> *</span></label>
                        <input type="text" id="author_name" name="author_name" class="form-input w-full" placeholder="작성자 이름을 입력하세요." required>
                    </div>

                    <!-- 제목 -->
                    <div>
                        <label class="block mb-2">제목<span class="text-rose-500"> *</span></label>
                        <input type="text" id="title" name="title" class="form-input w-full" placeholder="제목을 입력하세요." required>
                    </div>                    

                    <!-- 다운로드 형식 -->
                    <div>
                        <label class="block mb-2">다운로드 형식<span class="text-rose-500"> *</span></label>
                        <select id="download_type" name="download_type" class="form-select w-full">
                            <option value="excel" selected>Excel</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>

                    <!-- 다운로드 버튼 -->
                    <div class="mt-6 text-center">
                        <button id="download-btn" type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded w-full hover:bg-indigo-600">
                            다운로드
                        </button>
                        <div id="warningMessage" class="mt-2 text-red-500 hidden">
                            데이터가 50,000건 이상인 경우 Excel 형식으로 다운로드할 수 없습니다.
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="flex justify-center p-5">
        {{ $articles->links() }}        
    </div>
</x-app-layout>
<script src="https://code.jquery.com/jquery-latest.min.js"></script>
<script>
    function setSearchType(event, typeText) {
        event.preventDefault();

        document.getElementById('searchTypeText').textContent = typeText;

        document.getElementById('searchTypeInput').value = typeText;
    }

    function toggleFilter() {
        const filterPanel = document.getElementById('filter-panel');
        if (filterPanel.style.display === 'none' || filterPanel.style.display === '') {
            filterPanel.style.display = 'block';
            filterPanel.style.transform = 'translateX(0)';
        } else {
            filterPanel.style.transform = 'translateX(100%)';
            setTimeout(() => {
                filterPanel.style.display = 'none';

                // 필터 값 초기화
                document.getElementById('start_date').value = '';
                document.getElementById('end_date').value = '';
                document.getElementById('author_name').value = '';
                document.getElementById('title').value = '';
                document.getElementById('searchTypeInput').value = '';
                document.getElementById('searchTypeText').textContent = '제목+내용'; // 기본값 설정
            }, 300); // 애니메이션 시간과 일치
        }
    }

    // 시작 날짜 변경 시 종료 날짜의 최소값 설정
    document.getElementById('start_date').addEventListener('change', function () {
        const startDate = this.value;
        const endDateInput = document.getElementById('end_date');

        // 종료 날짜의 최소값을 시작 날짜로 설정
        endDateInput.min = startDate;

        // 현재 설정된 종료 날짜가 시작 날짜보다 이전이면 종료 날짜 초기화
        if (endDateInput.value < startDate) {
            endDateInput.value = '';
        }
    });

    // 종료 날짜 변경 시 시작 날짜의 최대값 설정
    document.getElementById('end_date').addEventListener('change', function () {
        const endDate = this.value;
        const startDateInput = document.getElementById('start_date');

        // 시작 날짜의 최대값을 종료 날짜로 설정
        startDateInput.max = endDate;

        // 현재 설정된 시작 날짜가 종료 날짜보다 이후면 시작 날짜 초기화
        if (startDateInput.value > endDate) {
            startDateInput.value = '';
        }
    });

    document.getElementById('download-btn').addEventListener('click', function(e) {
        e.preventDefault(); // 기본 동작 중단

        const form = document.getElementById('filtering-form'); // form 객체 가져오기
        const start_date = document.getElementById('start_date').value;
        const end_date = document.getElementById('end_date').value;
        const author_name = document.getElementById('author_name').value;
        const title = document.getElementById('title').value;
        const download_type = document.getElementById('download_type').value;

        if(start_date == ''){
            e.preventDefault();
            alert('시작날짜를 정하세요.');
            document.getElementById('start_date').focus();
            return false ;
        }

        if(end_date == ''){
            e.preventDefault();
            alert('종료날짜를 정하세요.');
            document.getElementById('end_date').focus();
            return false ;
        }

        if(author_name == ''){
            e.preventDefault();
            alert('작성자를 입력하세요.');
            document.getElementById('author_name').focus();
            return false ;
        }

        if(title == ''){
            e.preventDefault();
            alert('제목을 입력하세요.');
            document.getElementById('title').focus();
            return false ;
        }

        if (download_type === 'excel') {
            $.ajax({
                url: "{{ route('excel.data_count') }}",
                method: 'GET',
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    author_name: author_name,
                    title: title
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(data){
                    if(data.count > 50000){
                        document.getElementById('warningMessage').style.display = 'block';
                        return false;
                    } else {
                        document.getElementById('warningMessage').style.display = 'none';
                        form.submit();
                    }
                }
            });
        } else {
            document.getElementById('warningMessage').style.display = 'none';
            form.submit();
        }
    });
</script>