function descriptionCard() {
    window.editors = {};
    document.querySelectorAll('.description-hidden').forEach((node, index) => {
        CKEDITOR.ClassicEditor
            .create(node, {
                toolbar: {
                    items: [
                        'bold', 'underline', 'italic', 'fontColor', 'specialCharacters', 'removeFormat', '|',
                        'undo', 'redo',
                    ],
                    shouldNotGroupWhenFull: true
                },
                fontColor: {
                    colors: [
                        {
                            color: '#3b2a7a',
                            label: 'Fucsia'
                        },
                        {
                            color: '#4987f7',
                            label: 'Blue'
                        },
                        {
                            color: '#7C3131',
                            label: 'Red'
                        },
                        {
                            color: '#16841a',
                            label: 'Green'
                        }
                    ]
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
    descriptionCard();
    $(".description-hidden").wrap('<div class="description-card"></div>').after(function () {
        $('.description-card').attr('id', function () {
            return $(this).index() + 1;
        });
    })
    $('.description-card').each(function () {
        if ($(this).find('.del-card').length === 0) {
            $(this).append("<a class='del-card description'><i class='trash alternate icon red'></i></a>")
        }
        setTimeout(function () {
            $(".description-hidden").remove();
            $('.preview-info').append($('<div/>').addClass("description-view")).after(function () {
                $('.description-view').attr('id', function () {
                    return $(this).index() + 11;
                });
            })
        }, 100);
    })
})

$('.description-selector').on('click', function () {
    $('.cards-area').append('<div class="description-hidden"></div>')
    descriptionCard();

    $(".description-hidden").wrap('<div class="description-card"></div>').after(function () {
        $('.description-card').attr('id', function () {
            return $(this).index() + 1;
        });
    })
    $('.description-card').each(function () {
        if ($(this).find('.del-card').length === 0) {
            $(this).append("<a class='del-card description'><i class='trash alternate icon red'></i></a>")
        }
    })
    setTimeout(function () {
        $(".description-hidden").remove();
        $('.preview-info').append($('<div/>').addClass("description-view")).after(function () {
            $('.description-view').attr('id', function () {
                return $(this).index() + 11;
            });
        })
    }, 100);
});