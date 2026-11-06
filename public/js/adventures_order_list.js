$('#season_update_button, #episode_update_button, #chapter_update_button, #scene_update_button').on('click', function () {
    if ($('.form.contentMoved')) {
        let arr = [];
        $('#admin_seasons_list > *').not('.add.ui.basic.button').each(function () {
            arr.push($(this).data('value'));
        });
        $('.array-list').val(JSON.stringify(arr));
        $('.form').submit();
    } else {
        $('.form').submit();
    }
})

    // disable arrow UP / DOWN in edit if filter
    if ($('#adventure_filter').val() == 'all' ){
        $('.arrows .button').removeClass('hid').addClass('vis');
    }else{
        $('.arrows .button').addClass('hid');
    }
    $('#filter').on('change', '.ui.selection.dropdown input[value=all]', function() {
        $('.arrows .button').removeClass('hid').addClass('vis');
    })
    $('#filter').on('change', '.ui.selection.dropdown input[value=unlock]', function() {
        $('.arrows .button').removeClass('vis').addClass('hid');
    })

