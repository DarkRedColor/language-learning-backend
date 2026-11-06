<div class="edit">
    <h1>Create</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <form action="{{route('practice-store')}}" class="ui form" method="post" id="practice_form">
        @csrf
        <div class="option-area theory-option">
            <div class="ui selection dropdown" style="background: var(--action)">
                <label>TYPE: </label>
                <div class="text">Practice</div>
            </div>
        </div>
        <div class="edit-area">
            <div class="two fields">
                <div class="ui field">
                    <label>Name Practice</label>
                    <input placeholder="Word name" type="text" name="name">
                </div>
            </div>
            <div class="buttons action-buttons">
                <button type="submit" class="positive ui button save" disabled>SAVE</button>
                <a href="#" class="grey ui button" id="theory_cancel">Cancel</a>
            </div>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script type="application/javascript" src={{asset("js/search_result.js")}} ></script>

<script>
    // add class && show button
    $('.form input[name]').not('.hidden').on('keyup change', function () {
        if ($(this).val() !== '') {
            $('.form').addClass('contentChanged')
            $('.button.save').attr('disabled', false)
        } else {
            $('.button.save').attr('disabled', true)
        }
    })

    $('#theory_cancel').on('click', function () {
        if ($('#practice_form').hasClass('contentChanged')) {
            $(".discard-modal").modal({
                onApprove: function () {
                    location.reload();
                }
            }).modal('show');
        } else {
            location.reload();
        }
    })
</script>
