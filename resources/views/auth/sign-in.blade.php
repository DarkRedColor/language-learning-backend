@extends("layouts.racoon")
@section("content")
    <div class="login-wrap">
        <div class="login-panel">
            <object data="{{ asset('img/racoon.svg') }}" type="image/svg+xml"></object>
            <a onclick="getAuthCode();" class="login-btn">Log in</a>
        </div>
    </div>
@endsection

@section("exec-js")
    <script>
        var client;
        function initClient() {
            client = google.accounts.oauth2.initCodeClient({
                client_id: '{{ config('values.google_oauth_client_id') }}',
                scope: 'https://www.googleapis.com/auth/userinfo.email',
                ux_mode: 'popup',
                callback: (response) => {
                    const codeReceiverUri = '{{ route('post-login') }}'
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', codeReceiverUri, true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.onload = function() {
                        let response = JSON.parse(xhr.responseText)
                        if (response.redirectUri){
                            window.location.href = response.redirectUri
                        }
                    };
                    xhr.send('code=' + response.code + '&_token={{ csrf_token() }}');
                },
            });
        }
        function getAuthCode() {
            client.requestCode();
        }
    </script>
    <script src="https://accounts.google.com/gsi/client" onload="initClient()" async defer></script>
@endsection