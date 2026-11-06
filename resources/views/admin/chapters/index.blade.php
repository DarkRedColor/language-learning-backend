@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree" id="admin_adventure_lists">
        <h1 id="adventure-h">Adventure</h1>
        <div id="filter" class="field">
            <label>Filter</label>
            <div class="ui selection dropdown">
                <input type="hidden" name="filter" value="{{$filter}}" id="adventure_filter">
                <div class="text">All</div>
                <div class="menu">
                    <div class="item default" data-value="all">All</div>
                    <div class="item" data-value="unlock">Unlock</div>
                </div>
            </div>
        </div>
        <div class="ui search">
            <div class="ui icon input">
                <input class="prompt" type="text" placeholder="Search" id="filterInput">
                <i class="search icon"></i>
            </div>
        </div>
        <div class="ui small breadcrumb" id="adventures_path">
            <a href="{{route('episode-index',[$season->id,$filter])}}" id="adventures_back_button"><i
                        class="arrow left icon"></i></a>
            <a href="{{route('season-index',$filter)}}" class="section">Adventure</a>
            <a href="{{route('season-index',$filter)}}" class="section"><i
                        class="right chevron icon divider"></i>Season {{$season->order + 1}}</a>
            <a href="{{route('episode-index',[$season->id,$filter])}}" class="section"><i
                        class="right chevron icon divider"></i>Episode {{$episode->order + 1}}</a>

        </div>
        <div class="ui buttons fluid" id="admin_seasons_list">
            @php
                $i = 0;
                foreach ($chapters as $item){
                    if ($item->type == 2){
                        $i += 1;
                    }
                }
            @endphp
            @foreach($chapters as $item)
                <div class="{{$item->id}} ui left labeled button season adv chapter_div_button @if($item->type == 2) intro @endif"
                     data-value="{{$item->id}}"
                     id="{{$loop->index + 1}}">
                    <a href="{{route('sub-chapter-index',[$season->id,$episode->id,$item->id])}}"
                       class="ui basic label adventures_chapter_entity_button">
                        @if($item->type == 0)
                            <span style="background-color: var(--chapter)">C @if($i>0)
                                    {{$loop->index}}
                                @else
                                    {{$loop->index + 1}}
                                @endif</span>
                        @elseif($item->type == 2)
                            <span style="background-color: var(--intro)">I @if($i>0)
                                    {{$loop->index}}
                                @else
                                    {{$loop->index + 1}}
                                @endif</span>
                        @else
                            <span style="background-color: var(--quiz)">Q @if($i>0)
                                    {{$loop->index}}
                                @else
                                    {{$loop->index + 1}}
                                @endif</span>
                        @endif
                        <p>{{$item->name_en}}</p>
                    </a>
                    @if($item->type <> 1)
                        <button class="ui icon button chapter_edit_button">
                            <i class="edit outline icon"></i>
                        </button>
                    @endif
                    @if($item->is_displayed_to_user == 1)
                        @if($item->type == 1)
                            <button class="negative ui icon button chapter_delete_button">
                                <i class="trash alternate icon"></i>
                            </button>
                        @endif
                        <button class="un-lock ui icon button chapter_unlock" data-value="{{$item->id}}">
                            <i class="unlock alternate icon"></i>
                        </button>
                    @else
                        @if($item->type == 1)
                        <button class="negative ui icon button chapter_delete_button">
                            <i class="trash alternate icon"></i>
                        </button>
                        @endif
                        <button class="lock ui icon button chapter_lock" data-value="{{$item->id}}">
                            <i class="lock alternate icon"></i>
                        </button>
                    @endif
                </div>
            @endforeach
            <button class="add ui basic button" id="add_new_chapter_button" data-value="{{$episode_id}}"><i
                        class="plus icon"></i>add new chapter
            </button>
        </div>
    </div>

    <div id="edit" class="ten wide column">
        <div class="edit" id="season_edit_create_zone">

        </div>
    </div>
    </button>
    <script src="{{asset("js/search_result.js")}}"></script>
    <script>
    $(document).ready(function(){
        $('#admin_seasons_list > div a p').each(function() {
            if ($(this).text() === 'Quiz') {
                $(this).parents('.adv').addClass('quiz')
            }
        })
    })

        $("#add_new_chapter_button").on('click', function () {
            let episodeId = $(this).attr('data-value');
            let seasonId = "{{$season_id}}"
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/chapter/create/" + seasonId + '/' + episodeId,
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let form = $("#season_edit_create_zone");
                                form.empty();
                                form.append(res)
                            },
                        });
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/chapter/create/" + seasonId + '/' + episodeId,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $("#season_edit_create_zone");
                        form.empty();
                        form.append(res)
                    },
                });
            }
        })

        $(".chapter_edit_button").on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                let id = $($(this).parent()[0]).attr('data-value');
                let position = $($(this).parent()[0]).attr('id');
                let path = $("#adventures_path");
                let seasonId = "{{$season_id}}";
                let episodeId = "{{$episode_id}}";
                let clickedButton = $($(this).parent()[0]);
                $(".basic-error").modal({
                    onApprove: function () {
                        if (path.hasClass('selected')) {
                            path.children().last().remove()
                            @if($i>0)
                            path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Intro ' + position + '</a>')
                            @else
                            path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Chapter ' + position + '</a>')
                            @endif
                        } else {
                            path.addClass('selected');
                            @if($i>0)
                            path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Intro ' + position + '</a>')
                            @else
                            path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Chapter ' + position + '</a>')
                            @endif
                        }

                        $(".chapter_div_button").removeClass('check')
                        clickedButton.addClass('check')
                        $.ajax({
                            type: "get",
                            url: "/admin/chapter/edit/" + id + '/' + seasonId + '/' + episodeId,
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let form = $("#season_edit_create_zone");
                                form.empty();
                                form.append(res)
                            },
                        });
                    }
                }).modal('show');
            } else {
                let id = $($(this).parent()[0]).attr('data-value');
                let position = $($(this).parent()[0]).attr('id');
                let path = $("#adventures_path");
                let clickedButton = $($(this).parent()[0]);
                let seasonId = "{{$season_id}}";
                let episodeId = "{{$episode_id}}"
                if (path.hasClass('selected')) {
                    path.children().last().remove()
                    @if($i>0)
                    path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Intro ' + position + '</a>')
                    @else
                    path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Chapter ' + position + '</a>')
                    @endif
                } else {
                    path.addClass('selected');
                    @if($i>0)
                    path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Intro ' + position + '</a>')
                    @else
                    path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Chapter ' + position + '</a>')
                    @endif
                }


                $(".chapter_div_button").removeClass('check')
                clickedButton.addClass('check')
                $.ajax({
                    type: "get",
                    url: "/admin/chapter/edit/" + id + '/' + seasonId + '/' + episodeId,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $("#season_edit_create_zone");
                        form.empty();
                        form.append(res)
                    },
                });
            }

        })

        {{--function entityPass(id) {--}}
        {{--    let filter = $("#adventure_filter").val()--}}
        {{--    if ($('#chapter_create_form').hasClass('contentChanged') || $('#chapter_edit_form').hasClass('contentChanged')) {--}}
        {{--        $('#entity_pass_modal').modal({--}}
        {{--            closable: false,--}}
        {{--            onApprove: function () {--}}
        {{--                location.href = '/admin/episode/sub-chapters/'+"{{$season_id}}/{{$episode_id}}" + id + "/" + filter;--}}

        {{--            }--}}
        {{--        }).modal('show')--}}
        {{--    } else {--}}
        {{--        location.href = '/admin/episode/sub-chapters/'+"{{$season_id}}/{{$episode_id}}/" + id + "/" + filter;--}}
        {{--    }--}}
        {{--}--}}
        {{--$('#adventure_filter').on('change',function (){--}}
        {{--    let filter = $("#adventure_filter").val()--}}
        {{--    location.href = '/admin/episode/chapters/' + {{$season_id}} + "/" + {{$episode_id}} + "/" + filter ;--}}
        {{--})--}}
        $(".chapter_unlock").on('click', function () {
            let id = $(this).attr('data-value');
            let elem = $(this);
            let closed = 0;

            if (elem.hasClass("lock")) {
                closed = 1;
            }
            $(".state-hidden").modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        url: "/admin/chapter/lock-unlock/" + id + "/" + closed,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            if (closed === 0) {
                                elem.removeClass("un-lock");
                                elem.addClass("lock")
                                let child = elem.children()[0];
                                $(child).removeClass('unlock');
                                $(child).addClass('lock');
                            } else {
                                elem.removeClass("lock");
                                elem.addClass("un-lock")
                                let child = elem.children()[0];
                                $(child).removeClass('lock');
                                $(child).addClass('unlock');
                            }
                        },
                    });
                }
            }).modal('show');

        })
        $(".chapter_lock").on('click', function () {
            let id = $(this).attr('data-value');
            let elem = $(this);
            let closed = 0;

            if (elem.hasClass("lock")) {
                closed = 1;
            }
            $(".state-visible").modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        url: "/admin/chapter/lock-unlock/" + id + "/" + closed,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            if (closed === 0) {
                                elem.removeClass("un-lock");
                                elem.addClass("lock")
                                let child = elem.children()[0];
                                $(child).removeClass('unlock');
                                $(child).addClass('lock');
                            } else {
                                elem.removeClass("lock");
                                elem.addClass("un-lock")
                                let child = elem.children()[0];
                                $(child).removeClass('lock');
                                $(child).addClass('unlock');
                            }
                        },
                    });
                }
            }).modal('show');

        })
        // delete Quiz btn
        $('.negative.chapter_delete_button').on('click', function () {
            $('.delete-modal').modal({
                onApprove: function () {
                    let id = $('#admin_seasons_list .quiz').attr('data-value');
                    $.ajax({
                        type: "post",
                        url: "/admin/chapter/delete/" + id,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            location.reload();
                        }
                    })
                }
            }).modal('show');
        });

        $(document).ajaxComplete(function () {
            $('#chapter_select_field').on('change', function () {
                let type = $(this).val();
                let episodeId = "{{$episode->id}}";
                let seasonId = "{{$season->id}}"

                if ($('.form').hasClass('contentChanged')) {
                    $.ajax({
                        type: "get",
                        url: "/admin/chapter/create/" + seasonId + '/' + episodeId + '/' + type,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            let form = $("#season_edit_create_zone");
                            form.empty();
                            form.append(res)
                        },
                    });
                } else {
                    $.ajax({
                        type: "get",
                        url: "/admin/chapter/create/" + seasonId + '/' + episodeId + '/' + type,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            let form = $("#season_edit_create_zone");
                            form.empty();
                            form.append(res)
                        },
                    });
                }

            })
        })

    </script>

@endsection