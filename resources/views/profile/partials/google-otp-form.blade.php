<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Google OTP 활성화') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("계정을 더 안전하게 보호하기 위해 Google OTP를 설정하세요.") }}
        </p>
        <div class="mt-6 space-y-6">
            <div class="flex items-center">
                <!-- Google OTP 활성화 버튼 -->
                <x-input-label for="update_password_current_password" id="otp-status" :value="__('활성화 여부')" />
                <label class="relative inline-flex items-center cursor-pointer ml-3">
                    <input id="google-otp-toggle" type="checkbox" class="sr-only peer" {{ $user->is_google2fa_enabled ? 'checked' : '' }} {{ is_null($user->google2fa_secret) ? 'disabled' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:bg-indigo-500 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed"></div>
                    <span class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition peer-checked:translate-x-5 peer-disabled:cursor-not-allowed"></span>
                </label>

                @if(!$user->is_google2fa_enabled)
                <!-- Google OTP 설정 버튼 -->
                <a href="{{ route('google2fa.create')}}">
                    <div class="text-indigo-500 text-sm px-4 py-3 rounded hover:text-indigo-600 transition">Google OTP 설정</div>
                </a>
                @endif

                @if($user->is_google2fa_enabled)
                <!-- Google OTP 재설정 버튼 -->
                <a href="{{ route('google2fa.reset')}}">
                    <div class="text-indigo-500 text-sm px-4 py-3 rounded hover:text-indigo-600 transition">Google OTP 재설정</div>
                </a>
                @endif
            </div>
        </div>
    </header>
</section>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.getElementById('google-otp-toggle').addEventListener('change', function () {
        const isEnabled = this.checked;

        $.ajax({
            url: "{{ route('google2fa.toggle') }}",
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: JSON.stringify({
                is_google2fa_enabled: isEnabled
            }),
            dataType: 'json',
            success: function (data){
                if (data.success) {
                    console.log('Google OTP 활성화 여부가 업데이트되었습니다.');
                } else {
                    console.log('상태 업데이트 중 오류가 발생했습니다.');
                }
            }
        })
    })
</script>