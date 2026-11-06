@extends('admin.layouts.admin-intro-dashboard')

@section('content')

    <div class="four wide column tree" id="arcade_store_lists">
    <input type="hidden" value="{{$id}}" id="dialogue_sub_chapter_id">
        <h1 id="adventure-h">Arcade Games</h1>
        <div class="ui small breadcrumb" id="arcade_path">
            <a href="{{route('arcade-games')}}"><i class="arrow left icon"></i></a>
            <a href="{{route('arcade-games')}}" class="section">Arcade Games</a>
            <i class="right chevron icon divider"></i>
            <a href="#" class="section">
                @if (\App\Models\ArcadeGame::find($id)->type == 0)
                    Type Fast
                @else
                    Find Words
                @endif</a>

        </div>
        <div class="ui search">
            <div class="ui icon input">
                <input class="prompt" type="text" placeholder="Search ..." id="filterInput">
                <i class="search icon"></i>
            </div>
        </div>
        <div class="arcade-list ui buttons fluid results" id="filterList">
            <button class="add ui basic button" id="add_new_arcade_game_type" data-value="1">
                <i class="plus icon"></i>add new
            </button>
            @foreach($list as $item)
                @switch($item->type)
                    @case(0)
                        <div class="ui left labeled button season adv intro" data-value="{{$item->id}}" id="">
                            <a href="#"
                               class="ui basic label">
                                <span style="background-color: var(--intro)">I{{$item->order + 1}}</span>
                                <p>Intro</p>
                            </a>
                            <button class="ui icon button intro_edit_button dialogue-button" data-value="{{$item->id}}">
                                <i class="edit outline icon"></i>
                            </button>
                            <button class="negative ui icon button arcade_storage_delete_button"
                                    data-value="{{$item->id}}">
                                <i class="trash alternate icon"></i>
                            </button>
                        </div>
                        @break
                    @case(1)
                        <div class="ui left labeled button season adv notification" data-value="{{$item->id}}" id="">
                            <a href="#"
                               class="ui basic label">
                                <span style="background-color: var(--notification)">N{{$item->order + 1}}</span>
                                <p>Notification @if($item->add_type == 0)
                                        _Standard
                                    @else
                                        _Tutorial
                                    @endif</p>
                            </a>
                            <button class="ui icon button notification_edit_button dialogue-button" data-value="{{$item->id}}">
                                <i class="edit outline icon"></i>
                            </button>
                            <button class="negative ui icon button arcade_storage_delete_button"
                                    data-value="{{$item->id}}">
                                <i class="trash alternate icon"></i>
                            </button>
                        </div>
                        @break

                    @case(2)
                        <div class="ui left labeled button season adv end-game" data-value="{{$item->id}}" id="">
                            <a href="#"
                               class="ui basic label">
                                <span style="background-color: var(--end-game)">E{{$item->order + 1}}</span>
                                <p>End Game</p>
                            </a>
                            <button class="ui icon button end_game_edit_button dialogue-button" data-value="{{$item->id}}">
                                <i class="edit outline icon"></i>
                            </button>
                            <button class="negative ui icon button arcade_storage_delete_button"
                                    data-value="{{$item->id}}">
                                <i class="trash alternate icon"></i>
                            </button>
                        </div>
                        @break

                    @case(3)
                        <div class="ui left labeled button  choices adv" id="" data-value="{{$item->id}}">
                            <a href="{{route('arcade-games-choices-list')}}" class="ui basic label">
                                <span>C{{$item->order + 1}}</span>
                                <p>Choice</p>
                            </a>
                            <button class="ui icon button choices_edit_button dialogue-button" data-value="{{$item->id}}">
                                <i class="edit outline icon"></i>
                            </button>
                            <button class="negative ui icon button arcade_storage_delete_button"
                                    data-value="{{$item->id}}">
                                <i class="trash alternate icon"></i>
                            </button>
                        </div>
                        @break

                    @case(4)
                        <div class="ui left labeled button season adv win-alert" data-value="{{$item->id}}" id="">
                            <a href="#"
                               class="ui basic label">
                                <span style="background-color: var(--win-alert)">W{{$item->order + 1}}</span>
                                <p>Win Alert @if($item->add_type == 0)
                                        _Standard
                                    @else
                                        _Tutorial
                                    @endif</p>
                            </a>
                            <button class="ui icon button win_alert_edit dialogue-button" data-value="{{$item->id}}">
                                <i class="edit outline icon"></i>
                            </button>
                            <button class="negative ui icon button arcade_storage_delete_button"
                                    data-value="{{$item->id}}">
                                <i class="trash alternate icon"></i>
                            </button>
                        </div>
                        @break

                @endswitch
            @endforeach

        </div>
    </div>
    <div id="edit" class="ten wide column">
        <div class="edit" id="arcade_storage_edit_create_zone">

        </div>
    </div>
    </button>
    <script src="{{asset("js/search_result.js")}}"></script>
    <script>
        $("#add_new_arcade_game_type").on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "{{route('arcade-page-create')}}",
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let form = $("#arcade_storage_edit_create_zone");
                                form.empty();
                                form.append(res)
                            },
                        });
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "{{route('arcade-page-create')}}",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $("#arcade_storage_edit_create_zone");
                        form.empty();
                        form.append(res)
                    },
                });
            }
        })
        $('button.dialogue-button').on('click', function () {
            let parent = $(this).parent();
            let id = parent.attr('data-value');

            let content_type = "win-alert"
            let editAria = $('#arcade_storage_edit_create_zone')
            if (parent.hasClass('intro')) {
                content_type = "intro"
            }
            if (parent.hasClass('end-game')) {
                content_type = "end-game"
            }
            if (parent.hasClass('notification')) {
                content_type = "notification"
            }
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/arcade-games/page/create-and-edit/" + content_type + '/' + id,
                            contentType: 'application/json',
                            async: true,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                editAria.empty();
                                editAria.append(res);
                            }
                        });
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/arcade-games/page/create-and-edit/" + content_type + '/' + id,
                    contentType: 'application/json',
                    async: true,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        editAria.empty();
                        editAria.append(res);
                    }
                });
            }
        })
        // $(".intro_edit_button").on('click', function () {
        //     let episodeId = $(this).attr('data-value');
        //     if ($('.form').hasClass('contentChanged')) {
        //         $('.basic-error').modal({
        //             onApprove: function () {
        //                 $.ajax({
        //                     type: "get",
        //                     url: "/admin/arcade-games/list/intro-edit",
        //                     contentType: 'application/json',
        //                     headers: {
        //                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                     },
        //                     success: function (res) {
        //                         let form = $("#arcade_storage_edit_create_zone");
        //                         form.empty();
        //                         form.append(res)
        //                     },
        //                 });
        //             }
        //         }).modal('show')
        //     } else {
        //         $.ajax({
        //             type: "get",
        //             url: "/admin/arcade-games/list/intro-edit",
        //             contentType: 'application/json',
        //             headers: {
        //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //             },
        //             success: function (res) {
        //                 let form = $("#arcade_storage_edit_create_zone");
        //                 form.empty();
        //                 form.append(res)
        //             },
        //         });
        //     }
        // })
        // $(".notification_edit_button").on('click', function () {
        //     let episodeId = $(this).attr('data-value');
        //     if ($('.form').hasClass('contentChanged')) {
        //         $('.basic-error').modal({
        //             onApprove: function () {
        //                 $.ajax({
        //                     type: "get",
        //                     url: "/admin/arcade-games/list/notification-edit",
        //                     contentType: 'application/json',
        //                     headers: {
        //                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                     },
        //                     success: function (res) {
        //                         let form = $("#arcade_storage_edit_create_zone");
        //                         form.empty();
        //                         form.append(res)
        //                     },
        //                 });
        //             }
        //         }).modal('show')
        //     } else {
        //         $.ajax({
        //             type: "get",
        //             url: "/admin/arcade-games/list/notification-edit",
        //             contentType: 'application/json',
        //             headers: {
        //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //             },
        //             success: function (res) {
        //                 let form = $("#arcade_storage_edit_create_zone");
        //                 form.empty();
        //                 form.append(res)
        //             },
        //         });
        //     }
        // })
        // $(".end_game_edit_button").on('click', function () {
        //     let episodeId = $(this).attr('data-value');
        //     if ($('.form').hasClass('contentChanged')) {
        //         $('.basic-error').modal({
        //             onApprove: function () {
        //                 $.ajax({
        //                     type: "get",
        //                     url: "/admin/arcade-games/list/end-game-edit",
        //                     contentType: 'application/json',
        //                     headers: {
        //                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                     },
        //                     success: function (res) {
        //                         let form = $("#arcade_storage_edit_create_zone");
        //                         form.empty();
        //                         form.append(res)
        //                     },
        //                 });
        //             }
        //         }).modal('show')
        //     } else {
        //         $.ajax({
        //             type: "get",
        //             url: "/admin/arcade-games/list/end-game-edit",
        //             contentType: 'application/json',
        //             headers: {
        //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //             },
        //             success: function (res) {
        //                 let form = $("#arcade_storage_edit_create_zone");
        //                 form.empty();
        //                 form.append(res)
        //             },
        //         });
        //     }
        // })
        // $(".choices_edit_button").on('click', function () {
        //     let episodeId = $(this).attr('data-value');
        //     if ($('.form').hasClass('contentChanged')) {
        //         $('.basic-error').modal({
        //             onApprove: function () {
        //                 $.ajax({
        //                     type: "get",
        //                     url: "/admin/arcade-games/choices-list/choice-edit",
        //                     contentType: 'application/json',
        //                     headers: {
        //                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                     },
        //                     success: function (res) {
        //                         let form = $("#arcade_storage_edit_create_zone");
        //                         form.empty();
        //                         form.append(res)
        //                     },
        //                 });
        //             }
        //         }).modal('show')
        //     } else {
        //         $.ajax({
        //             type: "get",
        //             url: "/admin/arcade-games/choices-list/choice-edit",
        //             contentType: 'application/json',
        //             headers: {
        //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //             },
        //             success: function (res) {
        //                 let form = $("#arcade_storage_edit_create_zone");
        //                 form.empty();
        //                 form.append(res)
        //             },
        //         });
        //     }
        // })
        // $(".win_alert_edit").on('click', function () {
        //     let episodeId = $(this).attr('data-value');
        //     if ($('.form').hasClass('contentChanged')) {
        //         $('.basic-error').modal({
        //             onApprove: function () {
        //                 $.ajax({
        //                     type: "get",
        //                     url: "/admin/arcade-games/list/win-alert-edit",
        //                     contentType: 'application/json',
        //                     headers: {
        //                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                     },
        //                     success: function (res) {
        //                         let form = $("#arcade_storage_edit_create_zone");
        //                         form.empty();
        //                         form.append(res)
        //                     },
        //                 });
        //             }
        //         }).modal('show')
        //     } else {
        //         $.ajax({
        //             type: "get",
        //             url: "/admin/arcade-games/list/win-alert-edit",
        //             contentType: 'application/json',
        //             headers: {
        //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //             },
        //             success: function (res) {
        //                 let form = $("#arcade_storage_edit_create_zone");
        //                 form.empty();
        //                 form.append(res)
        //             },
        //         });
        //     }
        // })

        $('.arcade_storage_delete_button').on('click', function() {
            let episodeId = $(this).attr('data-value');
            $('.delete-modal').modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        url: "/admin/arcade-games/page/delete/"+episodeId,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            location.reload();
                        }
                    });
                }
            }).modal('show')
        })
    </script>

@endsection