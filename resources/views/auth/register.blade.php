<x-guest-layout>
    <form id="register_form" method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <div class="flex items-center mt-1">
                <x-text-input id="email" class="flex-grow" type="email" name="email" :value="old('email')" required autocomplete="email" />
                <button type="button" class="text-gray-500 bg-gray-100 rounded px-4 py-2 ml-2" onclick="sendEmailVerification()">{{ __('이메일인증') }}</button>
            </div>
            <div class="flex items-center mt-2">
                <x-text-input id="verify" class="flex-grow" name="verify" placeholder="이메일 인증번호" />
                <button type="button" class="text-gray-500 bg-gray-100 rounded px-6 py-2 ml-2" onclick="verification()" >{{ __('인증하기') }}</button>
            </div>
            <p id="email_success_msg" class="text-sm text-green-600 space-y-1 mt-2 hidden">인증 완료</p>
            <p id="email_error_msg" class="text-sm text-red-600 space-y-1 mt-2 hidden">인증 번호를 잘못 입력하셨습니다.</p>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="postcode" value="우편번호" />
            <div class="flex items-center mt-1">
                <x-text-input id="postcode" class="block" type="text" name="postcode" placeholder="우편번호" required autocomplete="address" />
                <button type="button" class="text-gray-500 bg-gray-100 rounded px-4 py-2 ml-2" onclick="DaumPostcode()">{{ __('주소찾기') }}</button>
            </div>
        </div>

        <div class="mt-4">
            <x-input-label for="address" value="주소" />
            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" placeholder="주소" required autocomplete="address" />
        </div>

        <div class="mt-4">
            <x-input-label for="detailAddress" value="상세주소" />
            <x-text-input id="detailAddress" class="block mt-1 w-full" type="text" name="detailAddress" placeholder="상세주소" required autocomplete="address" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script src="https://code.jquery.com/jquery-latest.min.js"></script>
<script>
    function sendEmailVerification(){
        const email = document.getElementById('email').value;
        if(!email){
            alert('이메일을 입력해주세요.');
            return;
        }
        $.ajax({
            url: "{{ route('users.email') }}",
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: JSON.stringify({
                email:email
            }),
            dataType: 'json',
            success: function(data){
                if(data.success){
                    alert('이메일 인증이 발송되었습니다.');
                } else {
                    alert('이메일 인증 발송에 실패하였습니다.');
                }
            },
            error: function(xhr, status, error){
                alert('이메일 인증 발송 오류');
            }
        });
    }

    function verification(){
        const code = document.getElementById('verify').value;
        if(!code){
            alert('인증 번호를 입력해주세요.');
            return;
        }
        $.ajax({
            url: "{{ route('users.verify') }}",
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: JSON.stringify({
                code:code
            }),
            dataType: 'json',
            success: function(data){
                if(data.success){
                    alert('인증이 완료되었습니다.');
                    document.getElementById('email_success_msg').classList.remove('hidden');
                    document.getElementById('email_error_msg').classList.add('hidden');
                } else {
                    alert('인증에 실패하였습니다.');
                    document.getElementById('email_success_msg').classList.add('hidden');
                    document.getElementById('email_error_msg').classList.remove('hidden');                    
                }
            },
            error: function(xhr, status, error){
                alert('인증 오류');
            }
        })
    }

    document.getElementById('register_form').addEventListener('submit', function(e){
        e.preventDefault();

        $.ajax({
            url: "{{ route('users.email_check') }}",
            method: 'get',
            headers: {
                'Content-Type': 'application/json'
            },
            dataType: 'json',
            success: function(data){
                if(data.is_verified){
                    e.target.submit();
                } else {
                    alert('이메일 인증을 완료해주세요.');
                }
            }
        })
    })

    function DaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var addr = ''; // 주소 변수

                //사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
                if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                    addr = data.roadAddress;
                } else { // 사용자가 지번 주소를 선택했을 경우(J)
                    addr = data.jibunAddress;
                }

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                document.getElementById('postcode').value = data.zonecode;
                document.getElementById("address").value = addr;
                // 커서를 상세주소 필드로 이동한다.
                document.getElementById("detailAddress").focus();
            }
        }).open();
    }
</script>

