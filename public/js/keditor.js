// This sample still does not showcase all CKEditor 5 features (!)
// Visit https://ckeditor.com/docs/ckeditor5/latest/features/index.html to browse all the features.
CKEDITOR.ClassicEditor.create(document.getElementById("editor"), {
    toolbar: {
        items: [
            'bold', 'italic', 'fontColor', 'removeFormat', '|',
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
    editor.editing.view.document.on('paste', (evt, data) => {
        // Get the plain text from the clipboard
        const text = (evt.clipboardData || window.clipboardData).getData('text/plain');
        // Paste the plain text into the editor
        editor.model.change(writer => {
            writer.insertText(text, editor.model.document.selection.getFirstPosition());
        });
        // Prevent the default paste behavior
        evt.stop();
    });
});