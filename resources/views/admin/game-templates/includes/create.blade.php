<div class="edit">
    <h1>Create</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area">
        <h3>Game creation</h3>
    </div>
    <div class="edit-area grid">
        <form action="{{route('game-templates-store')}}" class="ui form game_creation eight wide column" method="post">
            @csrf
            <div class="ui field eight wide column">
                <label>NAME*</label>
                <input placeholder="Name" type="text" class="game_name" name="name"
                       value="">
            </div>
            <div class="grouped fields tasks">
                <h3>TASK*</h3>
                <div class="field">
                    <div class="ui radio checkbox">
                        <input type="radio" name="task" value="image">
                        <label>IMG</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui radio checkbox">
                        <input type="radio" name="task" value="audio">
                        <label>Audio</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui radio checkbox">
                        <input type="radio" name="task" value="video">
                        <label>Video</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui radio checkbox">
                        <input type="radio" name="task" checked="checked" value="text">
                        <label>Text</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui radio checkbox space">
                        <input type="radio" name="task" value="space">
                        <label>Space</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui radio checkbox link-words">
                        <input type="radio" name="task" value="link_words">
                        <label>Link Words</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui radio checkbox symbols">
                        <input type="radio" name="task" value="symbols">
                        <label>Symbols</label>
                    </div>
                </div>
            </div>
            <div class="field">
                <div class="ui checkbox additional_task">
                    <input type="checkbox" name="additional_task" value="true">
                    <label><h3>Additional Task*</h3></label>
                </div>
            </div>
            <div class="inline fields additional_task_list">
                <div class="field">
                    <div class="ui radio disabled checkbox">
                        <input type="radio" name="additional" value="text">
                        <label>Text</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui radio disabled checkbox">
                        <input type="radio" name="additional" value="audio">
                        <label>Audio</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui radio disabled checkbox">
                        <input type="radio" name="additional" value="image">
                        <label>IMG</label>
                    </div>
                </div>
            </div>
            <h3>ANSWER*</h3>
            <div class="two fields eight wide">
                <div class="grouped fields">
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input type="radio" name="answer" checked="checked" value="type">
                            <label>Type</label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input type="radio" name="answer" value="erudite">
                            <label>Erudite</label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input type="radio" name="answer" value="sentence">
                            <label>Sentence</label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input type="radio" name="answer" value="words">
                            <label>Words</label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input type="radio" name="answer" value="remove">
                            <label>Remove</label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input type="radio" name="answer" value="true_false">
                            <label>True/false</label>
                        </div>
                    </div>
                </div>
                <div class="grouped fields">
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input type="radio" name="answer" value="2">
                            <label>2</label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input type="radio" name="answer" value="3">
                            <label>3</label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input type="radio" name="answer" value="4">
                            <label>4</label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input type="radio" name="answer" value="5">
                            <label>5</label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input type="radio" name="answer" value="matched_not">
                            <label>Matched/not</label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input type="radio" name="answer" value="list">
                            <label>List</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="buttons action-buttons">
                <button type="submit" class="positive ui button save" disabled>SAVE</button>
                <button type="button" href="#" class="grey ui button cancel">CANCEL</button>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript" src="{{asset('js/main.js')}}"></script>
<script>
    $('.radio').checkbox();
    // additional tasks check
    var additionalTask = $('.additional_task_list').find('.field:first-child .radio');
    $('.additional_task').checkbox({
        onChecked: function () {
            additionalTask.checkbox('check');
            $('.additional_task_list').find('.radio').removeClass('disabled');
        },
        onUnchecked: function () {
            additionalTask = $('.additional_task_list').find('.field .radio');
            additionalTask.checkbox('uncheck');
            $('.additional_task_list').find('.radio').addClass('disabled');
        }
    });

    // link words / Space check / symbols
    $('.link-words, .space, .symbols').checkbox({
        onChecked: function () {
            $('.additional_task input').prop("checked", true);
            $('.additional_task_list').find('.radio').removeClass('disabled');
            additionalTask.checkbox('check');
        }
    })
    $('.game_name').on('keyup', function () {
        if ($(this).val().length > 0) {
            $('.form').addClass('contentChanged')
            $('.button.save').attr('disabled', false)
        } else {
            $('.button.save').attr('disabled', true)
        }
    })
    $('.cancel').on('click', function () {
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
</script>