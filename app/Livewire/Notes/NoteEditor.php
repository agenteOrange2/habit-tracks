<?php

namespace App\Livewire\Notes;

use Livewire\Component;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;

class NoteEditor extends Component
{
    public ?Note $note = null;
    public string $title = '';
    public string $plainContent = '';
    public string $icon = '📝';
    public ?int $folderId = null;
    public array $selectedTags = [];

    public function mount(?Note $note = null): void
    {
        if ($note && $note->exists) {
            $this->note = $note;
            $this->title = $note->title;
            // Obtener el contenido HTML para mostrar en el editor
            $this->plainContent = $note->content_html ?? '';
            $this->icon = $note->icon;
            $this->folderId = $note->folder_id;
            $this->selectedTags = $note->tags->pluck('id')->toArray();
        }
    }

    protected $listeners = ['updateContent'];

    public function updateContent($content): void
    {
        $this->plainContent = $content;
        $this->saveNote();
    }

    public function updated($property): void
    {
        // Auto-guardar cuando cambia el título
        if ($property === 'title') {
            $this->saveNote();
        }
    }

    protected function saveNote(): void
    {
        $user = Auth::user();

        $data = [
            'title' => $this->title ?: 'Sin título',
            'content' => ['html' => $this->plainContent],
            'icon' => $this->icon,
            'folder_id' => $this->folderId,
        ];

        if (!$this->note) {
            $this->note = $user->notes()->create($data);
        } else {
            $this->note->update($data);
        }

        if ($this->note) {
            $this->note->tags()->sync($this->selectedTags);
        }
    }

    public function moveToFolder($folderId): void
    {
        $this->folderId = $folderId ? (int) $folderId : null;
        
        if ($this->note) {
            $this->note->update(['folder_id' => $this->folderId]);
        }
    }

    public function addTag(int $tagId): void
    {
        if (!in_array($tagId, $this->selectedTags)) {
            $this->selectedTags[] = $tagId;
            
            if ($this->note) {
                $this->note->tags()->sync($this->selectedTags);
            }
        }
    }

    public function removeTag(int $tagId): void
    {
        $this->selectedTags = array_values(array_filter($this->selectedTags, fn($id) => $id !== $tagId));
        
        if ($this->note) {
            $this->note->tags()->sync($this->selectedTags);
        }
    }

    public function createTag(string $name, string $color = '#6366F1'): void
    {
        $tag = Auth::user()->noteTags()->create([
            'name' => trim($name),
            'color' => $color,
        ]);

        $this->selectedTags[] = $tag->id;
        
        if ($this->note) {
            $this->note->tags()->sync($this->selectedTags);
        }
    }

    public function render()
    {
        $user = Auth::user();

        return view('livewire.notes.note-editor', [
            'folders' => $user->noteFolders()->orderBy('sort_order')->get(),
            'tags' => $user->noteTags()->get(),
            'noteTags' => $this->note ? $this->note->tags : collect(),
        ])->layout('components.layouts.app');
    }
}
