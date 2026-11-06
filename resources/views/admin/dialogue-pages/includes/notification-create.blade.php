<div class="edit">
    <h1>Create</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="option-area">
        <div class="arrows">
            <div class="info">
                <p>
                    New Page
                </p>
            </div>
        </div>

        <div class="ui selection dropdown" style="background: var(--notification);">
            <input type="hidden" name="type" value="notification" id="dialogue_type_select">
            <div class="default text">Type</div>
            <div class="menu">
                <div class="item" data-value="line">Line</div>
                <div class="item" data-value="action">Action</div>
                <div class="item" data-value="notification">Notification</div>
                <div class="item" data-value="choice">Choice</div>
            </div>
            <i class="chevron down icon"></i>
        </div>
    </div>

    <div class="edit-area">
        <form action="{{route('dialogue-page-store')}}" class="ui form" method="POST" id="dialogue_create_form">
            @csrf
            <input type="hidden" name="tree" value="" id="create_tree">
            <input type="hidden" name="content_type" value="notification" id="content_type">
            <input type="hidden" name="sub_chapter_id" value="" id="dialogue_sub_id">
            <input type="hidden" name="parent_id" value="" id="dialogue_parent_id">
            <div class="two fields">
                <div class="field options char_bg">
                    <div class="field">
                        <label>Background</label>

                        <div class="ui selection search dropdown">
                            <input type="hidden" name="background_id" value="" id="background_id">
                            <div class="default text">Background</div>
                            <div class="menu">
                                @foreach($bg as $item)
                                    <div class="item" data-value="{{$item['id']}}">{{$item['name']}}</div>
                                @endforeach
                            </div>
                            <i class="chevron down icon"></i>
                        </div>
                    </div>
                </div>
                <div class="ui field accordion">
                    <div class="title inputs">
                        <div class="field main-local">
                            <label>Localization</label>
                            <textarea rows="2" name="text_en"></textarea>
                            <div class="ui label lang-iso">EN
                                <i class="chevron down icon"></i>
                            </div>
                        </div>
                    </div>
                    <div class="content field inputs">
                        @foreach($langs as $item)
                            @if($item['lcid'] <> "en")
                                @php
                                    $textIso ="text_" . $item['lcid']
                                @endphp
                                <div class="field">
                                        <textarea rows="2"
                                                  name="text_{{$item['lcid']}}"></textarea>
                                    <div class="ui label lang-iso">{{$item['lcid']}}</div>
                                </div>

                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="buttons action-buttons">
                <button type="submit" class="positive ui button save" id="save">SAVE</button>
                <button class="negative ui button" id="cancel_dialogue_pages" type="button">CANCEL</button>
            </div>
        </form>
    </div>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>
    $(document).ready(function () {
        // background
        var cashBg = localStorage.getItem('bg');
        $('.char_bg .text').removeClass('default').html(cashBg);
        var cashBgId = localStorage.getItem('bg_id');
        $('#background_id').val(cashBgId)

        $('#dialogue_sub_id').val($('#dialogue_sub_chapter_id').val())
        $('#dialogue_parent_id').val($('#dialogue_index_parent_id').val())
        $('.button.save, .button.discard').attr('disabled', true)
        if(localStorage.length > 0){
            $('.button.save, .button.discard').attr('disabled', false)
        }else{
            $('.button.save, .button.discard').attr('disabled', true)
        }

        $('#cancel_dialogue_pages').on('click', function () {
            $('.discard-modal').modal({
                onApprove: function () {
                    $('#edit').empty();
                }
            }).modal('show');
        })

    })
    // add class && show button
    $('.form input, textarea').not('.hidden').on('keyup change', function () {
        // catch background
        localStorage.setItem('bg', $('.char_bg .text').html());
        localStorage.setItem('bg_id', $('#background_id').val());

        $('.form').addClass('contentChanged')
        $('.button.save, #cancel_dialogue_pages').attr('disabled', false)
    })
</script>