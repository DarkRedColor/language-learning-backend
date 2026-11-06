@extends('admin.layouts.admin-intro-dashboard')

@section('content')
    <div class="four wide column tree">
        <div class="load-btns">
            <h1>Download</h1>
            <a href="/admin/dialogue/page/{{$id}}" class="ui icon basic button exit"><i class="arrow left icon"></i></a>
        </div>
        <div id="local-list" class="ui buttons fluid">
            @foreach($languages as $language)
                @if($language->lcid <> 'en')
                    <div class="ui checkbox left labeled button lang" data-value="{{$language->lcid}}">
                        <a href="#" class="ui basic label">
                            <img src="{{$language->flag}}">
                            <p>{{$language->display_name}}</p>
                            <div class="ui fitted checkbox">
                                <input type="checkbox">
                                <label></label>
                            </div>
                        </a>
                    </div>
                @endif
            @endforeach
            <div class="down-prev">
                <button type="button" class="ui button download-text">download</button>
            </div>
        </div>
    </div>

    {{--        <script src={{asset("js/main.js")}}></script>--}}
    <script>
        $(document).ready(function () {

            $('input[type="checkbox"]').on('change', function () {
                if ($(this).is(':checked')) {
                    $(this).parents('.ui.basic.label').css('background', 'var(--blue)')
                    $(this).parents('.checkbox.left.labeled').addClass('selected-item')
                } else {
                    $(this).parents('.ui.basic.label').css('background', 'var(--white)')
                    $(this).parents('.checkbox.left.labeled').removeClass('selected-item')
                }
            })
        })

        $('.download-text').on('click', function () {
            let arr = [];

            $('.selected-item').each(function () {
                arr.push($(this).attr('data-value'))
            })

            $.ajax({
                type: "post",
                contentType: 'application/json',
                url: "{{route('dialogue-export-localisation',$id)}}",
                data: JSON.stringify(arr),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    location.href = '/admin/dialogue/download/localisation/' + data;
                }
            })

        })

    </script>
@endsection