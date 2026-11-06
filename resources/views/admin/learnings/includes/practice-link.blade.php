<div class="edit">
    <h1>{{$practice->name}}</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area">
        <h3>Link new files</h3>
    </div>
    <div class="edit-area grid">
        <form action="{{route('learning-link-post',[$learning,$practice->id])}}" class="ui form learning link-files" method="post">
            @csrf
            <div class="ui grid">
                <div class="field eight wide column">
                    <label>Link Files if you need</label>
                    <div class="ui selection search dropdown file">
                        <input type="hidden" name="files_id" value="{{$practice->file_id}}">
                        <div class="default text">Files to link</div>
                        <div class="menu">
                            @foreach($files as $item)
                                <div class="item" data-value="{{$item->id}}">
                                    <span>
                                        @switch($item->type)
                                            @case(1)
                                                audio
                                                @break
                                            @case(2)
                                                video
                                                @break
                                            @default
                                                text
                                        @endswitch
                                    </span>{{$item->name}}
                                </div>
                            @endforeach
                        </div>
                        <i class="chevron down icon"></i>
                    </div>
                </div>
                <div id="remove-audio" class="ui animated fade button negative four wide column remove-link" tabindex="0">
                    <div class="visible content">REMOVE LINK</div>
                    <div class="hidden content">
                        This remove selected files
                    </div>
                </div>
            </div>
            <div class="ui grid">
                <div class="field eight wide column">
                    <label>Link Texts for your audio</label>
                    <div class="ui selection search dropdown subtitle">
                        <input type="hidden" name="subtitle_id" value="{{$practice->subtitle_id}}">
                        <div class="default text">Subtitle to link</div>
                        <div class="menu">
                            @foreach($subtitles as $item)
                                <div class="item" data-value="{{$item->id}}">
                                    {{$item->key}}
                                </div>
                            @endforeach
                        </div>
                        <i class="chevron down icon"></i>
                    </div>
                </div>
                <div id="remove-text" class="ui animated fade button negative four wide column remove-link" tabindex="0">
                    <div class="visible content">REMOVE LINK</div>
                    <div class="hidden content">
                        This remove selected text
                    </div>
                </div>
            </div>

            <div class="buttons action-buttons">
                <button type="submit" class="positive ui button save" >SAVE</button>
                <a href="#" type="button" class="grey ui button dis">DISCARD</a>
            </div>
        </form>
    </div>
</div>
<script src="{{asset('js/main.js')}}"></script>
<script>
    $('.button.dis').click(() => {
        if ($('.form').hasClass('contentChanged')) {
            $('.discard-modal').modal({
                onApprove: () => {
                    $.ajax({
                        type: "get",
                        url: "/admin/practice/link/edit/{{$learning}}/{{$practice->id}}",
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
            }).modal('show')
        } else {
            $.ajax({
                type: "get",
                url: "/admin/practice/link/edit/{{$learning}}/{{$practice->id}}",
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
    })
    $('.form').on('change keyup', function () {
        let selectedInput = $('.dropdown .text').text();
        if (selectedInput.length > 0) {
            $('.form').addClass('contentChanged')
        }
    });
    $('.remove-link').on('click', function() {
        $(this).siblings('.field').find('.text').empty();
        $(this).siblings('.field').find('input[type="hidden"]').val('');
        $('.form').addClass('contentChanged');
    });
</script>