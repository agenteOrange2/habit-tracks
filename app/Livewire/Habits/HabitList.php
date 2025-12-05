<?php

namespace App\Livewire\Habits;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class HabitList extends Component
{
    use WithPagination;

    public $filter = 'active'; // active, archived, all
    public $categoryFilter = 'all';
    public $search = '';

    protected $listeners = [
        'habitCreated' => '$refresh',
        'habitUpdated' => '$refresh',
        'habitDeleted' => '$refresh',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function setFilter($filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function setCategoryFilter($category): void
    {
        $this->categoryFilter = $category;
        $this->resetPage();
    }

    public function render()
    {
        $query = Auth::user()->habits()
            ->with(['logs' => function ($query) {
                $query->whereDate('completed_date', today());
            }]);

        // Filtro de estado
        if ($this->filter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->filter === 'archived') {
            $query->where('is_active', false);
        }

        // Filtro de categoría
        if ($this->categoryFilter !== 'all') {
            $query->where('category', $this->categoryFilter);
        }

        // Búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $habits = $query->latest()->paginate(12);

        return view('livewire.habits.habit-list', [
            'habits' => $habits,
        ])->layout('layouts.app');
    }
}