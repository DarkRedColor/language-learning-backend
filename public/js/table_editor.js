function tableCard() {
    window.editors = {};
    document.querySelectorAll('.table-hidden').forEach((node, index) => {
        CKEDITOR.ClassicEditor
            .create(node, {
                toolbar: {
                    items: [
                        'bold', 'italic', 'strikethrough', 'fontColor', 'insertTable', 'specialCharacters', 'removeFormat', '|',
                        'undo', 'redo',
                    ],
                    shouldNotGroupWhenFull: true
                },
                fontColor: {
                    colors: [
                        {
                            color: '#4987f7',
                            label: 'Blue'
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
    tableCard();
    $(".table-hidden").wrap('<div class="table-card"></div>').after(function () {
        $('.table-card').attr('id', function () {
            return $(this).index() + 1;
        });
    })
    $('.table-card').each(function () {
        if ($(this).find('.del-card').length === 0) {
            $(this).append("<a class='del-card table'><i class='trash alternate icon red'></i></a>")
        }
        setTimeout(function () {
            $(".table-hidden").remove();
            $('.preview-info').append($('<div/>').addClass("table-view")).after(function () {
                $('.table-view').attr('id', function () {
                    return $(this).index() + 11;
                });
            })
        }, 100);
    })
})

$('.table-selector').on('click', function () {
    $('.cards-area').append('<div class="table-hidden"></div>')
    tableCard();

    $(".table-hidden").wrap('<div class="table-card"></div>').after(function () {
        $('.table-card').attr('id', function () {
            return $(this).index() + 1;
        });
    })
    $('.table-card').each(function () {
        if ($(this).find('.del-card').length === 0) {
            $(this).append("<a class='del-card table'><i class='trash alternate icon red'></i></a>")
        }
    })
    setTimeout(function () {
        $(".table-hidden").remove();
        $('.preview-info').append($('<div/>').addClass("table-view")).after(function () {
            $('.table-view').attr('id', function () {
                return $(this).index() + 11;
            });
        })
    }, 100);
});