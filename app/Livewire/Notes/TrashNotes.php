<?php

namespace App\Livewire\Notes;

use Livewire\Component;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;

class TrashNotes extends Component
{
    public function restore(int $noteId): void
    {
        $note = Auth::user()->notes()->onlyTrashed()->find($noteId);
        
        if (!$note) {
            session()->flash('error', 'Nota no encontrada');
            return;
        }

        $note->restore();
        session()->flash('success', 'Nota restaurada');
    }

    public function permanentDelete(int $noteId): void
    {
        $note = Auth::user()->notes()->onlyTrashed()->find($noteId);
        
        if (!$note) {
            session()->flash('error', 'Nota no encontrada');
            return;
        }

        $note->forceDelete();
        session()->flash('success', 'Nota eliminada permanentemente');
    }

    public function emptyTrash(): void
    {
        Auth::user()->notes()->onlyTrashed()->forceDelete();
        session()->flash('success', 'Papelera vaciada');
    }

    public function render()
    {
        $trashedNotes = Auth::user()->notes()
            ->onlyTrashed()
            ->latest('deleted_at')
            ->get();

        return view('livewire.notes.trash-notes', [
            'notes' => $trashedNotes,
        ])->layout('components.layouts.app');
    }
}
