CKEDITOR.ClassicEditor.create(document.getElementById("editor"), {
    toolbar: {
        items: [
            'bold', 'italic', 'underline', '|', 'removeFormat',
            'undo', 'redo',
        ],
        shouldNotGroupWhenFull: true
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
});