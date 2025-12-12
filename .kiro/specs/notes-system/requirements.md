# Requirements Document - Notes System

## Introduction

Sistema de notas personales con editor de bloques estilo Notion usando Tiptap. Permite a los usuarios crear, organizar y buscar notas con formato rico, incluyendo headings, listas, checkboxes y más. El diseño sigue la estética minimalista de Notion.

## Glossary

- **Note**: Documento de texto con formato rico creado por el usuario
- **Tiptap**: Editor de texto rico headless basado en ProseMirror
- **Block**: Elemento individual de contenido (párrafo, heading, lista, etc.)
- **Folder**: Agrupación lógica de notas para organización
- **Tag**: Etiqueta para categorizar y filtrar notas
- **Pinned**: Nota marcada como favorita para acceso rápido

## Requirements

### Requirement 1

**User Story:** As a user, I want to create notes with rich text formatting, so that I can capture ideas with proper structure and emphasis.

#### Acceptance Criteria

1. WHEN a user creates a new note THEN the system SHALL display a Tiptap editor with Notion-style formatting
2. WHEN a user types "/" THEN the system SHALL show a command menu with block options (heading, list, checkbox, quote)
3. WHEN a user selects text THEN the system SHALL display a floating toolbar with formatting options (bold, italic, underline, link)
4. WHEN a user saves a note THEN the system SHALL persist the content as JSON to the database
5. WHEN a user adds a title THEN the system SHALL use a large, bold input field similar to Notion

### Requirement 2

**User Story:** As a user, I want to organize my notes in folders, so that I can keep related content together.

#### Acceptance Criteria

1. WHEN a user creates a folder THEN the system SHALL add it to the sidebar navigation
2. WHEN a user moves a note to a folder THEN the system SHALL update the note's folder association
3. WHEN a user views a folder THEN the system SHALL display only notes belonging to that folder
4. WHEN a user deletes a folder THEN the system SHALL move contained notes to "Sin carpeta"
5. WHEN a user renames a folder THEN the system SHALL update the folder name immediately

### Requirement 3

**User Story:** As a user, I want to tag my notes, so that I can categorize and filter them across folders.

#### Acceptance Criteria

1. WHEN a user adds a tag to a note THEN the system SHALL associate the tag with that note
2. WHEN a user clicks a tag THEN the system SHALL filter notes to show only those with that tag
3. WHEN a user creates a new tag THEN the system SHALL add it to the available tags list
4. WHEN a user removes a tag from a note THEN the system SHALL update the association immediately
5. WHEN displaying tags THEN the system SHALL show them as colored pills below the note title

### Requirement 4

**User Story:** As a user, I want to search my notes, so that I can quickly find specific content.

#### Acceptance Criteria

1. WHEN a user types in the search box THEN the system SHALL filter notes by title and content
2. WHEN search results are displayed THEN the system SHALL highlight matching text
3. WHEN a user clears the search THEN the system SHALL restore the full note list
4. WHEN no results are found THEN the system SHALL display a helpful empty state message

### Requirement 5

**User Story:** As a user, I want to pin important notes, so that I can access them quickly.

#### Acceptance Criteria

1. WHEN a user pins a note THEN the system SHALL move it to the top of the list
2. WHEN a user unpins a note THEN the system SHALL return it to its chronological position
3. WHEN displaying notes THEN the system SHALL show pinned notes first with a visual indicator
4. WHEN a user has multiple pinned notes THEN the system SHALL order them by pin date

### Requirement 6

**User Story:** As a user, I want to delete and restore notes, so that I can manage my content safely.

#### Acceptance Criteria

1. WHEN a user deletes a note THEN the system SHALL soft-delete it and move to trash
2. WHEN a user views trash THEN the system SHALL display all soft-deleted notes
3. WHEN a user restores a note THEN the system SHALL return it to its original folder
4. WHEN a user permanently deletes a note THEN the system SHALL remove it from the database
5. WHEN a note has been in trash for 30 days THEN the system SHALL auto-delete it permanently

### Requirement 7

**User Story:** As a user, I want the notes interface to feel like Notion, so that I have a familiar and pleasant experience.

#### Acceptance Criteria

1. WHEN viewing the notes list THEN the system SHALL display a clean sidebar with folders and notes
2. WHEN editing a note THEN the system SHALL provide a distraction-free full-width editor
3. WHEN hovering over notes THEN the system SHALL show subtle action buttons (pin, delete, move)
4. WHEN the editor loads THEN the system SHALL focus the title field automatically
5. WHEN typing THEN the system SHALL auto-save content every few seconds

### Requirement 8

**User Story:** As a user, I want to see my recent notes on the dashboard, so that I can quickly continue where I left off.

#### Acceptance Criteria

1. WHEN viewing the dashboard THEN the system SHALL display the 3 most recent notes
2. WHEN clicking a recent note THEN the system SHALL navigate to the note editor
3. WHEN no notes exist THEN the system SHALL show a call-to-action to create the first note
