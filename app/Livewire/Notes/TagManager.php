<?php

namespace App\Livewire\Notes;

use Livewire\Component;
use App\Models\NoteTag;
use Illuminate\Support\Facades\Auth;

class TagManager extends Component
{
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?NoteTag $tag = null;
    public string $name = '';
    public string $color = '#E9E9E7';

    protected $listeners = ['editTag', 'deleteTag'];

    protected function rules(): array
    {
        $userId = Auth::id();
        $tagId = $this->tag?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                "unique:note_tags,name,{$tagId},id,user_id,{$userId}",
            ],
            'color' => 'required|string|size:7',
        ];
    }

    protected $messages = [
        'name.unique' => 'Ya tienes una etiqueta con ese nombre.',
    ];

    public function openModal(): void
    {
        $this->reset(['name', 'color', 'isEditing', 'tag']);
        $this->color = '#E9E9E7';
        $this->showModal = true;
    }

    public function editTag(int $tagId): void
    {
        $this->tag = Auth::user()->noteTags()->findOrFail($tagId);
        $this->name = $this->tag->name;
        $this->color = $this->tag->color;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'color' => $this->color,
        ];

        if ($this->isEditing && $this->tag) {
            $this->tag->update($data);
            session()->flash('success', 'Etiqueta actualizada correctamente');
        } else {
            Auth::user()->noteTags()->create($data);
            session()->flash('success', 'Etiqueta creada correctamente');
        }

        $this->closeModal();
        $this->dispatch('tag-updated');
    }

    public function deleteTag(int $tagId): void
    {
        $tag = Auth::user()->noteTags()->findOrFail($tagId);
        
        // Las relaciones se eliminarán automáticamente por cascadeOnDelete
        $tag->delete();
        
        session()->flash('success', 'Etiqueta eliminada correctamente');
        $this->dispatch('tag-updated');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['name', 'color', 'isEditing', 'tag']);
    }

    public function render()
    {
        return view('livewire.notes.tag-manager', [
            'tags' => Auth::user()->noteTags()
                ->withCount('notes')
                ->orderBy('name')
                ->get(),
            'availableColors' => NoteTag::COLORS,
        ])->layout('components.layouts.app');
    }
}

