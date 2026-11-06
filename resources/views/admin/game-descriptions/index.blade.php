@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree lang-list">
        <div class="interface">
            <h1>Description</h1>
        </div>
        <div class="ui search">
            <div class="ui icon input">
                <input id="filterInput" class="prompt" type="text" placeholder="Type something">
                <i class="search icon"></i>
            </div>
        </div>
        <button class="add ui basic button add-text"><i class="plus icon"></i>add description</button>
        <div id="filterList" class="ui buttons fluid ordered list results">
            @foreach($gameDescriptions as $item)
                <a href="#" class="ui basic item label text @if($gameDescriptionLinked->where('game_description_id',$item->id)->first())sk @endif" id="{{$item->id}}">
                        <span class="elem">
                            <p>{{$item->description_en}}</p>
                            @php
                                $localisation = "description_ru-RU"
                            @endphp
                            <p class="hid">{{$item->$localisation}}</p>
                        </span>
                </a>
            @endforeach
        </div>
    </div>
    <div id="edit" class="ten wide column">

    </div>

    <script src="{{asset('js/search_result.js')}}"></script>


    <script>
        $(document).ready(function () {
            let checked = {{session("id") ?? "null"}};
            if (checked !== null) {
                $('#' + checked).addClass('check')
            }
            // add class && show button
            $('.form .main-local textarea').on('keyup', function () {
                if ($(this).val() !== '') {
                    $('.form').addClass('contentChanged')
                    $('.button.save, .button.discard').attr('disabled', false)
                } else {
                    $('.button.save').attr('disabled', true)
                }
            })
        })


        $("a.text").on('click', function () {
            $("a.text").removeClass('check');
            $(this).addClass('check')
            let id = $(this).attr('id');
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    closable: false,
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/game-description/edit/" + id,
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let text = $("#edit");
                                text.empty();
                                text.append(res);
                            }
                        })
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/game-description/edit/" + id,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let text = $("#edit");
                        text.empty();
                        text.append(res);
                    }
                })
            }
        })

        $('.add-text').on('click', function () {
            $.ajax({
                type: "get",
                url: "{{route('game-description-create')}}",
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    let text = $("#edit");
                    text.empty();
                    text.append(res);
                }
            })
        })


    </script>
@endsection
