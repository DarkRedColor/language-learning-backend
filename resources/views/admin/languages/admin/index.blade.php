@extends('admin.layouts.admin-intro-dashboard')

@section('content')

{{--    <link rel="stylesheet" href="{{asset('css/lang.css')}}">--}}

    <div class="four wide column tree lang-list">
        <h1>Languages</h1>
        <div class="ui buttons fluid">
            @foreach($languages as $item)
                <a href="#" class="ui basic label langs" data-value="{{$item->id}}">
                    <p>{{$item->display_name}}</p>
                </a>
            @endforeach
                @if (Auth::user()->hasRole(['admin','qa','project','developer']))
            <button class="add ui basic button" id="add_language"><i class="plus icon"></i>add new language</button>
                @endif

        </div>
    </div>
    <div id="edit" class="ten wide column">
        <div class="edit" id="modal_langs_aria">

        </div>
    </div>

    <script>

        $(".langs").on('click', function () {
            let id = $(this).attr('data-value');
            $('.check').removeClass("check")
            $(this).addClass('check');
            $.ajax({
                type: "get",
                url: "/admin/language/edit/modal/" + id,
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    let createAria = $("#modal_langs_aria");
                    createAria.empty();
                    createAria.append(res)
                },
            });
        });

        $('#add_language').on('click',function (){
            $.ajax({
                type: "get",
                url: "/admin/language/create/modal",
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    let createAria = $("#modal_langs_aria");
                    createAria.empty();
                    createAria.append(res)
                },
            });
        })

    </script>

@endsection
