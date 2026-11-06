@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree">
        <div class="interface">
            <h1>Characters</h1>
        </div>
        <div class="ui search">
            <div class="ui icon input">
                <input id="filterInput" class="prompt" type="text" placeholder="Character search">
                <i class="search icon"></i>
            </div>
        </div>
        <button class="add ui basic button add-text" id="character_add_button"><i class="plus icon"></i>add character
        </button>
        <div id="filterList" class="character-list ui buttons fluid ordered list results">
            @foreach($personages as $item)
                <div class="char ui left labeled button">
                    <a href="{{route('emotion-index',$item->id)}}" class="ui basic item label"
                       data-value="{{$item->id}}">
                        <p>{{$item->name_en}}</p>
                    </a>
                    <button class="ui icon button character_edit_button" data-value="{{$item->id}}">
                        <i class="edit icon"></i>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
    <div id="edit" class="ten wide column">

    </div>
    <script type="application/javascript" src={{asset("js/search_result.js")}}></script>


    <script>
        $(".character_edit_button").on('click', function () {
            let id = $(this).attr('data-value');
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "/admin/personage/edit/" + id,
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
                    url: "/admin/personage/edit/" + id,
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
        $(".char").not('.add-text').on('click', function () {
            $(".char").removeClass('check');
            $(this).addClass('check');
        })

        $('#character_add_button').on('click', function () {
            if ($('.form').hasClass('contentChanged')) {
                $('.basic-error').modal({
                    onApprove: function () {
                        $.ajax({
                            type: "get",
                            url: "{{route('personage-create')}}",
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
                    url: "{{route('personage-create')}}",
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
    </script>
@endsection
