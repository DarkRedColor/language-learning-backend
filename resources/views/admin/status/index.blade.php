@section('embed')
    <script src="https://unpkg.com/vue@3.3.4"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="{{asset('css/status.css')}}">
@endsection

@extends('admin.layouts.admin-intro-dashboard')
@section('content')

    @include('admin.status.status-list')
    <script>
        const email = "{{config('values.python_login')}}";
        const password = "{{config('values.python_password')}}";
    </script>
    <script src="{{asset("js/search_result.js")}}"></script>

    <script>
        function removeImg() {
            $('.previewImg').attr('src', '')
        }
        $('.ui.accordion').accordion({
            selector:{
                trigger: '.main-local .lang-iso'
            }
        });
        let timestamp;
        setInterval(() => {
            timestamp = new Date().getTime();
        }, 1000);

        // ====== endpoints
        const pageUrls = window.location.href;
        let logIn, status, sort, imageRequest;

        if (pageUrls.indexOf("localhost") > -1 || pageUrls.indexOf("stage") > -1) {
            // ===== DEV server
            console.log("DEV")
            logIn = 'https://prod-auth.langly.ai/api/accounts/login/';
            status = 'https://prod-energy.langly.ai/api/energy/status-settings';
            sort = 'https://prod-energy.langly.ai/api/energy/status-reindexing';
            imageRequest = 'https://stage.langly.ai/api/status-media/save';
        } else if (pageUrls.indexOf("langly-api-development-development") > -1) {
            // ===== RC server
            console.log("RC")
            // logIn = 'https://auth-microservice-release-candidate.langly.ai/api/accounts/login/';
            // status = 'https://socks-microservice-release-candidate.langly.ai/api/energy/status-settings';
            // sort = 'https://socks-microservice-release-candidate.langly.ai/energy/status-reindexing';
            logIn = 'https://prod-auth.langly.ai/api/accounts/login/';
            status = 'https://prod-energy.langly.ai/api/energy/status-settings';
            sort = 'https://prod-energy.langly.ai/api/energy/status-reindexing';
            imageRequest = 'https://langly-api-development-development.azurewebsites.net/api/status-media/save';
        } else if (pageUrls.indexOf("api") > -1) {
            // ===== PROD server
            console.log("PROD")
            logIn = 'https://auth-microservice-production.langly.ai/api/accounts/login/';
            status = 'https://socks-microservice-production.langly.ai/api/energy/status-settings';
            sort = 'https://socks-microservice-production.langly.ai/energy/status-reindexing';
            imageRequest = 'https://api.langly.ai/api/status-media/save';
        }
    </script>
    <script src="{{asset("js/status.js")}}"></script>
@endsection


