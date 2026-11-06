@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree lang-list">
        <h1 id="adventure-h">Learning</h1>
        <div class="ui small breadcrumb">
            <a href="{{route('learning-index')}}"><i class="arrow left icon"></i></a>
            <a href="{{route('learning-index')}}" class="section">Learning<i class="right chevron icon divider"></i></a>
            <a href="{{route('learning-index')}}" class="section">{{$learning->name}}</a>
        </div>
        <div id="filterList" class="character-list ui buttons fluid ordered results gramm-pages l-game-list">
            @foreach($learning->theories as $item)
                <a href="#" class="ui basic item label text theory" disabled data-value="{{$item->id}}">
                    @if($item->type == 0)
                        <h4>{{$item->name}}</h4>
                    @else
                        <h4>{{$item->description_en}}</h4>
                    @endif
                </a>
            @endforeach
            @foreach($learning->practices as $item)
                {{--                <a href="{{route('learning-practice',[$item->id,$learning->id])}}" class="ui basic item label text"
                                   data-value="{{$item->id}}">
                                    <h4>{{$item->name}}</h4>
                                </a>--}}

                <div class="char ui left labeled button">
                    <a href="{{route('learning-practice',[$item->id,$learning->id])}}" class="ui basic item label"
                       data-value="{{$item->id}}">
                        <p>{{$item->name}}</p>
                    </a>
                    <button class="ui icon button character_edit_button words-edit" data-value="{{$item->id}}">
                        <i class="edit icon"></i>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
    <div id="edit" class="ten wide column">

    </div>
    <script>
        $('.words-edit').on('click', function () {
                let id = $(this).attr('data-value');
                if ($('form').hasClass('contentChanged')) {
                    $('.basic-error').modal({
                        onApprove: function () {
                            $.ajax({
                                type: "get",
                                url: "/admin/practice/link/edit/{{$learning->id}}/" + id,
                                contentType: 'application/json',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (res) {
                                    let form = $("#edit");
                                    form.empty();
                                    form.append(res)
                                },
                            });
                        }
                    }).modal('show');
                } else {
                    $.ajax({
                        type: "get",
                        url: "/admin/practice/link/edit/{{$learning->id}}/" + id,
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            let form = $("#edit");
                            form.empty();
                            form.append(res)
                        },
                    });
                }
            }
        )
    </script>
@endsection