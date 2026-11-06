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
    <div class="ui selection dropdown" style="background: var(--episode);">
        <label>TYPE:</label>
        <input type="hidden" name="type" id="episode_type" value="Episode">
        <div class="text">Final Quiz</div>
        <div class="menu">
            <div class="item" data-value="episode">Episode</div>
            <div class="item" data-value="final_quiz">Final Quiz</div>
        </div>
    </div>
</div>
<div class="edit-area adventure">
    <form action="/admin/episode/store/{{$season_id}}" class="ui form" id="episode_create_form" method="post" enctype="multipart/form-data">
        @csrf
        <input hidden value="1" name="type">

        <input hidden value="Final Quiz" name="name_en">
        <div class="two fields">
            <div class="ui field accordion">
                <div class="field char_bg">
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
        </div>
        <div class="buttons action-buttons">
            <button type="button" class="positive ui button save" id="episode_save_button" disabled>SAVE</button>
            <a href="#" class="negative ui button discard" id="episode_cancel_button">CANCEL</a>
        </div>
    </form>
</div>

<script type="application/javascript" src={{asset("js/main.js")}} ></script>
<script>

    $(document).ready(function(){
        $('.ui.dropdown').dropdown({
            onChange: function(){
                $('form').addClass('contentChanged');
                $('.button.save').prop('disabled', false);
            }
        })
    })

    $("#episode_save_button").one('click', function() {
        $('.form').submit();
    })

    $('.button.discard').on('click',function (){
        if ($('.form').hasClass('contentChanged')){
            $(".discard_modal").modal({
                onApprove:function (){
                    $('.edit').empty();
                }
            }).modal('show');
        } else {
            $('.edit').empty();
        }
    })
    $('#episode_type').on('change', function () {
        let editAria = $('#edit')
        editAria.empty();
        $.ajax({
            type: "get",
            url: "/admin/episode/create/" + "{{$season_id}}" ,
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                editAria.append(res);
            }
        });
    })

</script>