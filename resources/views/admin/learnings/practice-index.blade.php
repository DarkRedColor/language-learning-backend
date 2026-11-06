@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree lang-list">
        <h1 id="adventure-h">Learning</h1>
        <div class="ui search">
            <div class="ui icon input">
                <input class="prompt" type="text" placeholder="Search" id="filterInput">
                <i class="search icon"></i>
            </div>
        </div>
        <div class="ui small breadcrumb" id="learning_path">
            <a href="{{route('learning-show',$learningId)}}"><i class="arrow left icon"></i></a>
            <a href="{{route('learning-index')}}" class="section">Learning</a>
            <a href="{{route('learning-index')}}" class="section"><i
                        class="right chevron icon divider"></i>{{$learning->name}}</a>
            <a href="{{route('learning-show',$learningId)}}" class="section"><i
                        class="right chevron icon divider"></i>{{$practices->name}}</a>
        </div>
        <div id="filterList" class="character-list ui buttons fluid ordered list results gramm-pages">
            @foreach($practices->gameTemplates as $item)
                <div class="char ui left labeled button" id="{{$item->id}}">
                    <a href="{{route('game-index',[$learningId,$practices->id,$item->id])}}" class="ui basic item label"
                       data-value="">
                        <p>{{$item->name}}</p>
                    </a>
                    <button class="ui icon button character_edit_button words-edit">
                        <i class="edit icon"></i>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
    <div id="edit" class="ten wide column">
    </div>
    <script src={{asset("js/search_result.js")}} ></script>
    <script>
        $('div.button').on('click', function () {
            $('div.button').removeClass('check');
            $(this).addClass('check');

        })
        $('.character_edit_button').on('click', function () {
            let id = $(this).parent().attr('id');
            let path = $("#learning_path");
            let name = $($($(this).parent()).children()[0]).children()[0].innerHTML;
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
                            url: "/admin/learning/practice/edit/" + id + "/{{$learningId}}/{{$id}}",
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
                }).modal('show')
            } else {
                $.ajax({
                    type: "get",
                    url: "/admin/learning/practice/edit/" + id + "/{{$learningId}}/{{$id}}",
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
    </script>
@endsection