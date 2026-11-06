<div class="edit">
    <h1>Edit</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area">
        <h3>{{$learning->name}}</h3>
    </div>
    <div class="edit-area grid">
        <form action="{{route('learning-update',$learning->id)}}" class="ui form learning add-words" method="post">
            @csrf
            {{--            @dump($learning)--}}
            <div class="two fields">
                <div class="field">
                    <label>Name Learning</label>
                    <input placeholder="learning_name" type="text" name="name" value="{{$learning->name}}">
                </div>
                {{--                <div class="field">--}}
                {{--                    <label>Games count</label>--}}
                {{--                    <input placeholder="games count number" type="number" name="game_count">--}}
                {{--                </div>--}}
            </div>
            <div class="field">
                <label>Link Theory</label>
                <input name="theory" value="" id="theory" hidden/>
                <div class="ui fluid multiple search selection dropdown theory">
                    <i class="dropdown icon"></i>
                    @foreach($learning->theories as $item)
                        <a class="ui label transition visible" data-value="{{$item->id}}"
                           style="display: inline-block !important;">@if($item->type == 0)
                                {{$item->name}}
                            @else
                                {{$item->description_en}}
                            @endif<i class="delete icon"></i></a>
                    @endforeach
                    <div class="default text">Theory</div>
                    <div class="menu">
                        @foreach($theories as $item)
                            <div class="item" data-value="{{$item->id}}">@if(isset($item->name) and !empty($item->name))
                                    {{$item->name}}
                                @else
                                    {{$item->description_en}}
                                @endif</div>
                        @endforeach
                        @foreach($learning->theories as $item)
                            <div class="item active filtered" data-value="{{$item->id}}">{{$item->name}}</div>
                        @endforeach
                    </div>
                </div>
                {{--                <select class="ui fluid search dropdown theory" multiple="">--}}
                {{--                    <option value="">Theory</option>--}}
                {{--                    @foreach($theories as $item)--}}
                {{--                        <option value="{{$item->id}}">{{$item->name}}</option>--}}
                {{--                    @endforeach--}}
                {{--                    <i class="chevron down icon"></i>--}}
                {{--                </select>--}}
            </div>
            <div class="field">
                <label>Link Practice</label>
                <input name="practice" value="" id="practice" hidden/>
                <div class="ui fluid multiple search selection dropdown practice">
                    <i class="dropdown icon"></i>
                    @foreach($learning->practices as $item)
                        <a class="ui label transition visible" data-value="{{$item->id}}"
                           style="display: inline-block !important;">{{$item->name}}<i class="delete icon"></i></a>
                    @endforeach
                    <div class="default text">Practice</div>
                    <div class="menu">
                        @foreach($practices as $item)
                            <div class="item" data-value="{{$item->id}}">{{$item->name}}</div>
                        @endforeach
                        @foreach($learning->practices as $item)
                            <div class="item active filtered" data-value="{{$item->id}}">{{$item->name}}</div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="buttons action-buttons">
                <button type="button" class="positive ui button save" disabled>SAVE</button>
                <button type="button" class="grey ui button discard" disabled>DISCARD</button>
                <button type="button" class="negative ui button delete">DELETE</button>
            </div>
        </form>
    </div>
</div>
<script src="{{asset('js/main.js')}}"></script>
<script>
    $(document).ready(function () {
        {{--        @foreach($learning->practices as $item)--}}
        {{--        $('.search.dropdown.practice').prepend('<a class="ui label transition visible" data-value="{{$item->id}}">{{$item->name}}<i class="delete icon"></i></a>')--}}
        {{--        @endforeach--}}

        {{--        @foreach($learning->theories as $item)--}}
        {{--        $('.search.dropdown.theory').prepend('<a class="ui label transition visible" data-value="{{$item->id}}">{{$item->name}}<i class="delete icon"></i></a>')--}}
        {{--        @endforeach--}}

        $('.save').on('click', function () {
            let arr = [];
            $('.search.dropdown.practice >a').each(function () {
                arr.push($(this).attr('data-value'))
            })
            $('#practice').val(JSON.stringify(arr));
            arr = [];
            $('.search.dropdown.theory >a').each(function () {
                arr.push($(this).attr('data-value'))
            })
            $('#theory').val(JSON.stringify(arr));
            $('.form').submit();
        })
        // type just numbers
        $(function () {
            $("input[type='number']").on('input', function () {
                $(this).val($(this).val().replace(/[^0-9]/g, ''));
            });
        });
        // add class && show button
        $('.form').on('change keyup click', function () {
            $('.button.discard').attr('disabled', false)
            var inputText = $('.form input[type="text"]:not([type="hidden"])').val().length;
            var dropPractice = $('.form .practice a').length;
            var dropTheory = $('.form .theory a').length;
            if (inputText > 0 && dropPractice > 0) {
                $('.form').addClass('contentChanged')
                $('.form .button.save').attr('disabled', false)
            } else {
                $('.form .button.save').attr('disabled', true)
            }
        });

        $('.button.discard').on('click', function () {
            $('.discard-modal').modal({
                onApprove: function () {
                    $.ajax({
                        type: "get",
                        url: "/admin/learning/edit/{{$learning->id}}",
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
        })
        $('.button.delete').on('click', function () {
            $('.delete-modal').modal({
                onApprove: function () {
                    $.ajax({
                        type: "post",
                        url: "/admin/learning/delete/{{$learning->id}}",
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
    })

</script>