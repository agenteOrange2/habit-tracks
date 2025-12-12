<?php

namespace App\Livewire\Notes;

use Livewire\Component;
use App\Models\NoteFolder;
use Illuminate\Support\Facades\Auth;

class FolderManager extends Component
{
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?NoteFolder $folder = null;
    public string $name = '';
    public string $icon = '📁';
    public int $sortOrder = 0;

    protected $listeners = ['editFolder', 'deleteFolder'];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:10',
            'sortOrder' => 'nullable|integer',
        ];
    }

    public function openModal(): void
    {
        $this->reset(['name', 'icon', 'sortOrder', 'isEditing', 'folder']);
        $this->icon = '📁';
        $this->showModal = true;
    }

    public function editFolder(int $folderId): void
    {
        $this->folder = Auth::user()->noteFolders()->findOrFail($folderId);
        $this->name = $this->folder->name;
        $this->icon = $this->folder->icon;
        $this->sortOrder = $this->folder->sort_order;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'icon' => $this->icon ?: '📁',
            'sort_order' => $this->sortOrder,
        ];

        if ($this->isEditing && $this->folder) {
            $this->folder->update($data);
            session()->flash('success', 'Carpeta actualizada correctamente');
        } else {
            Auth::user()->noteFolders()->create($data);
            session()->flash('success', 'Carpeta creada correctamente');
        }

        $this->closeModal();
        $this->dispatch('folder-updated');
    }

    public function deleteFolder(int $folderId): void
    {
        $folder = Auth::user()->noteFolders()->findOrFail($folderId);
        
        // Mover todas las notas de esta carpeta a "Sin carpeta"
        $folder->notes()->update(['folder_id' => null]);
        
        $folder->delete();
        
        session()->flash('success', 'Carpeta eliminada. Las notas se movieron a "Sin carpeta"');
        $this->dispatch('folder-updated');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['name', 'icon', 'sortOrder', 'isEditing', 'folder']);
    }

    public function render()
    {
        return view('livewire.notes.folder-manager', [
            'folders' => Auth::user()->noteFolders()
                ->withCount('notes')
                ->orderBy('sort_order')
                ->get(),
        ])->layout('components.layouts.app');
    }
}

