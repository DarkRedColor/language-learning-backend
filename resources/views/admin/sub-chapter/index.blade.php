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
            <a href="{{route('chapter-index',[$season->id,$episode->id,$filter])}}" id="adventures_back_button"><i
                        class="arrow left icon"></i></a>
            <a href="{{route('season-index',$filter)}}" class="section">Adventure</a>
            <a href="{{route('season-index',$filter)}}" class="section"><i
                        class="right chevron icon divider"></i>Season {{$season->order + 1}}</a>
            <a href="{{route('episode-index',[$season->id,$filter])}}" class="section"><i
                        class="right chevron icon divider"></i>Episode {{$episode->order + 1}}</a>
            <a href="{{route('chapter-index',[$season->id,$episode->id,$filter])}}" class="section"><i
                        class="right chevron icon divider"></i>
                @if($chapter->type == 2)
                    Intro {{$chapter->order}}
                @else
                    Chapter
                    @if(count(\App\Models\Chapter::where('episode_id',$episode->id)->where('type',2)->get())>0)
                        {{$chapter->order}}
                    @else
                        {{$chapter->order + 1}}
                    @endif
                @endif
            </a>

        </div>
        <div class="ui buttons fluid" id="admin_seasons_list">
            @foreach($scenes as $item)
                <div class="{{$item->id}} ui left labeled button season adv scene_div_button @if($chapter->type == 1)@if($allScenes->where('sub_chapter_id', $item->id)->first() != null)is_linked @endif @endif"
                     data-value="{{$item->id}}"
                     id="{{$loop->index + 1}}">
                    <a href="#"
                       class="ui basic label adventures_scene_entity_button">
                        <span style="background-color: var(--scene)">Sc {{$loop->index + 1}}</span>
                        <p>{{$item->name_en}}</p>
                    </a>
                    <button class="ui icon button scene_edit_button">
                        <i class="edit outline icon"></i>
                    </button>


                    @if($item->is_displayed_to_user == 1)
                        <button class="un-lock ui icon button sub_chapter_unlock" data-value="{{$item->id}}">
                            <i class="unlock alternate icon"></i>
                        </button>
                    @else
                        <button class="lock ui icon button sub_chapter_lock" data-value="{{$item->id}}">
                            <i class="lock alternate icon"></i>
                        </button>
                    @endif
                </div>
            @endforeach
            <button class="add ui basic button" id="add_new_scene_button" data-value="{{$chapter->id}}"><i
                        class="plus icon"></i>add new scene
            </button>
        </div>
    </div>

    <div id="edit" class="ten wide column">
        <div class="edit" id="season_edit_create_zone">

        </div>
    </div>

    <script src="{{asset("js/search_result.js")}}"></script>
    <script>

        $("#add_new_scene_button").on('click', function () {
            let episodeId = "{{$episode->id}}";
            let seasonId = "{{$season->id}}"
            let chapterId = '{{$chapter->id}}'
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/sub-chapter/create/" + seasonId + '/' + episodeId + '/' + chapterId,
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let form = $("#season_edit_create_zone");
                                form.empty();
                                form.append(res)
                                $(".adv").removeClass('check');
                            },
                        });
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/sub-chapter/create/" + seasonId + '/' + episodeId + '/' + chapterId,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $("#season_edit_create_zone");
                        form.empty();
                        form.append(res)
                        $(".adv").removeClass('check');
                    },
                });
            }
        })

        $(".scene_edit_button").on('click', function () {
            if ($('#scene_create_form').hasClass('contentChanged') || $('#scene_edit_form').hasClass('contentChanged')) {
                let id = $($(this).parent()[0]).attr('data-value');
                let position = $($(this).parent()[0]).attr('id');
                let path = $("#adventures_path");
                let seasonId = "{{$season->id}}";
                let episodeId = "{{$episode->id}}";
                let chapterId = "{{$chapter->id}}"
                let clickedButton = $($(this).parent()[0]);
                $(".basic-error").modal({
                    onApprove: function () {
                        if (path.hasClass('selected')) {
                            path.children().last().remove()
                            path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Scene ' + position + '</a>')
                        } else {
                            path.addClass('selected');
                            path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Scene ' + position + '</a>')
                        }

                        $(".scene_div_button").removeClass('check')
                        clickedButton.addClass('check')
                        $.ajax({
                            type: "get",
                            url: "/admin/sub-chapter/edit/" + id + '/' + seasonId + '/' + episodeId + '/' + chapterId,
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
                let seasonId = "{{$season->id}}";
                let episodeId = "{{$episode->id}}"
                let chapterId = "{{$chapter->id}}"
                if (path.hasClass('selected')) {
                    path.children().last().remove()
                    path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Scene ' + position + '</a>')
                } else {
                    path.addClass('selected');
                    path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Scene ' + position + '</a>')
                }


                $(".scene_div_button").removeClass('check')
                clickedButton.addClass('check')
                $.ajax({
                    type: "get",
                    url: "/admin/sub-chapter/edit/" + id + '/' + seasonId + '/' + episodeId + '/' + chapterId,
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

        // function entityPass(id) {
        //     if ($('#scene_create_form').hasClass('contentChanged') || $('#scene_edit_form').hasClass('contentChanged')) {
        //         $('#entity_pass_modal').modal({
        //             closable: false,
        //             onApprove: function () {
        //                 // location.href = '/admin/season/episodes/' + id;
        //             }
        //         }).modal('show')
        //     } else {
        //         // location.href = '/admin/season/episodes/' + id;
        //     }
        // }
        {{--$('#adventure_filter').on('change',function (){--}}
        {{--    let filter = $("#adventure_filter").val()--}}
        {{--    location.href = '/admin/episode/sub-chapters/'+"{{$season->id}}/{{$episode->id}}/" + {{$chapter->id}} + "/" + filter;--}}
        {{--})--}}

        $(".sub_chapter_unlock").on('click', function () {
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
                        url: "/admin/sub-chapter/lock-unlock/" + id + "/" + closed,
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
        $(".sub_chapter_lock").on('click', function () {
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
                        url: "/admin/sub-chapter/lock-unlock/" + id + "/" + closed,
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
    </script>
@endsection