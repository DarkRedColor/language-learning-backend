@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree">
        <h1 id="adventure-h">Text</h1>
        <div class="ui search">
            <div class="ui icon input">
                <input id="filterInput" class="prompt" type="text" placeholder="Search ...">
                <i class="search icon"></i>
            </div>
        </div>
        <button class="add ui basic button add-text"><i class="plus icon"></i>add new text</button>
        <div id="filterList" class="character-list ui buttons fluid ordered list results gramm-pages">
            @foreach($texts as $text)
                <div class="char ui left labeled button" id="{{$text->id}}">
                    <a href="#" class="ui basic item label" data-value="">
                        <p>{{$text->name}}</p>
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
    <script type="application/javascript" src="{{asset('js/search_result.js')}}"></script>
    <script>
        $('.words-edit').on('click', function () {
            $('div.button').removeClass('check');
            $(this).parent().addClass('check');
            let id = $(this).parent().attr('id');
            if($('.form').hasClass('contentChanged')){
                $('.basic-error').modal({
                    onApprove: function(){
                        $.ajax({
                            type: "get",
                            url: "text/edit/" + id,
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let form = $("#edit");
                                form.empty();
                                form.append(res);
                            }
                        })
                    }
                }).modal('show')
            }else{
                $.ajax({
                    type: "get",
                    url: "text/edit/" + id,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $("#edit");
                        form.empty();
                        form.append(res);
                    }
                })
            }
        })

        $('.add-text').on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "{{route('file-text-create')}}",
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                let form = $("#edit");
                                form.empty();
                                form.append(res);
                            }
                        })
                    }
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "{{route('file-text-create')}}",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $("#edit");
                        form.empty();
                        form.append(res);
                    }
                })
            }
        })
        $('.words-detach').on('click', function () {
            let id = $(this).parent().attr('id');
            $('.delete-modal').modal({
                onApprove: function(){
                    $.ajax({
                        type: "post",
                        url: "text/delete/" + id,
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