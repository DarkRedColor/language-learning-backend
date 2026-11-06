<h1>Create</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<div class="option-area">
    <div class="arrows">
        <div class="big ui left attached icon button">
            <i class="arrow down icon"></i>
        </div>
        <div class="big ui right attached icon button">
            <i class="arrow up icon"></i>
        </div>
        <div class="info">
            <p class="position-list">Position list "the last one"</p>
        </div>
    </div>
    <div class="ui selection dropdown" style="background: var(--quiz);">
        <label>TYPE:</label>
        <input type="hidden" name="type" id="chapter_select_field" value="quiz">
        <div class="text">Quiz</div>
        <div class="menu">
            <div class="item" data-value="chapter">Chapter</div>
            <div class="item" data-value="intro">Intro</div>
            <div class="item" data-value="quiz">Quiz</div>
        </div>
        <i class="chevron down icon"></i>
    </div>
</div>
<div class="edit-area adventure">
    <form action="{{route('chapter-store',[$season_id,$episode_id])}}" class="ui form" id="chapter_create_form"
          method="post" enctype="multipart/form-data">
        @csrf
        <input type="text" hidden name="type" id="chapter_select_type_value" value="1">


        <div class="buttons action-buttons">
            <button type="submit" class="positive ui button save" id="chapter_save_button">SAVE</button>
            <a href="#" class="negative ui button discard" id="chapter_cancel_button">CANCEL</a>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>

    $('.button.discard').on('click', function () {
        if ($('.form').hasClass('contentChanged')) {
            $(".discard_modal").modal({
                onApprove: function () {
                    $('.edit').empty();
                }
            }).modal('show');
        } else {
            $('.edit').empty();
        }
    })

</script>