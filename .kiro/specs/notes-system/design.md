# Design Document - Notes System

## Overview

Sistema de notas personales con editor Tiptap estilo Notion. Proporciona una experiencia de escritura limpia y organización flexible mediante carpetas y tags. El diseño sigue la estética minimalista de Notion con colores neutros y tipografía clara.

## Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                      Notes Module                            │
├─────────────────────────────────────────────────────────────┤
│  Livewire Components                                         │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐        │
│  │  NotesList   │ │  NoteEditor  │ │  NoteSidebar │        │
│  └──────────────┘ └──────────────┘ └──────────────┘        │
│  ┌──────────────┐ ┌──────────────┐                          │
│  │ RecentNotes  │ │  TrashNotes  │                          │
│  └──────────────┘ └──────────────┘                          │
├─────────────────────────────────────────────────────────────┤
│  Frontend (Alpine.js + Tiptap)                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  tiptap-editor.js - Editor configuration & extensions │  │
│  └──────────────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────────────┤
│  Models                                                      │
│  ┌────────┐ ┌────────────┐ ┌─────────┐ ┌──────────┐       │
│  │  Note  │ │ NoteFolder │ │ NoteTag │ │ NoteTagP │       │
│  └────────┘ └────────────┘ └─────────┘ └──────────┘       │
└─────────────────────────────────────────────────────────────┘
```

## Components and Interfaces

### Livewire Components

#### NotesList
- Displays all notes with search and filter
- Handles note selection and navigation
- Properties: `$notes`, `$search`, `$selectedFolder`, `$selectedTag`
- Methods: `search()`, `filterByFolder()`, `filterByTag()`, `pinNote()`, `deleteNote()`

#### NoteEditor
- Full-page editor with Tiptap integration
- Auto-save functionality
- Properties: `$note`, `$title`, `$content`, `$tags`, `$folderId`
- Methods: `save()`, `updateTitle()`, `updateContent()`, `addTag()`, `removeTag()`, `moveToFolder()`

#### NoteSidebar
- Folder navigation and management
- Quick access to pinned notes
- Properties: `$folders`, `$pinnedNotes`, `$activeFolder`
- Methods: `createFolder()`, `renameFolder()`, `deleteFolder()`

#### RecentNotes (Dashboard Widget)
- Shows 3 most recent notes
- Properties: `$recentNotes`

#### TrashNotes
- Displays soft-deleted notes
- Methods: `restore()`, `permanentDelete()`, `emptyTrash()`

### Tiptap Editor Configuration

```javascript
// Extensions to include:
- StarterKit (paragraphs, headings, lists, bold, italic)
- Placeholder
- TaskList & TaskItem (checkboxes)
- Link
- Highlight
- Typography
```

### Slash Command Menu
Triggered by "/" with options:
- Heading 1, 2, 3
- Bullet List
- Numbered List
- Task List (checkboxes)
- Quote
- Divider

## Data Models

### Note
```php
Schema::create('notes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('folder_id')->nullable()->constrained('note_folders')->nullOnDelete();
    $table->string('title')->default('Sin título');
    $table->json('content')->nullable(); // Tiptap JSON
    $table->string('icon')->default('📝');
    $table->boolean('is_pinned')->default(false);
    $table->timestamp('pinned_at')->nullable();
    $table->softDeletes();
    $table->timestamps();
});
```

### NoteFolder
```php
Schema::create('note_folders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->string('icon')->default('📁');
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

### NoteTag
```php
Schema::create('note_tags', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->string('color')->default('#E9E9E7'); // Notion gray
    $table->timestamps();
});
```

### NoteTagPivot
```php
Schema::create('note_tag', function (Blueprint $table) {
    $table->foreignId('note_id')->constrained()->cascadeOnDelete();
    $table->foreignId('note_tag_id')->constrained()->cascadeOnDelete();
    $table->primary(['note_id', 'note_tag_id']);
});
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Note content persistence round-trip
*For any* valid note content JSON, saving and then retrieving the note should return equivalent content
**Validates: Requirements 1.4**

### Property 2: Folder creation adds to user's folders
*For any* folder name, creating a folder should make it appear in the user's folders list
**Validates: Requirements 2.1**

### Property 3: Note-folder association update
*For any* note and folder belonging to the same user, moving the note to that folder should update the folder_id correctly
**Validates: Requirements 2.2**

### Property 4: Folder filtering correctness
*For any* folder, querying notes by that folder should return only notes with matching folder_id
**Validates: Requirements 2.3**

### Property 5: Folder deletion orphans notes
*For any* folder with notes, deleting the folder should set folder_id to null for all contained notes
**Validates: Requirements 2.4**

### Property 6: Tag association creation
*For any* note and tag belonging to the same user, adding the tag should create the pivot record
**Validates: Requirements 3.1**

### Property 7: Tag filtering correctness
*For any* tag, filtering notes by that tag should return only notes with that tag association
**Validates: Requirements 3.2**

### Property 8: Tag removal
*For any* note with tags, removing a tag should delete the pivot record
**Validates: Requirements 3.4**

### Property 9: Search returns matching notes
*For any* search term, the search should return notes where title OR content contains the term
**Validates: Requirements 4.1**

### Property 10: Pinned notes ordering
*For any* list of notes with some pinned, pinned notes should appear before unpinned notes
**Validates: Requirements 5.1, 5.3**

### Property 11: Multiple pinned notes order by pin date
*For any* set of pinned notes, they should be ordered by pinned_at descending
**Validates: Requirements 5.4**

### Property 12: Soft delete moves to trash
*For any* note, deleting should set deleted_at and exclude from active queries
**Validates: Requirements 6.1**

### Property 13: Trash query returns only deleted
*For any* user, the trash query should return only notes with non-null deleted_at
**Validates: Requirements 6.2**

### Property 14: Restore clears deleted_at
*For any* soft-deleted note, restoring should set deleted_at to null and preserve folder_id
**Validates: Requirements 6.3**

### Property 15: Permanent delete removes record
*For any* note, force deleting should remove it from the database entirely
**Validates: Requirements 6.4**

### Property 16: Dashboard recent notes limit
*For any* user with notes, the recent notes query should return at most 3 notes ordered by updated_at desc
**Validates: Requirements 8.1**

## Error Handling

- **Empty title**: Default to "Sin título"
- **Invalid JSON content**: Store as empty document `{}`
- **Folder not found**: Set folder_id to null
- **Tag not found**: Skip association silently
- **Unauthorized access**: Return 403 and redirect
- **Auto-save failure**: Show toast notification, retry after 5 seconds

## Testing Strategy

### Unit Tests
- Note model: title defaults, content casting, relationships
- Folder model: user relationship, notes relationship
- Tag model: color validation, user scope

### Property-Based Tests (using Pest + Faker)
- Test all 16 correctness properties with generated data
- Use `pest-plugin-faker` for random note content generation
- Minimum 100 iterations per property test

### Integration Tests
- Full CRUD flow for notes
- Folder management with note associations
- Tag management with filtering
- Search functionality
- Trash and restore flow
