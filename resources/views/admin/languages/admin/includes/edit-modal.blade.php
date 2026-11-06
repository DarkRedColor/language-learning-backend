<h1>Edit {{$language->display_name}}</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<form class="ui form" action="/admin/language/update/{{$language->id}}" id="admin_lang_form"
      data-value="{{$language->id}}" method="post">
    @csrf
    <div class="two fields">
        <div class="field">
            <label>lcid ( уникальный, нельзя изменить )</label>
            <input placeholder="Type lcid" type="text" name="lcid" value="{{$language->lcid}}" disabled>
        </div>
        <div class="field">
            <label>Display name</label>
            <input placeholder="Name to display" type="text" name="display_name" value="{{$language->display_name}}">
        </div>
    </div>
    <div class="two fields">
        <div class="field">
            <label>ISO 639-1</label>
            <input placeholder="iso 639-1" type="text" name="iso_639_1" value="{{$language->iso_639_1}}">
        </div>
        <div class="field">
            <label>ISO 639-2</label>
            <input placeholder="iso 639-2" type="text" name="iso_639_2" value="{{$language->iso_639_2}}">
        </div>
    </div>
    <div class="buttons action-buttons">
        <button type="submit" class="positive ui button save">save</button>
        <a href="#" class="negative ui button delete" id="admin_delete_language">delete</a>
    </div>
</form>


<script>
    $("#admin_delete_language").on('click', function () {
        let id = $('#admin_lang_form').attr('data-value');
        $.ajax({
            type: "post",
            url: "/admin/language/delete/" + id,
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function () {
                location.reload();
            },
        });
    });

    $('.exit').on('click', function () {
        location.reload();
    })
</script>