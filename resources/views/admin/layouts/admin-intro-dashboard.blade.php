<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>{{ config('app.name', 'Langly') }}</title>

    <link rel="stylesheet" href="{{asset("css/semantic.min.css")}}">
    <link rel="stylesheet" href="{{asset("css/main.css")}}">
    <script src={{asset("js/jquery-3.6.min.js")}} ></script>
    <script type="application/javascript" src={{asset("js/semantic.min.js")}}></script>
    <script type="application/javascript" src={{asset("js/main.js")}} ></script>
{{--    <script type="application/javascript" src={{asset("js/snowfall.min.js")}} ></script>--}}

    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset("css/filepond-plugin-image-preview.css")}}">
    <link rel="stylesheet" href="{{asset("css/filepond-plugin-image-edit.css")}}">

    @yield('embed')


</head>

<body>

@include("admin.includes.navbar-new")
<main>
    <div class="ui internally celled grid">
        <div class="row">
            @include("admin.includes.sidebar")
            @yield('content')
        </div>
    </div>
</main>


<div class="ui basic modal basic-error">
    <div class="ui icon header">
        <i class="massive exclamation triangle icon"></i>
    </div>
    <div class="content">
        <h2>All unsaved changes will be lost.</h2>
        <h3>Would you like to continue?</h3>
    </div>
    <div class="actions">
        <div class="ui red cancel inverted button">
            <i class="remove icon"></i>
            Stay
        </div>
        <div class="ui green ok inverted button" id="content-yes">
            <i class="checkmark icon"></i>
            Continue
        </div>
    </div>
</div>

<div class="ui basic modal delete-modal">
    <div class="ui icon header">
        <i class="massive trash alternate icon"></i>
    </div>
    <div class="content">
        <h2>This item will be permanently deleted.</h2>
        <H3>This action is irreversible!</H3>
    </div>
    <div class="actions">
        <div class="ui red cancel inverted button">
            <i class="remove icon"></i>
            Cancel
        </div>
        <div class="ui green ok inverted button" id="content-yes">
            <i class="checkmark icon"></i>
            Delete
        </div>
    </div>
</div>

<div class="ui basic modal discard-modal">
    <div class="ui icon header">
        <i class="massive exclamation circle icon"></i>
    </div>
    <div class="content">
        <h2>Your changes will be lost.</h2>
        <h3>Discard changes?</h3>
    </div>
    <div class="actions">
        <div class="ui red cancel inverted button">
            <i class="remove icon"></i>
            Cancel
        </div>
        <div class="ui green ok inverted button" id="content-yes">
            <i class="checkmark icon"></i>
            Discard
        </div>
    </div>
</div>

<div class="ui basic modal spell-modal">
    <div class="ui icon header">
        <i class="massive exclamation circle icon"></i>
    </div>
    <div class="content">
        <h2>Your audio file will be lost.</h2>
        <h3>Delete file?</h3>
    </div>
    <div class="actions">
        <div class="ui red cancel inverted button">
            <i class="remove icon"></i>
            Cancel
        </div>
        <div class="ui green ok inverted button" id="content-yes">
            <i class="checkmark icon"></i>
            Delete
        </div>
    </div>
</div>

<div class="ui basic modal state-visible">
    <div class="ui icon header">
        <i class="massive unlock alternate icon green"></i>
    </div>
    <div class="content">
        <h2>Make this item visible for users?</h2>
    </div>
    <div class="actions">
        <div class="ui red cancel inverted button">
            <i class="remove icon"></i>
            Cancel
        </div>
        <div class="ui green ok inverted button" id="content-yes">
            <i class="checkmark icon"></i>
            Confirm
        </div>
    </div>
</div>


<div class="ui basic modal state-hidden">
    <div class="ui icon header">
        <i class="massive lock alternate icon red"></i>
    </div>
    <div class="content">
        <h2>Make this item not visible for users?</h2>
    </div>
    <div class="actions">
        <div class="ui red cancel inverted button">
            <i class="remove icon"></i>
            Cancel
        </div>
        <div class="ui green ok inverted button" id="content-yes">
            <i class="checkmark icon"></i>
            Confirm
        </div>
    </div>
</div>
<script>
        var urls = window.location.href;
        var bar = $('nav.menu');

        if(urls.indexOf("localhost") > -1) {
            bar.append('<span>LOCALHOST</div>')
        }else if(urls.indexOf("stage") > -1){
            bar.append('<span>STAGING</div>')
        }else if(urls.indexOf("development") > -1){
            bar.append('<span>DEVELOPMENT</div>')
        }else if(urls.indexOf("release") > -1){
            bar.append('<span>TEMP_PRODUCTION</div>')
        }

</script>
{{--<script>--}}
{{--    //snow options--}}
{{--    --}}{{--snowFall.snow(document.body, {--}}
{{--    --}}{{--    round : true,--}}
{{--    --}}{{--    flakeCount : 50,--}}
{{--    --}}{{--    minSize: 3,--}}
{{--    --}}{{--    maxSize:15,--}}
{{--    --}}{{--    minSpeed: 1,--}}
{{--    --}}{{--    maxSpeed: 3,--}}
{{--    --}}{{--    image: '{{asset("img/snow.svg")}}'--}}
{{--    --}}{{--    //flakeColor: '#98ccf2'--}}
{{--    --}}{{--});--}}

{{--    // snow toggle--}}
{{--    // $('.ui.checkbox').checkbox({--}}
{{--    //     fireOnInit: true,--}}
{{--    //     onChecked: function(){--}}
{{--    //         $('.snowfall-flakes').hide()--}}
{{--    //     },--}}
{{--    //     onUnchecked: function(){--}}
{{--    //         $('.snowfall-flakes').show()--}}
{{--    //     }--}}
{{--    // });--}}

{{--    // remaining time til Xmas and salary--}}
{{--    // const countDownDate = new Date("Dec 31, 2022, 23:59:59").getTime();--}}
{{--    // const moneyDay = new Date("Dec 28, 2022, 11:00:00").getTime();--}}
{{--    //--}}
{{--    // const x = setInterval(function(){--}}
{{--    //     let now = new Date().getTime();--}}
{{--    //     let distance = countDownDate - now;--}}
{{--    //     let pay = moneyDay - now;--}}
{{--    //--}}
{{--    //     let days = Math.floor(distance / (1000*60*60*24));--}}
{{--    //     let hours = Math.floor((distance % (1000*60*60*24)) / (1000*60*60));--}}
{{--    //     let minutes = Math.floor((distance % (1000*60*60)) / (1000*60));--}}
{{--    //     let seconds = Math.floor((distance % (1000*60)) / 1000);--}}
{{--    //--}}
{{--    //     let daysPay = Math.floor(pay / (1000*60*60*24));--}}
{{--    //     let hoursPay = Math.floor((pay % (1000*60*60*24)) / (1000*60*60));--}}
{{--    //     let minutesPay = Math.floor((pay % (1000*60*60)) / (1000*60));--}}
{{--    //     let secondsPay = Math.floor((pay % (1000*60)) / 1000);--}}
{{--    //--}}
{{--    //     document.getElementById("count").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";--}}
{{--    //     document.getElementById("pay-day").innerHTML = daysPay + "d " + hoursPay + "h " + minutesPay + "m " + secondsPay + "s ";--}}
{{--    //     if(distance < 0){--}}
{{--    //         clearInterval(x);--}}
{{--    //         document.getElementById("count").innerHTML = "🎉🎉🎉2023🎉🎉"--}}
{{--    //     }--}}
{{--    //     if(pay < 0){--}}
{{--    //         clearInterval(x);--}}
{{--    //         document.getElementById("pay-day").innerHTML = "💰💰💰💰💰💰"--}}
{{--    //     }--}}
{{--    // }, 1000);--}}
{{--</script>--}}
<script src="{{asset('js/ckeditor.js')}}"></script>

<script src="{{asset('js/filepond-plugin-image-preview.js')}}"></script>
<script src="{{asset('js/filepond-plugin-image-exif-orientation.js')}}"></script>
<script src="{{asset('js/filepond-plugin-image-edit.js')}}"></script>
<script src="{{asset('js/filepond-plugin-file-validate-type.js')}}"></script>
<script src="{{asset('js/filepond.js')}}"></script>
@yield('scripts')
</body>

</html>
