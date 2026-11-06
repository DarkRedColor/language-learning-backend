<h1>Add language</h1>
<button class="ui icon basic button exit"><i class="close icon"></i></button>
<form class="ui form" action="/admin/language/store" method="post">
    @csrf
    <div class="two fields">
        <div class="field">
            <label>lcid ( уникальный, нельзя изменить )</label>
            <input placeholder="Type lcid" type="text" name="lcid" id="admin_language_lcid_input_field">
        </div>
        <div class="field">
            <label>Display name</label>
            <input placeholder="Name to display" type="text" name="display_name"
                   id="admin_language_display_name_input_field">
        </div>
    </div>
    <div class="two fields">
        <div class="field">
            <label>ISO 639-1</label>
            <input placeholder="iso 639-1" type="text" name="iso_639_1">
        </div>
        <div class="field">
            <label>ISO 639-2</label>
            <input placeholder="iso 639-2" type="text" name="iso_639_2">
        </div>
    </div>
    <div class="buttons action-buttons">
        <button type="submit" class="positive ui button" id="admin_language_create_button" disabled>save</button>
    </div>
</form>

<script>

    $('.exit').on('click', function () {
        location.reload();
    })

    $(document).ready(function () {
        let i = 0;
        let j = 0;

        $('#admin_language_lcid_input_field').on('keyup', function () {
            if ($(this).val() === '') {
                i = 0;
            } else {
                i = 1;
            }
            removeDisabled()
        })
        $('#admin_language_display_name_input_field').on('keyup', function () {
            if ($(this).val() === '') {
                j = 0;
            } else {
                j = 1;
            }
            removeDisabled()
        })

        function removeDisabled() {
            if (i === 1 && j === 1) {
                $("#admin_language_create_button").removeAttr('disabled');
            } else {
                $("#admin_language_create_button").attr('disabled', 'true');
            }
        }
    })


</script>