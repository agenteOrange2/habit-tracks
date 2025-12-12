# Implementation Plan - Notes System

- [x] 1. Create database migrations and models




  - [x] 1.1 Create notes table migration

    - Fields: id, user_id, folder_id, title, content (JSON), icon, is_pinned, pinned_at, soft deletes, timestamps
    - _Requirements: 1.4, 5.1, 6.1_

  - [x] 1.2 Create note_folders table migration

    - Fields: id, user_id, name, icon, sort_order, timestamps
    - _Requirements: 2.1_
  - [x] 1.3 Create note_tags table migration

    - Fields: id, user_id, name, color, timestamps
    - _Requirements: 3.1_


  - [x] 1.4 Create note_tag pivot table migration

    - Fields: note_id, note_tag_id

    - _Requirements: 3.1_
  - [x] 1.5 Create Note model with relationships and scopes

    - Relationships: user, folder, tags
    - Scopes: pinned, active, trashed, search
    - _Requirements: All_
  - [x] 1.6 Create NoteFolder model with relationships

    - _Requirements: 2.1, 2.2_


  - [x] 1.7 Create NoteTag model with relationships

    - _Requirements: 3.1, 3.2_

- [ ]* 1.8 Write property tests for Note model
  - **Property 1: Note content persistence round-trip**
  - **Property 12: Soft delete moves to trash**
  - **Property 14: Restore clears deleted_at**
  - **Validates: Requirements 1.4, 6.1, 6.3**

- [x] 2. Install and configure Tiptap


  - [x] 2.1 Install Tiptap npm packages


    - @tiptap/core, @tiptap/starter-kit, @tiptap/extension-placeholder, @tiptap/extension-task-list, @tiptap/extension-task-item, @tiptap/extension-link, @tiptap/extension-highlight
    - _Requirements: 1.1, 1.2, 1.3_

  - [x] 2.2 Create tiptap-editor.js Alpine component

    - Initialize editor with extensions
    - Implement slash command menu
    - Implement floating toolbar
    - Wire up Livewire content sync
    - _Requirements: 1.1, 1.2, 1.3_

- [x] 3. Create NoteEditor component



  - [x] 3.1 Create NoteEditor Livewire component

    - Properties: note, title, content, tags, folderId
    - Methods: save, updateTitle, updateContent, addTag, removeTag, moveToFolder
    - Auto-save with debounce
    - _Requirements: 1.1, 1.4, 1.5, 7.2, 7.4, 7.5_
  - [x] 3.2 Create note-editor.blade.php view

    - Notion-style full-width layout
    - Large title input
    - Tiptap editor container
    - Tag pills display
    - Folder selector
    - _Requirements: 1.5, 3.5, 7.2, 7.4_

- [ ]* 3.3 Write property test for note content save
  - **Property 1: Note content persistence round-trip**
  - **Validates: Requirements 1.4**




- [x] 4. Create NotesList component
  - [x] 4.1 Create NotesList Livewire component

    - Properties: notes, search, selectedFolder, selectedTag
    - Methods: search, filterByFolder, filterByTag, pinNote, deleteNote, createNote
    - _Requirements: 4.1, 5.1, 6.1_
  - [x] 4.2 Create notes-list.blade.php view

    - Search input
    - Notes grid/list with hover actions
    - Empty state
    - _Requirements: 4.1, 4.4, 7.1, 7.3_

- [ ]* 4.3 Write property tests for search and filtering
  - **Property 9: Search returns matching notes**
  - **Property 4: Folder filtering correctness**
  - **Property 7: Tag filtering correctness**
  - **Validates: Requirements 4.1, 2.3, 3.2**

- [ ]* 4.4 Write property tests for pinning
  - **Property 10: Pinned notes ordering**

  - **Property 11: Multiple pinned notes order by pin date**

  - **Validates: Requirements 5.1, 5.3, 5.4**

- [x] 5. Create NoteSidebar component (integrated into NotesList)

  - [x] 5.1 Sidebar integrated into NotesList component
    - Properties: folders, pinnedNotes, activeFolder
    - Methods: createFolder, setFolder, setTag
    - _Requirements: 2.1, 2.4, 2.5_
  - [x] 5.2 Sidebar view integrated in notes-list.blade.php
    - Folder list with icons
    - Quick actions (new note, new folder)
    - Tags section
    - Trash link
    - _Requirements: 2.1, 7.1_

- [ ]* 5.3 Write property tests for folder operations
  - **Property 2: Folder creation adds to user's folders**
  - **Property 3: Note-folder association update**

  - **Property 5: Folder deletion orphans notes**
  - **Validates: Requirements 2.1, 2.2, 2.4**


- [x] 6. Create TrashNotes component
  - [x] 6.1 Create TrashNotes Livewire component
    - Properties: trashedNotes

    - Methods: restore, permanentDelete, emptyTrash
    - _Requirements: 6.2, 6.3, 6.4_
  - [x] 6.2 Create trash-notes.blade.php view
    - List of deleted notes

    - Restore and delete buttons

    - Empty trash action
    - _Requirements: 6.2_

- [ ]* 6.3 Write property tests for trash operations
  - **Property 13: Trash query returns only deleted**
  - **Property 15: Permanent delete removes record**
  - **Validates: Requirements 6.2, 6.4**

- [x] 7. Create tag management
  - [x] 7.1 Add tag methods to NoteEditor
    - Create tag inline
    - Add/remove tag from note
    - Tag color picker
    - _Requirements: 3.1, 3.3, 3.4_


- [ ]* 7.2 Write property tests for tag operations
  - **Property 6: Tag association creation**
  - **Property 8: Tag removal**

  - **Validates: Requirements 3.1, 3.4**

- [x] 8. Create RecentNotes dashboard widget
  - [x] 8.1 Create RecentNotes Livewire component
    - Show 3 most recent notes

    - Link to note editor

    - Empty state with CTA
    - _Requirements: 8.1, 8.2, 8.3_


- [ ]* 8.2 Write property test for recent notes limit
  - **Property 16: Dashboard recent notes limit**

  - **Validates: Requirements 8.1**


- [x] 9. Add routes and navigation
  - [x] 9.1 Add routes in admin.php
    - notes.index, notes.create, notes.edit, notes.trash
    - _Requirements: All_
  - [x] 9.2 Add notes link to sidebar navigation
    - _Requirements: 7.1_

- [x] 10. Create NoteSeeder for testing
  - Sample folders, tags, and notes
  - _Requirements: All_

- [ ] 11. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.
