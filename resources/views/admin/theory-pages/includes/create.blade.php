<div class="edit">
    <h1>Create</h1>
    <button class="ui icon basic button exit"><i class="close icon"></i></button>
    <div class="search-area">
        <h3>NEW PAGE</h3>
    </div>
    <div class="edit-area words gramm-cards">
        <form action="{{route('theory-pages-store',$theoryId)}}" method="POST" enctype="multipart/form-data"
              class="ui form cards-form" id="en_form">
            {{csrf_field()}}
            <input name="cards-input" value="" hidden/>
            <input name="language" value="en" hidden/>
            <input type="text" hidden name="is_deleted" id="is_deleted_language_file"/>
            <input type="text" hidden name="dd_file" id="dd_file_language_file"/>

            <div class="cards-area">
                <!--  HERE GOES LIST WITH CARDS  -->
            </div>
            <div class="ui pointing dropdown add ui basic button cards-drop">
                <i class="plus icon"></i>add page
                <div class="menu">
                    <div class="item title-selector">
                        Title
                    </div>
                    <div class="item description-selector">
                        Description
                    </div>
                    <div class="item example-selector">
                        Example
                    </div>
                    <div class="item table-selector">
                        Table
                    </div>
                    <div class="item image-selector">
                        Image
                    </div>
                </div>
            </div>

            <div class="buttons action-buttons">
                <button type="button" class="positive ui button save" disabled >SAVE</button>
                <button type="button" class="negative ui button " disabled >CANCEL</button>
            </div>
        </form>
        <div class="preview">
            <div class="preview-wrap">
                <div class="over">
                </div>
                <div class="over"></div>
                <a href="#" class="local-btn">
                    <i class="language icon"></i>
                </a>
                <div class="preview-info"></div>
                <img class="iphone" src="{{asset('iphone_bg.svg')}}">
            </div>
        </div>
        <div class="local-add">
        </div>
    </div>
</div>
<script src="{{asset('js/main.js')}}"></script>
<script src="{{asset('js/title_editor.js')}}"></script>
<script src="{{asset('js/description_editor.js')}}"></script>
<script src="{{asset('js/example_editor.js')}}"></script>
<script src="{{asset('js/table_editor.js')}}"></script>

<script>

    $(document).ready(function () {
        // delete card
        let cards = "div[class$='-card']";
        $(".cards-area").on('DOMSubtreeModified', cards, function () {
            $('.del-card').on('click', function () {
                $(this).parent().remove();
                let id = parseInt($(this).parent().attr('id')) + 10;
                switch (true) {
                    case $(this).hasClass('title'):
                        $('#' + id + '.title-view').remove();
                        break;
                    case $(this).hasClass('description'):
                        $('#' + id + '.description-view').remove();
                        break;
                    case $(this).hasClass('example'):
                        $('#' + id + '.example-view').remove();
                        break;
                    case $(this).hasClass('table'):
                        $('#' + id + '.table-view').remove();
                        break;
                    case $(this).hasClass('image'):
                        $('.image-view').remove();
                        break;
                }
            });
        });
    })
    $('.local-btn').on('click', function () {
        $('.local-add').animate({
            'opacity': '1',
            'left': "50%",
        }, 750);
        $('.cards-area:first').toggleClass('cards-area cards-area-lock').after(function () {
            $('.cards-area > *').remove();
            $('.cards-area-lock .ck.ck-content').prop("contenteditable", false);
            $('.gramm-cards > .form .ui.dropdown, .gramm-cards > .form .action-buttons').hide();
        });
        $('.cards-area-local').toggleClass('cards-area-local cards-area');
        // $('#edit .exit').animate({
        //     'opacity': '0'
        // })
        // $('.oxford').animate({
        //     'opacity': '1'
        // }, 750)
    });

    $(".cards-area").on('DOMSubtreeModified', '.title-card', function () {
        let titleId = parseInt($(this).attr('id')) + 10;
        $('.title-view#' + titleId).html($(this).find('.ck.ck-content > *').parents('.title-card#' + titleId - 10).html())
    });
    $(".cards-area").on('DOMSubtreeModified', '.description-card', function () {
        let descriptionId = parseInt($(this).attr('id')) + 10;
        $('.description-view#' + descriptionId).html($(this).find('.ck.ck-content > *').parents('.description-card#' + descriptionId - 10).html())
    });
    $(".cards-area").on('DOMSubtreeModified', '.example-card', function () {
        let exampleId = parseInt($(this).attr('id')) + 10;
        $('.example-view#' + exampleId).html($(this).find('.ck.ck-content > *').parents('.example-card#' + exampleId - 10).html())
    });
    $(".cards-area").on('DOMSubtreeModified', '.table-card', function () {
        let tableId = parseInt($(this).attr('id')) + 10;
        $('.table-view#' + tableId).html($(this).find('.ck.ck-content > *').parents('.table-card#' + tableId - 10).html())
    });
    $(".cards-area").on('DOMSubtreeModified', '.image-card', function () {
        let imgId = parseInt($(this).attr('id')) + 10;
        $('.image-view#' + imgId).html($(this).find('.previewImg').parents('.image-card#' + imgId - 10).html());
    });
    setTimeout(function () {
        $('.preview-info').on('DOMSubtreeModified', function () {
            $('.form').addClass('contentChanged');
            $('.save.button').removeAttr('disabled');
            $('.negative.button').removeAttr('disabled');
        })
    }, 2000)

    function delLanguageFlag() {
        $('.previewImg').attr('src', '')
        $('#is_deleted_language_file').val(1);
        $('#dd_file_language_file').val(null)
    }

    var imageTemplate = `
      <div class="image-card">
            <label id="dropped" class="add-bg ">
                <input type="file" name="image" accept="image/png"
                       onchange="previewFile(this);$('#delete_flag_of_language').remove()" placeholder="IMG">
                <i class="upload icon">
                    <img class="previewImg" data-value="" src="">
                </i>
            </label>
            <a class='del-card image'><i class='trash alternate icon red'></i></a>
        </div>
    `;
    // check image just for one
    $('.cards-drop').on('click', function () {
        if ($('.cards-area .image-card').length < 1) {
            $('.image-selector').show()
        } else {
            $('.image-selector').hide()
        }
    })
    // appendImage
    $('.image-selector').on('click', function () {
        $(".cards-area").append(imageTemplate).after(function () {
            $('.image-card').attr('id', function () {
                return $(this).index() + 1
            });
        });
        $('.preview-info').append($('<div/>').addClass("image-view")).after(function () {
            $('.image-view').attr('id', function () {
                return $(this).index() + 11
            });
        });

    })

    // check overflow
    $(".preview-info").on('DOMSubtreeModified', function () {
        if ($('.preview-info > *').length) {
            $('.local-btn, .button.save').show();
        } else {
            $('.local-btn, .button.save').hide();
        }

        function isOverflown(element) {
            return element.scrollHeight > element.clientHeight;
        }

        let parent = document.querySelectorAll(".preview-info");
        parent.forEach(children => {
            if (isOverflown(children)) {
                $('.over').css('background', 'var(--coral)').text("content doesn't fit on page");
                $('.local-btn, .button.save').hide();
            } else {
                $('.over').css('background', 'var(--green)').text("correct");
                $('.local-btn, .button.save').show()
            }
        })
    })
    $('.negative.button').on('click', function () {
        $('.discard-modal').modal({
            onApprove: function () {
                location.reload();
            }
        }).modal('show');
    })

    $('.positive.save').one('click', function () {
        let elements = $(".ck-content > p, .ck-content table, .image-card .previewImg");
        let toInput = $('input[name="cards-input"]');
        let dd_file = $('#dd_file_language_file').val()
        let is_deleted = $('#is_deleted_language_file').val()
        let arr = [];
        for (let i = 0; i < elements.length; i++) {
            var content = elements[i].outerHTML;
            content = content.replace(/&nbsp;/g, '')
                             .replace(/�/g, '')
                             .replace(/’/g, "'")
                             .replace(/‘/g, "'");
            arr.push({
                "index": $(elements[i]).parents("div[class$='-card']").index(),
                "type": $(elements[i]).parents("div[class$='-card']").attr('class'),
                "content": content,
            });
        }
        toInput.val(JSON.stringify(arr))
        let data = {
            "cards-input": JSON.stringify(arr),
            "language": "en",
            "is_deleted": is_deleted,
            "dd_file":dd_file,
        };
        $.ajax({
            type: "post",
            url: "{{route('theory-pages-store',$theoryId)}}",
            data: JSON.stringify(data),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function () {
                location.reload();
            }
        })
    })

    $('.local-btn').on('click', function () {
        let elements = $(".ck-content > p, .ck-content table, .image-card .previewImg");
        let toInput = $('input[name="cards-input"]');
        let dd_file = $('#dd_file_language_file').val()
        let is_deleted = $('#is_deleted_language_file').val()
        let arr = [];
        for (let i = 0; i < elements.length; i++) {
            arr.push({
                "index":
                    $(elements[i]).parents("div[class$='-card']").index(),

                "type":
                    $(elements[i]).parents("div[class$='-card']").attr('class'),

                "content":
                elements[i].outerHTML,

            });
        }
        toInput.val(JSON.stringify(arr))

        let data = {
            "cards-input": JSON.stringify(arr),
            "language": "en",
            "is_deleted": is_deleted,
            "dd_file":dd_file,
        };
        $.ajax({
            type: "post",
            url: "{{route('theory-pages-store',$theoryId)}}",
            data: JSON.stringify(data),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                let idsData = {
                    "ids": JSON.stringify(res["ids"]),
                };
                $.ajax({
                    type: "POST",
                    url: "/admin/theory/pages/create/localisation/{{$theoryId}}/" + res["stack_id"],
                    data: JSON.stringify(idsData),
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        let form = $('.local-add');
                        form.empty();
                        form.append(res)
                    }
                })
            }
        })
    })
</script>