@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree">
        <h1 id="adventure-h">{{$practicePages->name}}</h1>
        <button onclick="location.href = '{{route('practice-index')}}'" class="ui left labeled icon button back">
            <i class="left arrow icon"></i>
            Back to practice
        </button>
        <button class="add ui basic button add-text"><i class="plus icon"></i>add games</button>
        <div id="filterList" class="character-list ui buttons fluid ordered list results gramm-pages">
            @foreach($practicePages->gameTemplates->sortBy('name') as $item)
                <div class="char ui left labeled button">
                    <button type="button" class="ui basic item label preview-games" data-value="{{$item->id}}">
                        <p>{{$item->name}}</p>
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
                            url: "{{route('practice-pages-create',$practicePages->id)}}",
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
                    url: "{{route('practice-pages-create',$practicePages->id)}}",
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
                        url: "/admin/practice/pages/delete/"+id+"/{{$practicePages->id}}" ,
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
        $('.preview-games').on('click', function () {
            let id = $(this).attr('data-value');
            $.ajax({
                type: "get",
                async: false,
                url: "/admin/game/templates/preview/" + id,
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
        })
    </script>

@endsection