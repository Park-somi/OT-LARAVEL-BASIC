<x-app-layout>
    <x-slot name="header">  
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            글수정
        </h2>
    </x-slot>

    <div class="container p-5 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- get이나 post 이외의 메서드를 폼에다가 지정해줄 수 없음 -->
        <form id="article-form" action="{{ route('articles.update', ['article' => $article->id]) }}" method="POST" class="mt-5" enctype="multipart/form-data">
            @csrf
            <!-- <input type="hidden" name="_method" value="PUT"> PUT 메서드 지정 -->
            @method('PATCH')
            <div class="flex items-center mb-4">
                <label for="title" class="mr-6">제목</label>
                <input type="text" id="title" name="title" class="flex-grow p-3 border rounded" placeholder="제목을 입력하세요" value="{{ old('title') ?? $article->title }}">
            </div>
            <div class="flex items-start mb-4">
                <label for="body" class="mr-6 mt-2">내용</label>
                <div class="flex-grow">
                    <textarea id="body" name="body" class="block w-full p-3 border rounded h-60" placeholder="내용을 입력하세요">{{ old('body') ?? $article->body }}</textarea>
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
                <div id="file-list-container" class="flex-grow border border-gray-300 p-4 rounded-md bg-gray-50 mb-2">
                    <ul id="file-list" class="pl-0 text-gray-500">
                        @if ($files->isEmpty())
                            <li>아직 선택된 파일이 없습니다.</li>
                        @else
                            @foreach ($files as $file)
                                <li class="flex items-center justify-between px-2 py-2 existing-file" data-id="{{ $file->id }}">
                                    <span class="text-indigo-500">{{ $file->file_name }}</span>
                                    <button class="text-red-500 ml-4 remove-existing-file">x</button>
                                </li>
                            @endforeach
                        @endif
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
    document.addEventListener('DOMContentLoaded', () => {
        let selectedFiles = []; // 새로 추가된 파일들
        let filesToDelete = []; // 기존 파일들

        const fileInput = document.getElementById('file');
        const fileList = document.getElementById('file-list');

        // 기존 파일 삭제 (UI에서만)
        fileList.addEventListener('click', (event) => {
            if (event.target.classList.contains('remove-existing-file')) {
                const listItem = event.target.closest('.existing-file');
                const fileId = listItem.dataset.id;

                filesToDelete.push(fileId); // 삭제할 파일 ID 추가
                listItem.remove(); // UI에서 제거

                // 리스트가 비어 있으면 기본 메시지 표시
                if (fileList.children.length === 0) {
                    fileList.innerHTML = '<li>아직 선택된 파일이 없습니다.</li>';
                }
            }
        });

        // 새 파일 추가
        fileInput.addEventListener('change', (event) => {
            const files = Array.from(event.target.files); // 새로 선택한 파일들
            selectedFiles = selectedFiles.concat(files);

            // 기존 '아직 선택된 파일이 없습니다.' 메시지 제거
            if (fileList.children.length === 1 && fileList.children[0].textContent.includes('아직 선택된 파일이 없습니다.')) {
                fileList.innerHTML = '';
            }

            // 새로 추가된 파일 리스트 표시
            files.forEach((file, index) => {
                const listItem = document.createElement('li');
                listItem.className = 'flex items-center justify-between px-2 py-2 new-file';

                const fileName = document.createElement('span');
                fileName.textContent = file.name;
                fileName.className = 'text-indigo-500';

                const removeButton = document.createElement('button');
                removeButton.textContent = 'x';
                removeButton.className = 'text-red-500 ml-4 remove-new-file';
                removeButton.dataset.index = selectedFiles.length - files.length + index;

                listItem.appendChild(fileName);
                listItem.appendChild(removeButton);
                fileList.appendChild(listItem);
            });

            fileInput.value = ''; // 파일 입력 초기화
        });

        // 새로 추가된 파일 삭제
        fileList.addEventListener('click', (event) => {
            if (event.target.classList.contains('remove-new-file')) {
                const index = event.target.dataset.index;
                selectedFiles.splice(index, 1); // 새 파일 리스트에서 제거
                event.target.closest('li').remove(); // UI에서 제거

                // 리스트가 비어 있으면 기본 메시지 표시
                if (fileList.children.length === 0) {
                    fileList.innerHTML = '<li>아직 선택된 파일이 없습니다.</li>';
                }
            }
        });

        // 폼 제출 시 데이터 처리
        document.getElementById('article-form').addEventListener('submit', (event) => {
            event.preventDefault();

            const formData = new FormData(event.target);

            // 삭제할 파일 ID 추가
            filesToDelete.forEach((fileId) => formData.append('files_to_delete[]', fileId));

            // 새로 추가된 파일 추가
            selectedFiles.forEach((file) => formData.append('new_files[]', file));

            // 서버로 전송
            fetch(event.target.action, {
                method: 'POST',
                body: formData,
            }).then((response) => {
                if (response.ok) {
                    window.location.href = '/articles';
                } else {
                    response.text().then((text) => alert('Error: ' + text));
                }
            });
        });
    });


</script>