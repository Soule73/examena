import { useEffect, useRef, useState, forwardRef, useImperativeHandle } from 'react';
import EasyMDE from 'easymde';
import 'easymde/dist/easymde.min.css';

declare global {
    interface Window {
        EasyMDE: typeof EasyMDE;
    }
}

interface MarkdownEditorProps {
    value?: string;
    onChange?: (value: string) => void;
    placeholder?: string;
    required?: boolean;
    id?: string;
    className?: string;
    rows?: number;
    disabled?: boolean;
    error?: string;
    label?: string;
    helpText?: string;
}

export interface MarkdownEditorRef {
    focus: () => void;
    getValue: () => string;
    setValue: (value: string) => void;
}

export const MarkdownEditor = forwardRef<MarkdownEditorRef, MarkdownEditorProps>(({
    value = '',
    onChange,
    placeholder = 'Saisissez votre réponse ici...',
    required = false,
    id,
    className = '',
    rows = 6,
    disabled = false,
    error,
    label,
    helpText
}, ref) => {
    const textareaRef = useRef<HTMLTextAreaElement>(null);
    const editorRef = useRef<EasyMDE | null>(null);
    const [isReady, setIsReady] = useState(false);
    const componentId = id || `markdown-editor-${Math.random().toString(36).substr(2, 9)}`;

    useImperativeHandle(ref, () => ({
        focus: () => {
            if (editorRef.current) {
                const codemirror = (editorRef.current as any).codemirror;
                if (codemirror) {
                    codemirror.focus();
                }
            }
        },
        getValue: () => {
            return editorRef.current ? editorRef.current.value() : value;
        },
        setValue: (newValue: string) => {
            if (editorRef.current) {
                editorRef.current.value(newValue);
            }
            if (onChange) {
                onChange(newValue);
            }
        }
    }));

    const debounce = (func: Function, delay: number) => {
        let timeoutId: NodeJS.Timeout;
        return (...args: any[]) => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(null, args), delay);
        };
    };

    const debouncedOnChange = debounce((newValue: string) => {
        if (onChange) {
            onChange(newValue);
        }
    }, 500);

    useEffect(() => {
        if (!textareaRef.current) return;

        const editor = new EasyMDE({
            element: textareaRef.current,
            placeholder: placeholder,
            spellChecker: false,
            autofocus: false,
            status: false,
            initialValue: value || '',
            toolbar: disabled ? false : [
                "bold", "italic", "heading", "|",
                "quote", "unordered-list", "ordered-list", "|",
                "link", "table", "|",
                "preview", "side-by-side", "|",
                "guide"
            ],
            shortcuts: {
                "toggleBold": "Ctrl-B",
                "toggleItalic": "Ctrl-I",
                "togglePreview": "Ctrl-P"
            },
        } as any);

        editorRef.current = editor;
        setIsReady(true);

        setTimeout(() => {
            const container = textareaRef.current?.parentElement;
            const previewElements = container?.querySelectorAll('.editor-preview, .editor-preview-side');
            previewElements?.forEach(element => {
                element.classList.add('prose', 'prose-sm', 'max-w-none', 'p-4');
            });
        }, 100);

        editor.codemirror.on("change", () => {
            const newValue = editor.value();
            debouncedOnChange(newValue);
        });

        return () => {
            if (editorRef.current) {
                editorRef.current.toTextArea();
                editorRef.current = null;
            }
        };
    }, [placeholder, disabled]);

    useEffect(() => {
        if (editorRef.current && isReady) {
            const currentValue = editorRef.current.value();
            if (currentValue !== value) {
                editorRef.current.value(value || '');
            }
        }
    }, [value, isReady]);

    useEffect(() => {
        if (editorRef.current && isReady) {
            const codemirror = (editorRef.current as any).codemirror;
            if (codemirror) {
                codemirror.setOption('readOnly', disabled);
            }
        }
    }, [disabled, isReady]);

    return (
        <div className={`markdown-editor-field ${className}`}>
            {label && (
                <label
                    htmlFor={componentId}
                    className="block text-sm font-medium text-gray-700 mb-2"
                >
                    {label}
                    {required && <span className="text-red-500 ml-1">*</span>}
                </label>
            )}

            <div className={`markdown-editor-container relative ${error ? 'ring-2 ring-red-500 rounded-lg' : ''}`}>
                <textarea
                    ref={textareaRef}
                    id={componentId}
                    placeholder={placeholder}
                    rows={rows}
                    className="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"
                    style={{ display: 'none' }}
                />
            </div>

            {error && (
                <p className="mt-2 text-sm text-red-600">
                    {error}
                </p>
            )}

            {helpText && !error && (
                <p className="mt-2 text-sm text-gray-500">
                    {helpText}
                </p>
            )}
        </div>
    );
});

MarkdownEditor.displayName = 'MarkdownEditor';

export default MarkdownEditor;