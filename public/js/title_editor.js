function titleCard() {
    window.editors = {};
    document.querySelectorAll('.title-hidden').forEach((node, index) => {
        CKEDITOR.ClassicEditor
            .create(node, {
                toolbar: {
                    items: [
                        'undo', 'redo',
                    ],
                    shouldNotGroupWhenFull: true,
                },

                placeholder: '',

                htmlSupport: {
                    allow: [
                        {
                            name: /.*/,
                            attributes: true,
                            classes: true,
                            styles: true
                        }
                    ]
                },

                htmlEmbed: {
                    showPreviews: true
                },

                removePlugins: [
                    'CKBox',
                    'CKFinder',
                    'EasyImage',
                    'RealTimeCollaborativeComments',
                    'RealTimeCollaborativeTrackChanges',
                    'RealTimeCollaborativeRevisionHistory',
                    'PresenceList',
                    'Comments',
                    'TrackChanges',
                    'TrackChangesData',
                    'RevisionHistory',
                    'Pagination',
                    'WProofreader',
                    'MathType'
                ]
            }).then(editor => {
                // Get the paste event on the editor
                // Get the paste event on the editor
                editor.editing.view.document.on('paste', async (evt, data) => {
                    // Prevent the default paste behavior
                    evt.stop();

                    try {
                        // Get the plain text from the clipboard
                        const clipboardData = await navigator.clipboard.readText();

                        // Strip HTML tags from the plain text
                        const strippedText = clipboardData.replace(/<[^>]+>/g, '');

                        // Paste the stripped plain text into the editor
                        editor.model.change(writer => {
                            writer.insertText(strippedText, editor.model.document.selection.getFirstPosition());
                        });
                    } catch (error) {
                        console.error('Error reading clipboard data:', error);
                    }
                });
            }).then(newEditor => {
                window.editors[index] = newEditor
            });
    });
}

$(document).ready(function () {
    titleCard();
    $('.title-hidden').wrap('<div class="title-card"></div>').after(function () {
        $('.title-card').attr('id', function () {
            return $(this).index() + 1;
        });
    })
    $('.title-card').each(function () {
        if ($(this).find('.del-card').length === 0) {
            $(this).append("<a class='del-card title'><i class='trash alternate icon red'></i></a>")
        }
        setTimeout(function () {
            $(".title-hidden").remove();
            $('.preview-info').append($('<div/>').addClass("title-view")).after(function () {
                $('.title-view').attr('id', function () {
                    return $(this).index() + 11;
                });
            })
        }, 100);
    })
})

$('.title-selector').on('click', function () {
    $('.cards-area').append('<div class="title-hidden"></div>')
    titleCard();

    $(".title-hidden").wrap('<div class="title-card"></div>').after(function () {
        $('.title-card').attr('id', function () {
            return $(this).index() + 1;
        });
    })
    $('.title-card').each(function () {
        if ($(this).find('.del-card').length === 0) {
            $(this).append("<a class='del-card title'><i class='trash alternate icon red'></i></a>")
        }
    })
    setTimeout(function () {
        $(".title-hidden").remove();
        $('.preview-info').append($('<div/>').addClass("title-view")).after(function () {
            $('.title-view').attr('id', function () {
                return $(this).index() + 11;
            });
        })
    }, 100);
});
