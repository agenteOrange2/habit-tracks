<?php

namespace App\Livewire\Notes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Note, NoteFolder, NoteTag};
use Illuminate\Support\Facades\Auth;

class NotesList extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $selectedFolder = null;
    public ?int $selectedTag = null;
    public string $view = 'grid'; // grid or list

    protected $listeners = ['folder-updated' => '$refresh', 'tag-updated' => '$refresh'];

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedFolder' => ['except' => null, 'as' => 'folder'],
        'selectedTag' => ['except' => null, 'as' => 'tag'],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setFolder(?int $folderId): void
    {
        $this->selectedFolder = $folderId;
        $this->selectedTag = null;
        $this->resetPage();
    }

    public function setTag(?int $tagId): void
    {
        $this->selectedTag = $tagId;
        $this->selectedFolder = null;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedFolder = null;
        $this->selectedTag = null;
        $this->resetPage();
    }

    public function createNote(): void
    {
        $note = Auth::user()->notes()->create([
            'title' => 'Sin título',
            'folder_id' => $this->selectedFolder,
        ]);

        $this->redirect(route('notes.edit', $note), navigate: true);
    }

    public function pinNote(Note $note): void
    {
        if ($note->user_id !== Auth::id()) {
            return;
        }

        if ($note->is_pinned) {
            $note->unpin();
        } else {
            $note->pin();
        }
    }

    public function deleteNote(Note $note): void
    {
        if ($note->user_id !== Auth::id()) {
            return;
        }

        $note->delete();
        session()->flash('success', 'Nota movida a la papelera');
    }

    public function createFolder(string $name): void
    {
        Auth::user()->noteFolders()->create([
            'name' => trim($name),
        ]);
    }

    public function createTag(string $name, string $color = '#6366F1'): void
    {
        Auth::user()->noteTags()->create([
            'name' => trim($name),
            'color' => $color,
        ]);
    }

    public function render()
    {
        $user = Auth::user();

        $query = $user->notes()->ordered();

        if ($this->search) {
            $query->search($this->search);
        }

        if ($this->selectedFolder !== null) {
            if ($this->selectedFolder === 0) {
                $query->whereNull('folder_id');
            } else {
                $query->where('folder_id', $this->selectedFolder);
            }
        }

        if ($this->selectedTag) {
            $query->withTag($this->selectedTag);
        }

        return view('livewire.notes.notes-list', [
            'notes' => $query->paginate(12),
            'folders' => $user->noteFolders()->withCount('notes')->orderBy('sort_order')->get(),
            'tags' => $user->noteTags()->get(),
            'totalNotes' => $user->notes()->count(),
            'unfolderedCount' => $user->notes()->whereNull('folder_id')->count(),
        ])->layout('components.layouts.app');
    }
}
