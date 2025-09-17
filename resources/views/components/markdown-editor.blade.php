@props([
    'name' => '',
    'value' => '',
    'placeholder' => 'Saisissez votre texte ici...',
    'required' => false,
    'id' => null,
])

<div x-data="markdownEditor('{{ $name }}', '{{ $value }}', '{{ $id ?? uniqid() }}')" x-init="initEditor()">
    <textarea x-ref="editor" name="{{ $name }}" id="{{ $id ?? uniqid() }}" placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        class="block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"
        rows="5">{{ $value }}</textarea>
</div>

@once
    <script>
        function markdownEditor(name, initialValue, editorId) {
            return {
                editor: null,

                initEditor() {
                    this.$nextTick(() => {
                        if (window.EasyMDE && this.$refs.editor) {
                            this.editor = new EasyMDE({
                                element: this.$refs.editor,
                                placeholder: this.$refs.editor.placeholder,
                                spellChecker: false,
                                autofocus: false,
                                status: false,
                                toolbar: [
                                    "bold", "italic", "heading", "|",
                                    "quote", "unordered-list", "ordered-list", "|",
                                    "link", "image", "|",
                                    "preview", "side-by-side", "fullscreen", "|",
                                    "guide"
                                ],
                                shortcuts: {
                                    "toggleBold": "Ctrl-B",
                                    "toggleItalic": "Ctrl-I",
                                    "togglePreview": "Ctrl-P"
                                },
                                initialValue: initialValue,
                            });
                        }
                    });
                },

                destroy() {
                    if (this.editor) {
                        this.editor.toTextArea();
                        this.editor = null;
                    }
                }
            }
        }
    </script>
@endonce
