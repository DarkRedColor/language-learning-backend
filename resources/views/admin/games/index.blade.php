@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree lang-list">
        <div class="head-list">
            <h1 id="adventure-h">{{$gameTemplate->name}}</h1>
            <div class="ui small breadcrumb" id="learning_path">
                <a href="{{route('learning-practice',[$practiceId,$learning->id])}}"><i class="arrow left icon"></i></a>
                <a href="{{route('learning-index')}}" class="section">Learning</a>
                <a href="{{route('learning-index')}}" class="section"><i
                            class="right chevron icon divider"></i>{{$learning->name}}</a>
                <a href="{{route('learning-show',$learning->id)}}" class="section"><i
                            class="right chevron icon divider"></i>{{$practice->name}}</a>
                <a href="{{route('learning-practice',[$practiceId,$learning->id])}}" class="section"><i
                            class="right chevron icon divider"></i>{{$gameTemplate->name}}</a>
            </div>
            <button class="add ui basic button add-text"><i class="plus icon"></i>add game</button>
        </div>
        <div id="filterList" class="character-list ui buttons fluid ordered list l-game-list">
            @foreach($learnGames as $item)
                <div class="char ui left labeled button">
                    <button type="button" class="ui basic item label game-btn" data-value="" id="{{$item->id}}">
                        <h4>{{$loop->index + 1}}</h4>
                    </button>
                    <button class="ui icon button character_edit_button negative words-detach" id="{{$item->id}}">
                        <i class="trash alternate icon"></i>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
    <div id="edit" class="ten wide column">
    </div>
    <div class="legend">
        <button class="ui icon basic button legend-exit"><i class="close icon"></i></button>
        <h1>Games legend</h1>
        @include('admin.games.includes.legend')
    </div>
    <script type="application/javascript" src={{asset("js/search_result.js")}} ></script>
    <script>
        $('.add-text').on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $('.discard-modal').modal({
                    onApprove() {
                        $.ajax({
                            type: "get",
                            async: false,
                            url: "{{route('game-create',[$practiceId,$gameTemplate->id,$learning->id])}}",
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let form = $('#edit');
                                form.empty();
                                form.append(res);
                            }
                        });
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "{{route('game-create',[$practiceId,$gameTemplate->id,$learning->id])}}",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $('#edit');
                        form.empty();
                        form.append(res);
                    }
                });
            }
        })

        $('.game-btn').on('click', function () {
            let id = $(this).attr('id');
            let path = $("#learning_path");
            let name = $(this).children()[0].innerHTML;

            if (path.hasClass('selected')) {
                path.children().last().remove()
                path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>' + name + '</a>')
            } else {
                path.addClass('selected');
                path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>' + name + '</a>')
            }
            if ($('.form').hasClass('contentChanged')) {
                $('.discard-modal').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            async: false,
                            url: "/admin/game/edit/" + id + "/{{$practiceId}}/{{$gameTemplate->id}}/{{$learning->id}}",
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let form = $('#edit');
                                form.empty();
                                form.append(res);
                            }
                        });
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "/admin/game/edit/" + id + "/{{$practiceId}}/{{$gameTemplate->id}}/{{$learning->id}}",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $('#edit');
                        form.empty();
                        form.append(res);
                    }
                });
            }
        })

        $('.words-detach').on('click', function () {
            let id = $(this).attr('id');
            $('.delete-modal').modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        async: false,
                        url: "/admin/game/delete/" + id,
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