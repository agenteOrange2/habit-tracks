<?php

namespace App\Livewire\Admin\Difficulties;

use App\Models\Difficulty;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class DifficultyList extends Component
{
    public $showDeleteModal = false;
    public $difficultyToDeleteId = null;
    public $difficultyToDeleteName = '';
    public $deleteError = '';

    protected $listeners = [
        'difficultyCreated' => '$refresh',
        'difficultyUpdated' => '$refresh',
    ];

    public function mount()
    {
        // Check authorization
        Gate::authorize('viewAny', Difficulty::class);
    }

    public function toggleActive(int $difficultyId): void
    {
        try {
            $difficulty = Difficulty::findOrFail($difficultyId);
            
            // Check authorization
            Gate::authorize('update', $difficulty);
            
            $difficulty->is_active = !$difficulty->is_active;
            $difficulty->save();
            
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => $difficulty->is_active 
                    ? 'Dificultad activada correctamente' 
                    : 'Dificultad desactivada correctamente'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Error al cambiar el estado de la dificultad'
            ]);
        }
    }

    public function confirmDelete(int $difficultyId): void
    {
        $difficulty = Difficulty::findOrFail($difficultyId);
        
        // Check authorization
        Gate::authorize('delete', $difficulty);
        
        $this->difficultyToDeleteId = $difficulty->id;
        $this->difficultyToDeleteName = $difficulty->name;
        $this->deleteError = '';
        
        // Check if difficulty can be deleted
        if (!$difficulty->canBeDeleted()) {
            $habitsCount = $difficulty->getHabitsCount();
            $this->deleteError = "No se puede eliminar porque hay {$habitsCount} hábito(s) asociado(s)";
        }
        
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if (!$this->difficultyToDeleteId) {
            $this->closeDeleteModal();
            return;
        }
        
        try {
            $difficulty = Difficulty::findOrFail($this->difficultyToDeleteId);
            
            // Check authorization
            Gate::authorize('delete', $difficulty);
            
            // Double check if can be deleted
            if (!$difficulty->canBeDeleted()) {
                $habitsCount = $difficulty->getHabitsCount();
                $this->dispatch('notification', [
                    'type' => 'error',
                    'message' => "No se puede eliminar porque hay {$habitsCount} hábito(s) asociado(s)"
                ]);
                $this->closeDeleteModal();
                return;
            }
            
            $difficultyName = $difficulty->name;
            $difficulty->delete();
            
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => "Dificultad '{$difficultyName}' eliminada correctamente"
            ]);
            
            $this->closeDeleteModal();
            
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Error al eliminar la dificultad'
            ]);
            $this->closeDeleteModal();
        }
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->difficultyToDeleteId = null;
        $this->difficultyToDeleteName = '';
        $this->deleteError = '';
    }

    public function updateOrder(array $orderedIds): void
    {
        try {
            // Validate that all IDs exist
            $difficulties = Difficulty::whereIn('id', $orderedIds)->get();
            
            if ($difficulties->count() !== count($orderedIds)) {
                throw new \Exception('Invalid difficulty IDs provided');
            }
            
            // Check for duplicate order values before updating
            $orderValues = [];
            foreach ($orderedIds as $index => $difficultyId) {
                $newOrder = $index + 1;
                if (in_array($newOrder, $orderValues)) {
                    throw new \Exception('Duplicate order values detected');
                }
                $orderValues[] = $newOrder;
            }
            
            // Update order for each difficulty
            foreach ($orderedIds as $index => $difficultyId) {
                Difficulty::where('id', $difficultyId)->update(['order' => $index + 1]);
            }
            
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Orden actualizado correctamente'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Error al actualizar el orden: ' . $e->getMessage()
            ]);
        }
    }
    
    public function moveUp(int $difficultyId): void
    {
        $difficulties = Difficulty::ordered()->get();
        $currentIndex = $difficulties->search(fn($d) => $d->id === $difficultyId);
        
        if ($currentIndex > 0) {
            $orderedIds = $difficulties->pluck('id')->toArray();
            $temp = $orderedIds[$currentIndex];
            $orderedIds[$currentIndex] = $orderedIds[$currentIndex - 1];
            $orderedIds[$currentIndex - 1] = $temp;
            $this->updateOrder($orderedIds);
        }
    }
    
    public function moveDown(int $difficultyId): void
    {
        $difficulties = Difficulty::ordered()->get();
        $currentIndex = $difficulties->search(fn($d) => $d->id === $difficultyId);
        
        if ($currentIndex < $difficulties->count() - 1) {
            $orderedIds = $difficulties->pluck('id')->toArray();
            $temp = $orderedIds[$currentIndex];
            $orderedIds[$currentIndex] = $orderedIds[$currentIndex + 1];
            $orderedIds[$currentIndex + 1] = $temp;
            $this->updateOrder($orderedIds);
        }
    }

    public function render()
    {
        $difficulties = Difficulty::query()
            ->withCount('habits')
            ->ordered()
            ->get();
        
        return view('livewire.admin.difficulties.difficulty-list', [
            'difficulties' => $difficulties,
        ])->layout('components.layouts.app');
    }
}
