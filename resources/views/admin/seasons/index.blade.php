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
            {{--            <a href="#" id="adventures_back_button"><i class="arrow left icon"></i></a>--}}

            <a href="{{route('season-index',$filter)}}" class="section">Adventure</a>

        </div>
        <div class="ui buttons fluid" id="admin_seasons_list">
            @foreach($seasons as $item)
                <div class="{{$item->id}} ui left labeled button season adv seasons_div_button"
                     data-value="{{$item->id}}"
                     id="{{$loop->index + 1}}">
                    <a href="{{route('episode-index',[$item->id])}}"
                       class="ui basic label adventures_episode_entity_button">
                        <span>S {{$loop->index + 1}}</span>
                        <p>{{$item->name_en}}</p>
                    </a>
                    <button class="ui icon button season_edit_button">
                        <i class="edit outline icon"></i>
                    </button>

                    @if($item->is_displayed_to_user == 1)
                        <button class="un-lock ui icon button season_unlock" data-value="{{$item->id}}">
                            <i class="unlock alternate icon"></i>
                        </button>
                    @else
                        <button class="lock ui icon button season_lock" data-value="{{$item->id}}">
                            <i class="lock alternate icon"></i>
                        </button>
                    @endif
                </div>
            @endforeach
            <button class="add ui basic button" id="add_new_season_button"><i class="plus icon"></i>add new season
            </button>
        </div>
    </div>

    <div id="edit" class="ten wide column">
        <div class="edit" id="season_edit_create_zone">

        </div>
    </div>
    <script src="{{asset("js/search_result.js")}}" ></script>
    <script>
        $(".season_edit_button").on('click', function () {
            if ($('.form').hasClass('contentChanged')){
                let id = $($(this).parent()[0]).attr('data-value');
                let position = $($(this).parent()[0]).attr('id');
                let path = $("#adventures_path");
                let clickedButton = $($(this).parent()[0]);
                $(".basic-error").modal({
                    onApprove: function () {
                        if ($('#season_edit_form').hasClass("contentMoved")) {
                            let id = $('#season_edit_form').attr('data-value');
                            let elem = $("." + id);
                            let position = elem.attr('id');
                            let nextOrPrevElement = $("#" + (parseInt(position) + 1))
                            if (nextOrPrevElement.length === 0) {
                                nextOrPrevElement = $("#" + (parseInt(position) - 1))
                                elem.insertAfter(nextOrPrevElement)
                            } else {
                                elem.insertBefore(nextOrPrevElement)
                            }
                            $('#season_edit_form').removeClass("contentMoved");
                        }
                        if (path.hasClass('selected')) {
                            path.children().last().remove()
                            path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Season ' + position + '</a>')
                        } else {
                            path.addClass('selected');
                            path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Season ' + position + '</a>')
                        }
                        $(".seasons_div_button").removeClass('check')
                        clickedButton.addClass('check')
                        $.ajax({
                            type: "get",
                            url: "/admin/season/edit/" + id,
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
                if (path.hasClass('selected')) {
                    path.children().last().remove()
                    path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Season ' + position + '</a>')
                } else {
                    path.addClass('selected');
                    path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>Season ' + position + '</a>')
                }
                $(".seasons_div_button").removeClass('check')
                clickedButton.addClass('check')
                $.ajax({
                    type: "get",
                    url: "/admin/season/edit/" + id,
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

        $("#add_new_season_button").on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "{{route('season-create')}}",
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let form = $("#season_edit_create_zone");
                                form.empty();
                                form.append(res)
                                $(".seasons_div_button").removeClass('check')
                            },
                        });
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "{{route('season-create')}}",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $("#season_edit_create_zone");
                        form.empty();
                        form.append(res)
                        $(".seasons_div_button").removeClass('check')
                    },
                });
            }
        })

        function entityPass(id) {
            let filter = $("#adventure_filter").val()
            if ($('#season_create_form').hasClass('contentChanged') || $('#season_edit_form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    closable: false,
                    onApprove: function () {
                        location.href = '/admin/season/episodes/' + id + "/" + filter;
                    }
                }).modal('show')
            } else {
                location.href = '/admin/season/episodes/' + id + "/" + filter;
            }
        }

        $(".season_unlock").on('click', function () {
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
                        url: "/admin/season/lock-unlock/" + id + "/" + closed,
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
        $(".season_lock").on('click', function () {
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
                        url: "/admin/season/lock-unlock/" + id + "/" + closed,
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