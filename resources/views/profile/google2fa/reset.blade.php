<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Google OTP 재설정') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">Google OTP 재설정</h2>
                    <p class="text-gray-600 mb-4">Google Authenticator 앱에서 아래 QR 코드를 스캔하여 OTP를 설정하세요.</p>

                    <!-- QR 코드 -->
                    <div>
                        {!! $QR_Image !!}
                    </div>

                    <form method="POST" action="{{ route('google2fa.verify') }}">
                        @csrf
                        <!-- OTP 입력 -->
                        <label for="otp" class="block text-gray-700 font-medium mb-2">OTP 코드 입력</label>
                        <input type="text" id="otp" class="border border-gray-300 rounded w-full px-3 py-2 mb-4" placeholder="123456">
                        
                        @error('otp')
                        <p class="text-xs text-red-500 mb-4"> {{ $message }} </p>
                        @enderror

                        <!-- 인증 버튼 -->
                        <button 
                            onclick="verification()"
                            type="submit"
                            class="bg-indigo-500 text-white px-4 py-2 rounded w-full hover:bg-indigo-600 transition">
                            OTP 인증하기
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>    
    function verification(){
        const otp = document.getElementById('otp').value;

        if(!otp){
            alert('OTP 코드를 입력해주세요.');
            return;
        }

        $.ajax({
            url: "{{ route('google2fa.verify') }}",
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: JSON.stringify({
                otp:otp
            }),
            dataType: 'json',
            success: function (data) {
                if(data.success){
                    alert(data.message);
                    window.location.href = "{{ route('profile.edit') }}";
                } else {
                    alert(data.message);
                }
            }
        })
    }
</script>