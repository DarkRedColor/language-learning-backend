@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    {{--    <link rel="stylesheet" href="{{asset('css/lang.css')}}">--}}

    <div class="four wide column tree lang-list">
        <div class="interface">
            <h1>Welcome</h1>
{{--            <a href="{{route('text-export')}}" class="ui icon basic button exit export" type="button"><i--}}
{{--                        class="file excel icon"></i></a>--}}
{{--            <form method="post" action="{{route('text-import')}}" id="import" enctype="multipart/form-data">--}}
{{--                @csrf--}}
{{--                <label class="ui icon basic button upload-btn upload-btn-file">--}}
{{--                    <input type="file" class="import-text" name="import-file"--}}
{{--                           accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">--}}
{{--                    <i class="upload icon"></i></label>--}}
{{--            </form>--}}
        </div>

        <div class="ui search">
            <div class="ui icon input">
                <input class="prompt" type="text" placeholder="Search" id="filterInput">
                <i class="search icon"></i>
            </div>
        </div>
        <button class="add ui basic button add-text" id="add_text_button"><i class="plus icon"></i>Add new Welcome</button>

        <div class="ui buttons fluid ordered list results" id="filterList">
            @foreach($texts as $item)
                <a href="#" class="ui basic item label text type-{{$item->type}}" id="{{$item->id}}">
                    <span class="elem">
                    <p>{{$item->name_en}}</p>
                        @php
                            $ru = "name_ru-RU";
                        @endphp

                        <p class="hid">{{$item->$ru ?? null}}</p>

                    </span>
                </a>
            @endforeach
        </div>
    </div>

    <div id="edit" class="ten wide column">
        <div class="edit" id="text_modal_part">
        </div>
    </div>
    <script src="{{asset("js/search_result.js")}}" ></script>
    <script>

        $(document).ready(function () {
            let checked = {{session("id") ?? "null"}};
            if (checked !== null) {
                $('#' + checked).addClass('check')
                $.ajax({
                    type: "get",
                    url: "/admin/welcome/edit/" + checked,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let text = $("#text_modal_part");
                        text.empty();
                        text.append(res);
                    }
                })
            };
            // add Q / C for list
            $('#filterList > .item').each(function() {
                if($(this).hasClass('type-1')){
                    $(this).append('<span class="elem-quiz">Q<span/>')
                }else if($(this).hasClass('type-0')){
                    $(this).append('<span class="elem-chapter">C<span/>')
                }
            })
        })

        $('.item.text').on('click', function () {
            let id = $(this).attr('id');
            let button = $(this);
            if ($(".form").hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $('.item.text').removeClass('check')
                        button.addClass('check')
                        $.ajax({
                            type: "get",
                            url: "/admin/welcome/edit/" + id,
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let text = $("#text_modal_part");
                                text.empty();
                                text.append(res);
                            }

                        })
                    }
                }).modal('show')
            } else {
                $('.item.text').removeClass('check')
                button.addClass('check')
                $.ajax({
                    type: "get",
                    url: "/admin/welcome/edit/" + id,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let text = $("#text_modal_part");
                        text.empty();
                        text.append(res);
                    }
                })
            }
        })

        $('#add_text_button').on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/welcome/create",
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let text = $("#text_modal_part");
                                text.empty();
                                text.append(res);
                            }
                        })
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/welcome/create",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let text = $("#text_modal_part");
                        text.empty();
                        text.append(res);
                    }
                })
            }
        })

        $(document).ready(function () {
            $('.lang-list').animate({
                scrollTop: ($('.lang-list .buttons .ui.basic.label.check').offset().top - 150)
            }, 500);
        })

        // $("#text_search_input").on('keyup', function () {
        //     let text = $(this).val();
        //     if (text === '') {
        //         text = 'all';
        //     }
        //     let data = JSON.stringify({
        //         text
        //     })
        //     let results = $('#text_search_results')
        //     results.empty();
        //     $.ajax({
        //         type: "post",
        //         async: false,
        //         url: "/admin/text/search/results",
        //         data: data,
        //         contentType: 'application/json',
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         success: function (res) {
        //             results.append(res);
        //         }
        //     });
        // })

        $('.import-text').on('change', function () {
            $('#import').submit();
        })

    </script>

@endsection
