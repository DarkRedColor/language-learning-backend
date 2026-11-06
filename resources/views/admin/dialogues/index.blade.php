@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree lang-list">
        <h1>Dialogues</h1>
        <div class="ui search">
            <div class="ui icon input">
                <input id="filterInput" class="prompt" type="text" placeholder="Search ...">
                <i class="search icon"></i>
            </div>
        </div>
        <button class="add ui basic button add-text"><i class="plus icon"></i>add dialog</button>
        <div id="filterList" class="character-list ui buttons fluid ordered list results">
            @foreach($dialogue as $item)
                <div class="char ui left labeled button dialogue-button" id="{{$item->id}}">
                    <a href="{{route('dialogue-page-index',[$item->id])}}" class="ui basic item label"
                       id="dialogue-button" data-value="">
                        <p>{{$item->name_en}}</p>
                    </a>
                    <button class="ui icon button dialogue_edit_button" data-value="{{$item->id}}">
                        <i class="edit icon"></i>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
    <div id="edit" class="ten wide column">

    </div>
    <script type="application/javascript" src={{asset("js/search_result.js")}} ></script>
    <script>
        $(document).ready(function () {
            let checked = {{session("id") ?? "null"}};
            if (checked != null) {
                $('#' + checked).addClass('check')
            }
            $('.lang-list').animate({
                scrollTop: ($('.ui.left.labeled.button.check').offset().top - 150)
            }, 500);
        })

        $(".dialogue-button").on('click', function () {
            $(".dialogue-button").removeClass('check');
            $(this).addClass('check');
        })
        $('.add').on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "{{route('dialogue-create')}}",
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let form = $("#edit");
                                form.empty();
                                form.append(res)
                            },
                        });
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "{{route('dialogue-create')}}",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $("#edit");
                        form.empty();
                        form.append(res)
                    },
                });
            }
        })


        $('.dialogue_edit_button').on('click', function () {
                let id = $(this).attr('data-value');
                if ($('form').hasClass('contentChanged')) {
                    $('.basic-error').modal({
                        onApprove: function () {
                            $.ajax({
                                type: "get",
                                url: "/admin/dialogue/edit/" + id,
                                contentType: 'application/json',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (res) {
                                    let form = $("#edit");
                                    form.empty();
                                    form.append(res)
                                },
                            });
                        }
                    }).modal('show');
                } else {
                    $.ajax({
                        type: "get",
                        url: "/admin/dialogue/edit/" + id,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            let form = $("#edit");
                            form.empty();
                            form.append(res)
                        },
                    });
                }
            }
        )
    </script>

@endsection