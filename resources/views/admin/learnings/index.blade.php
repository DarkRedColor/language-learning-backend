@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree">
        <h1 id="adventure-h">Learning</h1>
        <div class="ui small breadcrumb" id="learning_path">
            <a href="#"><i class="arrow left icon"></i></a>
            <a href="#" class="section">Learning</a>
        </div>
        <div class="ui search">
            <div class="ui icon input">
                <input class="prompt" type="text" placeholder="Search" id="filterInput">
                <i class="search icon"></i>
            </div>
        </div>
        <button class="add ui basic button add-text"><i class="plus icon"></i>add learning</button>
        <div id="filterList" class="character-list ui buttons fluid ordered list results gramm-pages">
            @foreach($learnings as $item)
                <div class="char ui left labeled button" id="{{$item->id}}">
                    <a href="{{route('learning-show',$item->id)}}" class="ui basic item label" data-value="">
                        <p>{{$item->name}}</p>
                    </a>
                    <button class="ui icon button character_edit_button words-edit">
                        <i class="edit icon"></i>
                    </button>
                    <button class="ui icon button character_edit_button negative words-detach">
                        <i class="cancel icon"></i>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
    <div id="edit" class="ten wide column">

    </div>
    <script src={{asset("js/search_result.js")}} ></script>
    <script>
        $(document).ready(function () {
            let checked = {{session("id") ?? "null"}};
            if (checked !== null) {
                $('#' + checked).addClass('check')
            }
            $('.tree').animate({
                scrollTop: ($('.character-list .char.check').offset().top - 150)
            }, 500);
        });

        $('.left.labeled.button').on('click', function () {
            $('.left.labeled.button').removeClass('check');
            $(this).addClass('check');
        })

        $('.add-text').on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $('.discard-modal').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "{{route('learning-create')}}",
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let text = $("#edit");
                                text.empty();
                                text.append(res);
                                $(".char").removeClass('check');
                            }
                        })
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "{{route('learning-create')}}",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let text = $("#edit");
                        text.empty();
                        text.append(res);
                        $(".char").removeClass('check');
                    }
                })
            }
        })

        $('.words-edit').on('click', function () {
            let id = $(this).parent().attr('id');
            let path = $("#learning_path");
            let name = $($($(this).parent()).children()[0]).children()[0].innerHTML;
            if (path.hasClass('selected')) {
                path.children().last().remove()
                path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>' + name + '</a>')
            } else {
                path.addClass('selected');
                path.append('<a href="#" class="active section"><i class="right chevron icon divider"></i>' + name + '</a>')
            };
            if ($('.form').hasClass('contentChanged')) {
                $('.discard-modal').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/learning/edit/" + id,
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
                }).modal('show');
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/learning/edit/" + id,
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
        $('.words-detach').on('click', function () {
            let id = $(this).parent().attr('id');
            $('.delete-modal').modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        url: "/admin/learning/delete/" + id,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            location.reload();
                        }
                    })
                }
            }).modal('show')
        })
    </script>
@endsection