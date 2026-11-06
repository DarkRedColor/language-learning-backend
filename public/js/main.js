$(document).ready(function () {
    $('#arcade_type_select').on('change', function () {
        let editAria = $('#arcade_storage_edit_create_zone')
        editAria.empty();
        let content_type = $(this).val();
        $.ajax({
            type: "get",
            url: "/admin/arcade-games/page/create-and-edit/" + content_type,
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                editAria.append(res);
            }
        });
    })
    $('#dialogue_type_select').on('change', function () {
        let editAria = $('#edit')
        editAria.empty();
        let content_type = $(this).val();
        $.ajax({
            type: "get",
            url: "/admin/dialogue/page/create-and-edit/" + content_type,
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                editAria.append(res);
            }
        });
    })
    // ------- style for RTL languages
    $('form .lang-iso').each(function() {
        if ($(this).text().indexOf('ar-AE') >= 0) {
            $(this).siblings('textarea, input').each(function() {
                $(this).attr('dir', 'rtl')
            })
        }
    })
    // ------------------------------
    // show popup for delete buttons "Lena"
    $('.lena').popup({
        on: 'click',
        position: 'bottom center',
        content: 'Only Admin can delete',
    })

    // scroll to view
    // $('.lang-list').animate({
    //     scrollTop: ($('.lang-list .buttons .ui.basic.label.check').offset().top - 150)
    // },500);

    // backgrounds

    $('.ui.sidebar').sidebar({
        dimPage: true,
        transition: 'scale down',
        onHidden: function (){
            $('#background_update .filepond--root, #emotion_update .filepond--root').remove();
            $('#background_update, #emotion_update').prepend(`<input type="file" name="image[]" accept="image/png" multiple data-max-file-size="10MB" data-max-files="1" class="img-update-input">`)
        },
    });
    $('.bg-cards img').click(() => {
        $('.ui.sidebar').sidebar('toggle');
    });

    // Backgrounds show/hide card buttons
    $('#dropped').on('change', function () {
        $("#dropped").attr("src", $("#image").val('src'));
        $('#dropped').removeClass('add-bg').addClass('vis')
    })
    $('.bg-cards').on('click', function () {
        $(this).parent().find('.bg-cards').removeClass("check");
        $(this).addClass('check')
    })

    $('.bg-cards img, #background_cancel_edit_form').click(() => {
        $('.ui.sidebar').sidebar('toggle');
        $('.upload-edit-btn').addClass('disabled')
        $('#dropped-edit .plus').removeClass('bg-error')
    });

    // All discard MODAL
    // var editForm = '#scene_edit_form, #chapter_edit_form, #episode_edit_form, #season_edit_form';
    // var createForm = '#scene_create_form, #chapter_create_form, #episode_create_form, #season_edit_create_zone';
    $('.main-menu a[href]:not([href$="\\#"]), .tree a[href]:not([href$="\\#"])').on('click', function () {
        if ($('.form').hasClass('contentChanged')) {
            //return window.confirm('Unsaved data \n You really want to continue?');
            event.preventDefault();
            var link = $(this).attr("href");
            $('.basic-error').modal({
                onApprove: function () {
                    window.location = link;
                }
            }).modal('show');
        }
    })
    // END discard MODAL


    // END discard MODAL


    $('#filter').on('change', '.ui.selection.dropdown input[value=unlock]', function () {
        $('#filter .ui.selection.dropdown').removeClass('all').addClass('unlock');
        $('#admin_seasons_list').find('button.lock').parent().addClass('hid');
        $('#admin_seasons_list').find('button.un-lock').parent().addClass('vis');
    });
    $('#filter').on('change', '.ui.selection.dropdown input[value=all]', function () {
        $('#filter .ui.selection.dropdown').removeClass('unlock').addClass('all');
        $('#admin_seasons_list').find('button.lock').parent().removeClass('hid');
        $('#admin_seasons_list').find('button.un-lock').parent().removeClass('vis');
    });


    if ($('#adventure_filter').val() == 'unlock') {
        $('#filter .dropdown').addClass('unlock');
        $('#admin_seasons_list').find('button.lock').parent().addClass('hid');
        $('#admin_seasons_list').find('button.un-lock').parent().addClass('vis');
    } else {
        $('#filter .dropdown').removeClass('unlock');
    }


    // dropdown colors type
    $('.option-area .dropdown').on('change', 'input', function () {
        $(this).parent().css('background', 'var(--' + $(this).val() + ')')
    });

    $('.ui.accordion').accordion( {
        selector: {
            trigger: '.choice .icon,.scene-name > i.chevron.down.icon, .lang-iso, .files-menu'
        }
    });
    $('.form .accordion > *').addClass('active')

    $('.type.dropdown, .ui.dropdown').dropdown({
        fullTextSearch: true,
        autocomplete: true
    });

    $('.lockbutton').on('click', function () {
        let id = $(this).attr('id');
        $('.state-visible').modal({
            onApprove: function () {
                $.ajax({
                    type: "post",
                    url: "/admin/main/menu/language/displayed/" + id + "/yes",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        location.reload()
                    }
                });
            }
        }).modal('show')
    });

    $('.unlockbutton').on('click', function () {
        let id = $(this).attr('id');
        $('.state-hidden').modal({
            onApprove: function () {
                $.ajax({
                    type: "post",
                    url: "/admin/main/menu/language/not/displayed/" + id,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        location.reload()
                    }
                });
            }
        }).modal('show')
    });
    $('.lockbuttonweb').on('click', function () {
        let id = $(this).attr('id');
        $('.state-visible').modal({
            onApprove: function () {
                $.ajax({
                    type: "post",
                    url: "/admin/main/menu/language/web/displayed/" + id + "/yes",
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        location.reload()
                    }
                });
            }
        }).modal('show')
    });

    $('.unlockbuttonweb').on('click', function () {
        let id = $(this).attr('id');
        $('.state-hidden').modal({
            onApprove: function () {
                $.ajax({
                    type: "post",
                    url: "/admin/main/menu/language/web/not/displayed/" + id,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        location.reload()
                    }
                });
            }
        }).modal('show')
    });

    $('.delete.intro').click(function () {
        let id = $('#intro-id').val();
        $('.delete-modal').modal({
            onApprove: function () {
                $.ajax({
                    type: "POST",
                    url: "/admin/intro/delete/" + id,
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        location.reload();
                    }
                });
            }
        }).modal('show');
    });

    // let idArr = ['#del, #delete, #admin_delete_lang, #season_delete_modal, #text_delete_modal, #discard_modal, #is_displayed_to_user_lang_modal'];
    //
    // $('#season_delete_modal').modal({
    //
    //     closable: false
    //
    // })

    $("#save").on('click', function () {
        let id = $('#choice_id').val();
        let elem = $("#" + id);
        if (elem.length !== 0) {
            let classList = elem.attr("class");
            let classArr = classList.split(/\s+/);
            let type = classArr[0]
            let tree = elem.parent().children(":not(#add-page)");
            if (type === 'choice') {
                tree = $($(elem.parent()).parent()).parent().children(":not(#add-page)");
            }
            let data = treeConstruct(tree);
            $("#create_tree").val(JSON.stringify(data))
        }
        $("#choice_id_form").val(id);
        $('#init_in_form').val($('#init').val())

        $("#myForm").submit();
    })

    $("#update").on('click', function () {
        let type = $('#update_select').val()
        $("#type").val(type)
        let id = $("#intro-id").val()
        let elem = $("#" + id);
        let data;
        let form = $("#updateForm");
        if (type !== 'choice') {
            let tree = elem.parent().children(":not(#add-page)");
            data = treeConstruct(tree);
        } else {
            let tree = $($(elem.parent()).parent()).parent().children(":not(#add-page)");
            data = treeConstruct(tree);
        }
        // }
        $("#tree").val(JSON.stringify(data))

        form.submit();
    })

    $('#update_select').on('change', function () {
        let type = $(this).val();
        let id = $("#intro-id").val();
        let form = $('#updateForm .field');
        let data = formValues(form);
        $.ajax({
            type: "post",
            url: "/admin/intro/edit/modal/form/" + id + "/" + type,
            contentType: 'application/json',
            data: JSON.stringify({data: data}),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                let edit = $('.edit-area');
                edit.empty();
                edit.append(res);
            }
        });
    })
    $("#arrow_down").on('click', function () {
        $('#updateForm').addClass('contentMoved');
        $('#myForm').addClass('contentMoved');
        $('#update').removeAttr('disabled');
        $('#discardsss').removeAttr('disabled');
        let elem = $("#" + $(this).attr('name'));
        let prev = elem.prev();
        let data = elem.prev().attr('id');
        let pos = $('#pos');
        if (pos.val() === '') {
            if (data !== undefined) {
                if (data !== "choice") {
                    pos.val(data)
                } else {
                    let classList = prev.attr("class");
                    let classArr = classList.split(/\s+/);
                    $("#prev_position_type").val('choice')
                    data = [classArr[0]];
                    pos.val(data)
                }
            } else {
                $("#down").val("1");
                data = elem.next().attr('id');
                let next = elem.next();
                if (data !== "choice") {
                    pos.val(data)
                } else {
                    let classList = next.attr("class");
                    let classArr = classList.split(/\s+/);
                    $("#prev_position_type").val('choice')
                    data = [classArr[0]];
                    pos.val(data)
                }
                pos.val(data)
            }
        }
        let classList = elem.attr("class");
        let classArr = classList.split(/\s+/);
        let type = classArr[0];
        let choiceElem;
        if (type !== 'choice') {
            if (elem.next().attr('id') !== "add-page") {
                elem.insertAfter(elem.next());
            }
        } else {
            choiceElem = $(elem.parent()).parent()

            if (choiceElem.next().attr('id') !== "add-page") {
                choiceElem.insertAfter(choiceElem.next());
            }
        }

    })

    $("#arrow_up").on('click', function () {

        let elem = $("#" + $(this).attr('name'));
        let classList = elem.attr("class");
        let classArr = classList.split(/\s+/);
        let type = classArr[0];
        let choiceElem;
        if (type !== 'choice') {
            if (elem.prev().attr('id') !== "content") {
                elem.insertBefore(elem.prev());
                $('#updateForm').addClass('contentMoved');
                $('#myForm').addClass('contentMoved');
                $('#update').removeAttr('disabled');
                $('#discardsss').removeAttr('disabled');
            }
        } else {
            choiceElem = $(elem.parent()).parent()
            if (choiceElem.prev().attr('id') !== "content") {
                choiceElem.insertBefore(choiceElem.prev());
                $('#updateForm').addClass('contentMoved');
                $('#myForm').addClass('contentMoved');
                $('#update').removeAttr('disabled');
                $('#discardsss').removeAttr('disabled');
            }
        }
    })

    $("#select").on('change', function () {
        let type = $(this).val();
        let id = $("#choice_id").val();
        let next_id = $(this).attr('next_id')
        let initial = $("#init").val();
        let form = $('#myForm .field');
        let data = formValues(form);
        let subId = $('#sub_id').val();
        $.ajax({
            type: "post",
            url: "/admin/intro/create/modal/" + type + "/" + id + "/" + next_id + "/" + initial + "/" + subId,
            contentType: 'application/json',
            data: JSON.stringify({data: data}),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (type === "line") {
                    let edit = $('.edit');
                    edit.empty();
                    edit.append(res);
                } else {
                    let edit = $('.edit-area');
                    edit.empty();
                    edit.append(res);
                }
            }
        });
    });

    $(".exit").on("click", function () {
        location.reload();
    })

    $('#create_cancel').on('click', function () {
        if ($('#myForm').hasClass('contentChanged')) {
            $('.discard-modal').modal({
                onApprove: function () {
                    location.reload();
                }
            }).modal("show");
        } else {
            location.reload();
        }

    })

    $(".discard").on('click', function () {
        if ($('.form').hasClass('contentChanged')) {
            $('.discard-modal').modal({
                onApprove: function () {
                    let uForm = $(".form").find('input:not([type$="hidden"])');
                    uForm.each(function () {
                        $(this).val($(this).attr("data-value"))
                        if ($(this).next().hasClass('text')) {
                            $($(this).next()).text($(this).next().attr("data-value"))
                        }
                    })
                    let textareaUForm = $(".form").find("textarea");
                    textareaUForm.each(function () {
                        $(this).val($(this).attr('data-value'))
                    })
                    $(".discard").attr('disabled', 'true');
                    $('.save').attr('disabled', 'true');
                    $('#updateForm').removeClass('contentChanged');
                    $('#myForm').removeClass('contentChanged');
                }
            }).modal('show');
        }
        if ($('#updateForm').hasClass('contentMoved') || $('#myForm').hasClass('contentMoved')) {
            location.reload();
            // let pos = $('#pos');
            // let down = $('#down');
            // let prev_position_type = $('#prev_position_type');
            // let type = $("#update_select").val()
            //
            // if (prev_position_type.val() === "choice") {
            //     if (down.val() === "1") {
            //         let id = $("#intro-id").val();
            //         $('#' + id).insertBefore($("." + pos.val()));
            //     } else {
            //         let id = $("#intro-id").val();
            //         $('#' + id).insertAfter($("." + pos.val()));
            //     }
            // } else {
            //     if (down.val() === "1") {
            //         let id = $("#intro-id").val();
            //         if (type !== 'choice') {
            //             $('#' + id).insertBefore($("#" + pos.val()));
            //         } else {
            //             console.log(pos.val());
            //             // $('.'+id).insertBefore($('#' + pos.val()))
            //         }
            //
            //     } else {
            //         let id = $("#intro-id").val();
            //         if (type !== 'choice') {
            //             $('#' + id).insertAfter($("#" + pos.val()));
            //         } else {
            //             console.log("u")
            //         }
            //     }
            // }
            // // } else {
            // //     let choice = $('.' + pos.val())
            // //
            // //     // console.log(choice);
            // //     // choice.insertBefore($("." + pos.val()));
            // // }
        }
    });
    $(".discard").on('click', function () {
        if ($('#updateForm').hasClass('contentChanged') || $('#myForm').hasClass('contentChanged')) {
            $('.discard-modal').modal({
                onApprove: function () {
                    let uForm = $("#updateForm").find("input");
                    uForm.each(function () {
                        $(this).val($(this).attr("data-value"))
                        if ($(this).next().hasClass('text')) {
                            $($(this).next()).text($(this).next().attr("data-value"))
                        }
                    })
                    let textareaUForm = $("#updateForm").find("textarea");
                    textareaUForm.each(function () {
                        $(this).val($(this).attr('data-value'))
                    })
                    $(".discard").attr('disabled', 'true');
                    $('.save').attr('disabled', 'true');
                    $('#updateForm').removeClass('contentChanged');
                    $('#myForm').removeClass('contentChanged');
                }
            }).modal('show');
        }
        if ($('#updateForm').hasClass('contentMoved') || $('#myForm').hasClass('contentMoved')) {
            location.reload();
            // let pos = $('#pos');
            // let down = $('#down');
            // let prev_position_type = $('#prev_position_type');
            // let type = $("#update_select").val()
            //
            // if (prev_position_type.val() === "choice") {
            //     if (down.val() === "1") {
            //         let id = $("#intro-id").val();
            //         $('#' + id).insertBefore($("." + pos.val()));
            //     } else {
            //         let id = $("#intro-id").val();
            //         $('#' + id).insertAfter($("." + pos.val()));
            //     }
            // } else {
            //     if (down.val() === "1") {
            //         let id = $("#intro-id").val();
            //         if (type !== 'choice') {
            //             $('#' + id).insertBefore($("#" + pos.val()));
            //         } else {
            //             console.log(pos.val());
            //             // $('.'+id).insertBefore($('#' + pos.val()))
            //         }
            //
            //     } else {
            //         let id = $("#intro-id").val();
            //         if (type !== 'choice') {
            //             $('#' + id).insertAfter($("#" + pos.val()));
            //         } else {
            //             console.log("u")
            //         }
            //     }
            // }
            // // } else {
            // //     let choice = $('.' + pos.val())
            // //
            // //     // console.log(choice);
            // //     // choice.insertBefore($("." + pos.val()));
            // // }
        }
    });

    var $form = $('#updateForm'),
        origForm = $form.serialize();
    $('form :input').on('change input', function () {

        if ($form.serialize() !== origForm) {
            $('#myForm').addClass('contentChanged');
            $('#updateForm').addClass('contentChanged');
            $('#update').removeAttr('disabled');
            $('#discardsss').removeAttr('disabled');
        }
    });
    var $forms1 = $('#myForm'),
        origFormsIC = $forms1.serialize();

    $('form :input').on('change input', function () {

        if ($forms1.serialize() !== origFormsIC) {
            $('#myForm').addClass('contentChanged');
            $('#updateForm').addClass('contentChanged');
            // $('#update').removeAttr('disabled');
            $('#create_cancel').removeAttr('disabled');
        }
    });
    var $formsL = $('#main_menu_language_edit'),
        origFormsL = $formsL.serialize();
    $('form :input').on('change input', function () {

        if ($formsL.serialize() !== origFormsL) {
            $('#main_menu_language_edit').addClass('contentChanged');
            $('#save_language_button').removeAttr('disabled');
            $('#cancel_editing_button').removeAttr('disabled');
        }
    });
    var $formsT = $('#text_create_form'),
        origFormsT = $formsT.serialize();
    $('form :input').on('change input', function () {

        if ($formsT.serialize() !== origFormsT) {
            $('#text_create_form').addClass('contentChanged');
        }
    });
    var $forms = $('#text_update_form'),
        origForms = $forms.serialize();
    $('form :input').on('change input', function () {
        if ($forms.serialize() !== origForms) {
            $('#text_update_form').addClass('contentChanged');
            $('#text_discard').removeAttr('disabled');
            if ($('#name_en_field_of_text_update_form').val() !== '') {
                $('#text_update_button').removeAttr('disabled');
            }
        }
    });

    var $formSeasonEdit = $('#season_edit_form'),
        origFormSeasonEdit = $formSeasonEdit.serialize();
    $('form :input').on('change input', function () {
        if ($formSeasonEdit.serialize() !== origFormSeasonEdit) {
            $('#season_edit_form').addClass('contentChanged');
            $('#season_discard').removeAttr('disabled');
            if ($('#name_en_field_of_season_edit_form').val() !== '') {
                $('#season_update_button').removeAttr('disabled');
            }
        }
    });

    var $formSeasonCreate = $('#season_create_form'),
        origFormSeasonCreate = $formSeasonCreate.serialize();
    $('form :input').on('change input', function () {
        if ($formSeasonCreate.serialize() !== origFormSeasonCreate) {
            $formSeasonCreate.addClass('contentChanged');
            $('#name_en_field_of_season_create_form').on('keyup', function () {
                if ($(this).val() !== '') {
                    $('#season_save_button').removeAttr('disabled');
                } else {
                    $('#season_save_button').attr('disabled', true);
                }
            })
        }
    });

    var $formEpisodeCreate = $('#episode_create_form'),
        origFormEpisodeCreate = $formEpisodeCreate.serialize();
    $('form :input').on('change input', function () {
        if ($formEpisodeCreate.serialize() !== origFormEpisodeCreate) {
            $formEpisodeCreate.addClass('contentChanged');
            $('#name_en_field_of_episode_create_form').on('keyup', function () {
                if ($(this).val() !== '') {
                    $('#episode_save_button').removeAttr('disabled');
                } else {
                    $('#episode_save_button').attr('disabled', true);
                }
            })
        }
    });
    var $formEpisodeEdit = $('#episode_edit_form'),
        origFormEpisodeEdit = $formEpisodeEdit.serialize();
    $('form :input').on('change input', function () {
        if ($formEpisodeEdit.serialize() !== origFormEpisodeEdit) {
            $formEpisodeEdit.addClass('contentChanged');
            $("#episode_discard").removeAttr('disabled');
            if ($('#name_en_field_of_episode_edit_form').val() !== '') {
                $('#episode_update_button').removeAttr('disabled');
            }

        }
    });

    var $formChapterCreate = $('#chapter_create_form'),
        origFormChapterCreate = $formChapterCreate.serialize();
    $('form :input').on('change input', function () {
        if ($formChapterCreate.serialize() !== origFormChapterCreate) {
            $formChapterCreate.addClass('contentChanged');
            $('#name_en_field_of_chapter_create_form').on('keyup', function () {
                if ($(this).val() !== '') {
                    $('#chapter_save_button').removeAttr('disabled');
                } else {
                    $('#chapter_save_button').attr('disabled', true);
                }
            })
        }
    });
    var $formChapterEdit = $('#chapter_edit_form'),
        origFormChapterEdit = $formChapterEdit.serialize();
    $('form :input').on('change input', function () {
        if ($formChapterEdit.serialize() !== origFormChapterEdit) {
            $formChapterEdit.addClass('contentChanged');
            $("#chapter_discard").removeAttr('disabled');
            if ($('#name_en_field_of_chapter_edit_form').val() !== '') {
                $('#chapter_update_button').removeAttr('disabled');
            }

        }
    });

    var $formSceneCreate = $('#scene_create_form'),
        origFormSceneCreate = $formSceneCreate.serialize();
    $('form :input').on('change input', function () {
        if ($formSceneCreate.serialize() !== origFormSceneCreate) {
            $formSceneCreate.addClass('contentChanged');
            $('#name_en_field_of_scene_create_form').on('keyup', function () {
                if ($(this).val() !== '') {
                    $('#scene_save_button').removeAttr('disabled');
                } else {
                    $('#scene_save_button').attr('disabled', true);
                }
            })
        }
    });
    var $formSceneEdit = $('#scene_edit_form'),
        origFormSceneEdit = $formSceneEdit.serialize();
    $('form :input').on('change input', function () {
        if ($formSceneEdit.serialize() !== origFormSceneEdit) {
            $formSceneEdit.addClass('contentChanged');
            $("#scene_discard").removeAttr('disabled');
            if ($('#name_en_field_of_scene_edit_form').val() !== '') {
                $('#scene_update_button').removeAttr('disabled');
            }

        }
    });
    //
    // var $formIntroSceneEdit = $('#intro_scene_edit_form'),
    //     origFormIntroSceneEdit = $formIntroSceneEdit.serialize();
    // $('form :input').on('change input', function () {
    //     if ($formIntroSceneEdit.serialize() !== origFormIntroSceneEdit) {
    //         $formIntroSceneEdit.addClass('contentChanged');
    //         $("#scene_discard").removeAttr('disabled');
    //         if ($('#name_en_field_of_scene_edit_form').val() !== '') {
    //             $('#scene_update_button').removeAttr('disabled');
    //         }
    //
    //     }
    // });
    // }
    // });
    var $formCharacterEdit = $('#character_edit_form'),
        origFormCharacterEdit = $formCharacterEdit.serialize();
    $('form :input').on('change input', function () {
        if ($formCharacterEdit.serialize() !== origFormCharacterEdit) {
            $formCharacterEdit.addClass('contentChanged');
            $("#scene_discard").removeAttr('disabled');
            if ($('#name_en_field_of_character_edit_form').val() !== '') {
                $('#character_update_button').removeAttr('disabled');
                $('#character_discard_button').removeAttr('disabled');
            } else {
                $('#character_update_button').attr('disabled', true);
            }

        }
    });
    var $formCharacterCreate = $('#character_create_form'),
        origFormCharacterCreate = $formCharacterCreate.serialize();
    $('form :input').on('change input', function () {
        if ($formCharacterCreate.serialize() !== origFormCharacterCreate) {
            $formCharacterCreate.addClass('contentChanged');
            if ($('#name_en_field_of_character_create_form').val() !== '') {
                $('#character_save_button').removeAttr('disabled');
            } else {
                $('#character_save_button').attr('disabled', true);
            }
        }
    });

    var $formDialogueCreate = $('#dialogue_create_form'),
        origFormDialogueCreate = $formDialogueCreate.serialize();
    $('form :input').on('change input', function () {
        if ($formDialogueCreate.serialize() !== origFormDialogueCreate) {
            $formDialogueCreate.addClass('contentChanged');
            if ($('#name_en_field_of_dialogue_create_form').val() !== '') {
                $('#dialogue_save_button').removeAttr('disabled');
            } else {
                $('#dialogue_save_button').attr('disabled', true);
            }
        }
    });
    var $formDialogueUpdate = $('#dialogue_update_form'),
        origFormDialogueUpdate = $formDialogueUpdate.serialize();
    $('form :input').on('change input', function () {
        if ($formDialogueUpdate.serialize() !== origFormDialogueUpdate) {
            $formDialogueUpdate.addClass('contentChanged');
            if ($('#name_en_field_of_dialogue_update_form').val() !== '') {
                $('#dialogue_save_button').removeAttr('disabled');
                $('#dialogue_discard_button').removeAttr('disabled');

            } else {
                $('#dialogue_save_button').attr('disabled', true);
            }
        }
    });
    //
    // var $formIntroSceneEdit = $('#intro_scene_edit_form'),
    //     origFormIntroSceneEdit = $formIntroSceneEdit.serialize();
    // $('form :input').on('change input', function () {
    //     if ($formIntroSceneEdit.serialize() !== origFormIntroSceneEdit) {
    //         $formIntroSceneEdit.addClass('contentChanged');
    //         $("#scene_discard").removeAttr('disabled');
    //         if ($('#name_en_field_of_scene_edit_form').val() !== '') {
    //             $('#scene_update_button').removeAttr('disabled');
    //         }
    //
    //     }
    // });


});

function formValues(form) {
    let inputFormsElem = $(form.find('input'));
    let textareaFormsElem = $(form.find('textarea'));
    let data = [];
    let i = null;
    inputFormsElem.each(function (index) {
        data[index] = {[$(this).attr('name')]: $(this).val()}
        i = index;
    })
    textareaFormsElem.each(function (index) {
        data[index + i + 1] = {[$(this).attr('name')]: $(this).val()}
    })
    return data;
}

function treeConstruct(tree) {
    let data = []
    for (let i = 0; i < tree.length; i++) {
        if (tree[i].id === "choice") {
            let classList = $(tree[i]).attr("class");
            let classArr = classList.split(/\s+/);
            data[i] = classArr[0]
        } else {
            data[i] = tree[i].id;
        }
    }
    return data;
}


$('#dropped').on(
    'dragover',
    function (e) {
        this.className = 'hover';
        e.preventDefault();
        e.stopPropagation();
    }
)
$('#dropped2').on(
    'dragover',
    function (e) {
        this.className = 'hover';
        e.preventDefault();
        e.stopPropagation();
    }
)
$('#dropped').on(
    'dragenter',
    function (e) {
        e.preventDefault();
        e.stopPropagation();
    }
)
$('#dropped2').on(
    'dragenter',
    function (e) {
        e.preventDefault();
        e.stopPropagation();
    }
)

$('#dropped').on("drop", function (e) {
    if (e.originalEvent.dataTransfer) {
        if (e.originalEvent.dataTransfer.files.length) {
            // this.className = 'hidden';
            e.preventDefault();
            e.stopPropagation();
            /*UPLOAD FILES HERE*/
            var file = e.originalEvent.dataTransfer.files[0];
            const fileType = file['type'];
            const validImageTypes = ['image/png'];
            if (validImageTypes.includes(fileType)) {
                var reader = new FileReader();
                reader.onload = function () {
                    $('#dropped').className = 'visible'
                    $(".previewImg").attr('src', URL.createObjectURL(file));
                    $('#dd_file_language_file').val(reader.result)
                }
                reader.readAsDataURL(file);

                $('#main_menu_language_edit').addClass('contentChanged');
                $('#save_language_button').removeAttr('disabled');
                $('#season_update_button').removeAttr('disabled');
                $('#season_discard').removeAttr('disabled');
                $('#chapter_update_button').removeAttr('disabled');
                $('#scene_update_button').removeAttr('disabled');
                $('#cancel_editing_button').removeAttr('disabled');
                let fileZone = $('#language_file')
                if (fileZone.find('a#delete_flag_of_language').length > 0) {
                    fileZone.find('a#delete_flag_of_language')[0].remove();
                }
                // $('#dropped').append('<a href="#" class="dis-img" id="delete_flag_of_language" onclick="delLanguageFlag()"><i class="close icon"></i></a>')
            } else
                alert("only png")

        }
    }
    // this.className = 'hidden';
});

$('#dropped2').on("drop", function (e) {
    if (e.originalEvent.dataTransfer) {
        if (e.originalEvent.dataTransfer.files.length) {
            // this.className = 'hidden';
            e.preventDefault();
            e.stopPropagation();
            /*UPLOAD FILES HERE*/
            var file = e.originalEvent.dataTransfer.files[0];
            const fileType = file['type'];
            const validImageTypes = ['image/png'];
            if (validImageTypes.includes(fileType)) {
                var reader = new FileReader();
                reader.onload = function () {
                    $('#dropped2').className = 'visible'
                    $(".previewImgAdditional").attr('src', URL.createObjectURL(file));
                    $('#dd_file_language_file_additional').val(reader.result)
                }
                reader.readAsDataURL(file);

                $('#main_menu_language_edit').addClass('contentChanged');
                $('#save_language_button').removeAttr('disabled');
                $('#season_update_button').removeAttr('disabled');
                $('#season_discard').removeAttr('disabled');
                $('#chapter_update_button').removeAttr('disabled');
                $('#scene_update_button').removeAttr('disabled');
                $('#cancel_editing_button').removeAttr('disabled');
                let fileZone = $('#language_file')
                if (fileZone.find('a#delete_flag_of_language').length > 0) {
                    fileZone.find('a#delete_flag_of_language')[0].remove();
                }
                // $('#dropped').append('<a href="#" class="dis-img" id="delete_flag_of_language" onclick="delLanguageFlag()"><i class="close icon"></i></a>')
            } else
                alert("only png")

        }
    }
    // this.className = 'hidden';
});

function previewFile(input,selector = 'dd_file_language_file',imgSrc = "previewImg") {
    const [file] = input.files
    const fileType = file['type'];
    const validImageTypes = ['image/png'];
    if (validImageTypes.includes(fileType)) {
        var reader = new FileReader();
        reader.onload = function () {
            $('#dropped').className = 'visible'
            $("."+ imgSrc).attr('src', URL.createObjectURL(file));
            $("#background_file_name").val(file['name']);
            $('#'+ selector).val(reader.result)
        }
        reader.readAsDataURL(file);

        if (file) {
            $("."+imgSrc).attr('src', URL.createObjectURL(file));
            $('#main_menu_language_edit').addClass('contentChanged');
            $('#save_language_button').removeAttr('disabled');
            $('#season_update_button').removeAttr('disabled');
            $('#season_discard').removeAttr('disabled');
            $('#chapter_update_button').removeAttr('disabled');
            $('#scene_update_button').removeAttr('disabled');
            $('#cancel_editing_button').removeAttr('disabled');
            let fileZone = $('#language_file')
            if (fileZone.find('a#delete_flag_of_language').length > 0) {
                fileZone.find('a#delete_flag_of_language')[0].remove();
            }
            // $('#dropped').append('<a href="#" class="dis-img" id="delete_flag_of_language" onclick="delLanguageFlag()"><i class="close icon"></i></a>')
        }
    } else
        alert("only png")
}



