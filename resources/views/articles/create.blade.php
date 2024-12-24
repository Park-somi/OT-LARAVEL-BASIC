<x-app-layout> <!-- 공통 laytout 적용 -->
    <x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            글쓰기
        </h2>
    </div>
    </x-slot>

    <div class="container p-5 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form id="article-form" action="/articles" method="POST" class="mt-5" enctype="multipart/form-data">
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
                <input type="file" id="file" name="file[]" class="flex-grow" multiple>
            </div>
            <div class="flex">
                <label class="mr-6 invisible">파일</label> <!-- 빈 공간 유지 -->
                <div id="file-list-container" class="flex-grow border border-gray-300 p-4 rounded-md bg-gray-50">
                    <ul id="file-list" class="pl-0 text-gray-500">
                        <li>아직 선택된 파일이 없습니다.</li>
                    </ul>
                </div>
            </div>
            <div class="flex mt-5">
                <button class="py-3 px-5 bg-gray-500 text-white rounded hover:bg-gray-700 ml-auto" type="submit">저장하기</button>
            </div>
        </form>
    </div>
</x-app-layout>
<script>
    let selectedFiles = []; // 선택된 파일 리스트 저장

    // 파일 선택 이벤트
    document.getElementById('file').addEventListener('change', (event) => {
        const files = Array.from(event.target.files); // 선택한 파일들 가져오기
        selectedFiles = selectedFiles.concat(files); // 기존 파일 목록에 추가
        updateFileList(); // 파일 목록 UI 업데이트
        event.target.value = ''; // 파일 입력 초기화
    });

    // 파일 리스트 업데이트
    function updateFileList() {
        const fileList = document.getElementById('file-list');
        fileList.innerHTML = ''; // 기존 리스트 초기화

        if (selectedFiles.length === 0) {
            // 파일이 없을 때 기본 메시지 표시
            const noFileMessage = document.createElement('li');
            noFileMessage.className = 'text-gray-500';
            noFileMessage.textContent = '아직 선택된 파일이 없습니다.';
            fileList.appendChild(noFileMessage);
        } else {
            // 파일 리스트 생성
            selectedFiles.forEach((file, index) => {
                const listItem = document.createElement('li');
                listItem.className = 'flex items-center justify-between px-2 py-2';

                // 파일 이름
                const fileName = document.createElement('span');
                fileName.className = 'text-indigo-500';
                fileName.textContent = file.name;

                // 삭제 버튼
                const removeButton = document.createElement('button');
                removeButton.textContent = 'x';
                removeButton.className = 'text-red-500 ml-4';
                removeButton.onclick = () => removeFile(index);

                listItem.appendChild(fileName);
                listItem.appendChild(removeButton);
                fileList.appendChild(listItem);
            });
        }
    }

    // 파일 삭제
    function removeFile(index) {
        selectedFiles.splice(index, 1); // 해당 파일 삭제
        updateFileList(); // 리스트 업데이트
    }

    // 폼 제출 시 추가된 파일만 서버에 전송
    document.getElementById('article-form').addEventListener('submit', (event) => {
        event.preventDefault(); // 기본 폼 제출 방지

        // FormData 객체 생성
        const formData = new FormData(event.target);

        // 기존 파일 필드를 제거하고 selectedFiles에 있는 파일을 추가
        selectedFiles.forEach((file) => formData.append('file[]', file));

        // FormData를 서버로 전송
        fetch(event.target.action, {
            method: 'POST',
            body: formData
        }).then((response) => {
            if (response.ok) {
                // 성공 시 페이지 이동
                window.location.href = "{{ route('articles.index') }}";
            } else {
                // 실패 시 에러 메시지 출력
                response.text().then((text) => alert(text));
            }
        });
    });
</script>