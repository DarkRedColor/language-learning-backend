@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree">
        <h1 id="adventure-h">{{$theory->name}}</h1>
        <button onclick="location.href = '{{route('theory-index')}}'" class="ui left labeled icon button back">
                <i class="left arrow icon"></i>
                Back to theory
            </button>
        <button class="add ui basic button add-text"><i class="plus icon"></i>add page</button>
        <div id="filterList" class="character-list ui buttons fluid ordered list results gramm-pages">
            @foreach($pagesName as $item)
                <div class="char ui left labeled button">
                    <a href="#" class="ui basic item label" data-value="">
                        <p>PAGE {{$loop->index + 1}}</p>
                    </a>
                    <button class="ui icon button character_edit_button words-edit" data-value="{{$item->id}}">
                        <i class="edit icon"></i>
                    </button>
                    <button class="ui icon button character_edit_button negative words-detach"
                            data-value="{{$item->id}}">
                        <i class="cancel icon"></i>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
    <div id="edit" class="ten wide column">
    </div>
    <script>
        $('.ui.left.labeled.button').on('click', function () {
            $('.ui.left.labeled.button').removeClass('check');
            $(this).addClass('check');
        })

        $('.add.add-text').on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "{{route('theory-pages-create',$theory->id)}}",
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
                    url: "{{route('theory-pages-create',$theory->id)}}",
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
        });

        $('.words-edit').on('click', function () {
            let id = $(this).attr('data-value');
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/theory/pages/edit/{{$theory->id}}/" + id,
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
                    url: "/admin/theory/pages/edit/{{$theory->id}}/" + id,
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
        });

        $('.negative.words-detach').on('click', function () {
            let id = $(this).attr('data-value');
            $('.delete-modal').modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        url: "/admin/theory/pages/delete/" + id,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {
                            location.reload()
                        }
                    })
                }
            }).modal('show')
        })
    </script>

@endsection