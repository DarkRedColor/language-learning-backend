@extends('admin.layouts.admin-intro-dashboard')

@section('content')

    {{--    <link rel="stylesheet" href="{{asset('css/lang.css')}}">--}}


    <div class="four wide column tree">
        <h1>Languages</h1>
        <div class="ui buttons fluid">
            @foreach( $languages as $item)
                @if($item->lcid <> "en")
                    <div class="ui left labeled button lang">
                        <a href="#" class="ui basic label language_edit_button" data-value="{{$item->id}}">
                            @if($item->flag == null)
                                <img class="previewImg_" src="{{asset('img_placeholder.svg')}}">
                            @else
                                <img class="previewImg_" src="{{Storage::disk('gcs')->url("language/icon/$item->lcid.png")}}">
                            @endif
                            <p>{{$item->display_name}}</p>
                        </a>
                        @if($item->is_displayed_to_user_web == 0)
                            <button class="lock ui icon button lockbuttonweb" style="color: darkred" id="{{$item->id}}">
                                <i class="lock alternate icon"></i>
                            </button>
                        @else
                            <button class="un-lock ui icon button unlockbuttonweb" style="color: darkgreen" id="{{$item->id}}">
                                <i class="unlock alternate icon"></i>
                            </button>
                        @endif
                        @if($item->is_displayed_to_user == 0)
                            <button class="lock ui icon button lockbutton" id="{{$item->id}}">
                                <i class="lock alternate icon"></i>
                            </button>
                        @else
                            <button class="un-lock ui icon button unlockbutton" id="{{$item->id}}">
                                <i class="unlock alternate icon"></i>
                            </button>
                        @endif
                        {{--                        @if(Auth::check() && Auth::user()->hasRole('admin'))--}}
                        <button class="lock ui icon button language_delete_button  @if(!Auth::user()->hasRole('admin'))lena
                @endif" data-value="{{$item->id}}">
                            <i class="trash alternate icon"></i>
                        </button>
                        {{--                        @endif--}}
                    </div>
                @endif
            @endforeach
                @if (Auth::user()->hasRole(['admin','qa','project','developer']))
                <button class="add ui basic button" id="add_new_existence_language_button"><i class="plus icon"></i>add
                    new language
                </button>
            @endif
        </div>
    </div>
    <div id="edit" class="ten wide column">
        <div class="edit" id="add_new_existence_language">

        </div>
    </div>

    <script>

        $("div .lang").on('click', function () {
            $("div .lang").removeClass('check')
            $(this).addClass("check");
        })


        $('#add_new_existence_language_button').on('click', function () {
            $.ajax({
                type: "get",
                url: "/admin/main/menu/language/create/modal",
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    let createAria = $("#add_new_existence_language");
                    createAria.empty();
                    createAria.append(res)
                },
            });
        })

        $('.language_edit_button').on('click', function () {
            let id = $(this).attr('data-value')
            if ($("#main_menu_language_edit").hasClass("contentChanged")) {
                $('.discard-modal').modal({
                    onApprove: function () {
                        langAjax(id)
                    }
                }).modal('show')
            } else {
                langAjax(id)
            }
        })

        function langAjax(id) {
            $.ajax({
                type: "get",
                url: "/admin/main/menu/language/edit/modal/" + id,
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    let createAria = $("#add_new_existence_language");
                    createAria.empty();
                    createAria.append(res)
                },
            });
        }

        $(".language_delete_button:not(.lena)").on('click', function () {
            let id = $(this).attr('data-value');
            $('.delete-modal').modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        url: "/admin/main/menu/language/delete/" + id,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            location.reload();
                        },
                    });
                }
            }).modal('show');
        })

    </script>

@endsection
