<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="p-3">
        <h2 class="text-lg font-bold text-gray-700 mb-4">Google OTP 인증</h2>
        <p class="text-gray-600 mb-4">
            Google Authenticator 앱에서 인증번호를 확인하고 아래에 입력하세요.
        </p>
        
        <form id="otp-form" method="POST" action="{{ route('google2fa.verify') }}">
            <div class="mb-4">
                @csrf
                <label for="otp" class="block text-gray-700">OTP 코드</label>
                <input type="text" id="otp" name="otp" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring" placeholder="123456" required>

                @error('otp')
                <p class="text-xs text-red-500 mt-4"> {{ $message }} </p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-indigo-500 text-white py-2 rounded hover:bg-indigo-600 transition">
                인증하기
            </button>
        </form>
    </div>
</x-guest-layout>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.getElementById('otp-form').addEventListener('submit', function (event) {
        event.preventDefault();
        
        const otp = document.getElementById('otp').value;
        console.log(otp);

        if (!otp) {
            alert('OTP 코드를 입력해주세요.');
            return;
        }

        $.ajax({
            url: "{{ route('google2fa.login.verify') }}",
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: JSON.stringify({ otp: otp }),
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    alert(data.message);
                    window.location.href = "{{ route('home') }}";
                } else {
                    alert(data.message);
                }
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    alert(xhr.responseJSON.message);
                } else {
                    alert('OTP 인증 중 문제가 발생했습니다. 다시 시도해주세요.');
                }
            }
        });
    });
</script>


