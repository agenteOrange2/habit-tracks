import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'
import Placeholder from '@tiptap/extension-placeholder'
import TaskList from '@tiptap/extension-task-list'
import TaskItem from '@tiptap/extension-task-item'
import Highlight from '@tiptap/extension-highlight'
import Link from '@tiptap/extension-link'

// Create TipTap editor instance
export function createTiptapEditor(element, initialContent, onUpdate, onSelectionUpdate) {
    const editor = new Editor({
        element: element,
        extensions: [
            StarterKit.configure({
                heading: { levels: [1, 2, 3] },
            }),
            Placeholder.configure({
                placeholder: 'Escribe aquí...',
            }),
            TaskList,
            TaskItem.configure({ nested: true }),
            Highlight,
            Link.configure({
                openOnClick: true,
                HTMLAttributes: {
                    target: '_blank',
                    rel: 'noopener noreferrer',
                },
            }),
        ],
        content: initialContent || '',
        editorProps: {
            attributes: {
                class: 'focus:outline-none min-h-[500px]',
            },
        },
        onUpdate: ({ editor }) => {
            if (onUpdate) {
                onUpdate(editor.getHTML())
            }
            if (onSelectionUpdate) {
                onSelectionUpdate()
            }
        },
        onSelectionUpdate: () => {
            if (onSelectionUpdate) {
                onSelectionUpdate()
            }
        },
    })

    return editor
}

// Make it available globally
window.createTiptapEditor = createTiptapEditor
