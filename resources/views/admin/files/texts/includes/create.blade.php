<div class="edit">
    <h1>Edit</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area">
        <h3>Text file type</h3>
    </div>
    <div class="edit-area games">
        <form action="{{route('file-text-store')}}" class=" ui form game_creation text-file" method="post">
            @csrf
            <input type="text" name="text" class="hid text">
            <input type="text" name="images" class="hid images">
            <div class="two fields">
                <div class="ui field">
                    <div class="field">
                        <label>Name text</label>
                        <input type="text" placeholder="Name" name="name">
                    </div>
                    <div class="editor files-text">
                        <div id="toolbar-container"></div>
                        <div id="editor"></div>
                    </div>
                </div>
                <div class="ui field legend">
                    <h3>legend</h3>
                </div>
            </div>
            <div class="buttons action-buttons sixteen wide column">
                <button type="button" href="#" class="positive ui button save" disabled>SAVE</button>
                <button type="button" href="#" class="grey ui button cancel">CANCEL</button>
            </div>
        </form>
    </div>
</div>
<script type="application/javascript" src="{{asset('js/texts_editor.js')}}"></script>
<script>
    setTimeout(function(){
        $('.form, .form input').on('DOMSubtreeModified keyup', function() {
            $(this).addClass('contentChanged')
            $('.button.dis, .button.save').attr('disabled', false)
        })
    }, 500);
    // $(document).ready(function () {
    //     $('.form input').on('keyup', function () {
    //         if ($('.form input').not('.ck-hidden, .hid, [type="hidden"]').val().length > 0) {
    //             $('.button.save').attr('disabled', false);
    //         } else {
    //             $('.button.save').attr('disabled', true);
    //         }
    //     })
    // })
        $('.button.save').one('click', function () {
            var images = []
            $('.ck.ck-content img').each(function () {
                images.push($(this).attr('src'))
            })

            var text = []
            $('.ck.ck-content').children().not('.ck-fake-selection-container').each(function () {
                $('.ck.ck-content img').replaceWith('@@').after(function () {
                    $('[class^="ck"]').remove()
                })
                text.push($(this).html().replace('/<p>(.*?)<\/p>/g', '(.*?)'))
            })

            $('.hid.text').val(JSON.stringify(text))
            $('.hid.images').val(JSON.stringify(images))

            $('.form').submit();
        })
    $('.exit').click(() => {
        location.reload()
    })
    $('.button.cancel').click(() => {
        if($('.form').hasClass('contentChanged')){
            $('.discard-modal').modal({
                onApprove: () => {
                    location.reload()
                }
            }).modal('show')
        }else{
            location.reload()
        }
    })


</script>