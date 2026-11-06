<h3>Localization</h3>
<form action="{{route('theory-pages-update',$theoryId)}}" class="ui form" id="localisation">
    @csrf
    <input name="cards-input" value="" hidden/>
    <input type="text" hidden name="is_deleted" id="is_deleted_language_file"/>
    <input type="text" hidden name="dd_file" id="dd_file_language_file"/>
    <div class="field">
        <label>Select new localization</label>
        <div class="ui selection search dropdown">
            @if($language <> null)
                <input type="hidden" name="lcid"
                       value="{{$language}}"
                       id="lcid_code">
            @elseif(isset($languages[1]) and $languages[1]->lcid <> "en")
                <input type="hidden" name="lcid"
                       value="{{$languages[1]->lcid}}"
                       id="lcid_code">
            @endif
            <input class="search">
            <div class="default text">Dialogues</div>
            <div class="menu">
                @foreach($languages as $item)
                    @if($item->lcid <> 'en')
                        @if($loop->index == 0)
                            <div class="item default" data-value="{{$item->lcid}}">{{$item->display_name}}</div>
                        @else
                            <div class="item " data-value="{{$item->lcid}}">{{$item->display_name}}</div>
                        @endif
                    @endif
                @endforeach
            </div>
            <i class="chevron down icon"></i>
        </div>
    </div>
    <div class="cards-area">
        <!--  HERE GOES LIST WITH CARDS  -->
        @php
            $text ='text_' . $languages[1]->lcid;
            if ($language <> null){
                $text = "text_" . $language;
            }
        @endphp
        @if(isset($cards))
            @foreach($cards as $card)
                @switch($card->type)
                    @case(0)
                        @if(isset($card->$text))
                            <div class="title-hidden" id="{{$card->order}}">
                                @foreach(json_decode($card->$text) as $item)
                                    {!!$item!!}
                                @endforeach
                            </div>
                        @elseif(isset($card->text_en))

                            <div class="title-hidden" id="{{$card->order}}">
                                @foreach(json_decode($card->text_en) as $item)
                                    {!!$item!!}
                                @endforeach
                            </div>
                        @endif
                        @break
                    @case(1)
                        @if(isset($card->$text))
                            <div class="description-hidden" id="{{$card->order}}">
                                @foreach(json_decode($card->$text) as $item)
                                    {!!$item!!}
                                @endforeach
                            </div>
                        @elseif(isset($card->text_en))
                            <div class="description-hidden" id="{{$card->order}}">
                                @foreach(json_decode($card->text_en) as $item)
                                    {!!$item!!}
                                @endforeach
                            </div>
                        @endif
                        @break
                    @case(2)
                        @if(isset($card->$text))
                            <div class="example-hidden" id="{{$card->order}}">
                                @foreach(json_decode($card->$text) as $item)
                                    {!!$item!!}
                                @endforeach
                            </div>
                        @elseif(isset($card->text_en))
                            <div class="example-hidden" id="{{$card->order}}">
                                @foreach(json_decode($card->text_en) as $item)
                                    {!!$item!!}
                                @endforeach
                            </div>
                        @endif
                        @break
                    @case(3)
                        @if(isset($card->image))
                            <div class="image-card" id="{{$card->order + 1}}">
                                <label id="dropped" class="add-bg ">
                                    {{--                                    <input type="file" name="image" accept="image/png"--}}
                                    {{--                                           onchange="previewFile(this);$('#delete_flag_of_language').remove()"--}}
                                    {{--                                           placeholder="IMG">--}}
                                    <i class="upload icon">
                                        <img class="previewImg" data-value="" src="{{$card->image}}_S">
                                    </i>
                                </label>
                                <a class='del-card image'><i class='trash alternate icon red'></i></a>
                            </div>
                        @elseif(isset($card->text_en))

                            <div class="image-hidden" id="{{$card->order}}">
                                @foreach(json_decode($card->text_en) as $item)
                                    {!!$item!!}
                                @endforeach
                            </div>
                        @endif
                        @break
                    @case(4)
                        @if(isset($card->$text))
                            <div class="table-hidden" id="{{$card->order}}">
                                @foreach(json_decode($card->$text) as $item)
                                    {!!$item!!}
                                @endforeach
                            </div>
                        @elseif(isset($card->text_en))
                            <div class="table-hidden" id="{{$card->order}}">
                                @foreach(json_decode($card->text_en) as $item)
                                    {!!$item!!}
                                @endforeach
                            </div>
                        @endif
                        @break
                    @default
                        @break
                @endswitch
            @endforeach
        @endif
    </div>
    <div class="buttons action-buttons">
        <button type="button" class="positive ui button save">SAVE</button>
        <button type="button" class="negative ui button disabled">CANCEL</button>
    </div>
</form>

<script src="{{asset('js/main.js')}}"></script>
<script src="{{asset('js/description_editor.js')}}"></script>
<script src="{{asset('js/title_editor.js')}}"></script>
<script src="{{asset('js/example_editor.js')}}"></script>
<script src="{{asset('js/table_editor.js')}}"></script>

<script>
    // style for AR language
    $(document).ready(function(){
        if ($('#localisation .search.dropdown .text').text().indexOf('Arabic') >= 0) {
            $('#localisation > *').siblings('.cards-area').find('.ck-editor p').each(function(e) {
                $(this).attr('dir', 'rtl').css('text-align', 'right')
            })
            $('.ck-content').on('DOMSubtreeModified', function(){
                $(this).find('p').attr('dir', 'rtl').css('text-align', 'right')
            })
        }
    })
    // ----------------------------
    var imageTemplate = `
      <div class="image-card">
            <label id="dropped" class="add-bg ">
                <input type="file" name="image" accept="image/png"
                       onchange="previewFile(this);$('#delete_flag_of_language').remove()" placeholder="IMG">
                <i class="upload icon">
                    <img class="previewImg" data-value="" src="">
                </i>
                <a href="#" class="dis-img" onclick="delLanguageFlag()"><i class="close icon"></i></a>
            </label>
            <a class='del-card image'><i class='trash alternate icon red'></i></a>
        </div>
    `;
    // appendImage
    $('.image-selector').on('click', function () {
        $(".cards-area").append(imageTemplate).after(function () {
            $('.image-card').attr('id', function () {
                return $(this).index() + 1
            });
        });
        $('.preview-info').append($('<div/>').addClass("image-view")).after(function () {
            $('.image-view').attr('id', function () {
                return $(this).index() + 11
            });
        });

    })
    setTimeout(function () {
        $('.cards-area').on('DOMSubtreeModified', function () {
            $('#localisation').addClass('contentChanged');
            $('.discard.button').removeClass('disabled');
            $('.negative.button').removeClass('disabled');
        })
    }, 1500)
    $('.negative.button').on('click', function () {
        if ($('.form').hasClass('contentChanged')) {
            $('.discard-modal').modal({
                onApprove: function () {
                    location.reload();
                }
            }).modal('show');
        } else {
            location.reload();
        }
    })

    $('.positive.save').on('click', function () {
        let elements = $("#localisation .ck-content > p, #localisation .ck-content table, #localisation .image-card .previewImg");
        let toInput = $('input[name="cards-input"]');
        let lcid = $('#lcid_code').val()
        let arr = [];
        for (let i = 0; i < elements.length; i++) {
            arr.push({
                "index":
                    $(elements[i]).parents("div[class$='-card']").index(),

                "type":
                    $(elements[i]).parents("div[class$='-card']").attr('class'),

                "content":
                elements[i].outerHTML,

            });
        }
        toInput.val(JSON.stringify(arr))
        let data = {
            "cards-input": JSON.stringify(arr),
            "language": lcid,
            "theoryPageStack": "{{$theoryPageStackId}}"
        };
        $.ajax({
            type: "post",
            url: "{{route('theory-pages-update',$theoryId)}}",
            data: JSON.stringify(data),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function () {
                location.reload();
            }
        })
    })

    $('#lcid_code').on('change', function () {
        let lcid = $('#lcid_code').val();
        if ($('#localisation').hasClass('contentChanged')) {
            $('.discard-modal').modal({
                onApprove: function () {
                    $.ajax({
                        type: "POST",
                        url: "/admin/theory/pages/create/localisation/{{$theoryId}}/{{$theoryPageStackId}}/" + lcid,
                        data: JSON.stringify("{{$ids}}"),
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            let form = $('.local-add');
                            form.empty();
                            form.append(res)
                        }
                    })
                }
            }).modal('show');
        } else {
            $.ajax({
                type: "POST",
                url: "/admin/theory/pages/create/localisation/{{$theoryId}}/{{$theoryPageStackId}}/" + lcid,
                data: JSON.stringify("{{$ids}}"),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    let form = $('.local-add');
                    form.empty();
                    form.append(res)
                }
            })
        }
    })

</script>
