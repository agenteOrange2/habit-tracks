<?php

namespace App\Livewire\Journal;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\JournalEntry;
use App\Models\JournalCategory;
use App\Enums\Mood;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JournalList extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $filterMood = null;
    public ?string $filterMonth = null;
    public ?int $filterCategory = null;
    
    // Category form
    public bool $showCategoryForm = false;
    public bool $isEditingCategory = false;
    public ?int $editingCategoryId = null;
    public string $categoryName = '';
    public string $categoryIcon = '📁';
    public string $categoryColor = '#6366F1';
    
    public array $availableIcons = [
        '📁', '📂', '💼', '📝', '✏️', '📖', '📚', '🎯', '💡', '🌟',
        '❤️', '💪', '🧠', '🎨', '🎵', '🏃', '🍎', '💰', '🏠', '✈️',
        '🌱', '🔥', '⭐', '🎉', '💎', '🌈', '☀️', '🌙', '🍀', '🦋',
    ];
    
    public array $availableColors = [
        '#6366F1', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
        '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#14B8A6',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'filterMood' => ['except' => null, 'as' => 'mood'],
        'filterMonth' => ['except' => null, 'as' => 'month'],
        'filterCategory' => ['except' => null, 'as' => 'category'],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setMoodFilter(?string $mood): void
    {
        $this->filterMood = $mood;
        $this->resetPage();
    }

    public function setMonthFilter(?string $month): void
    {
        $this->filterMonth = $month;
        $this->resetPage();
    }
    
    public function setCategoryFilter(?int $categoryId): void
    {
        $this->filterCategory = $categoryId;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->filterMood = null;
        $this->filterMonth = null;
        $this->filterCategory = null;
        $this->resetPage();
    }

    public function deleteEntry(JournalEntry $entry): void
    {
        if ($entry->user_id !== Auth::id()) {
            return;
        }
        $entry->delete();
    }
    
    // Category Management
    public function openCategoryForm(): void
    {
        $this->resetCategoryForm();
        $this->showCategoryForm = true;
    }
    
    public function editCategory(int $categoryId): void
    {
        $category = JournalCategory::where('user_id', Auth::id())->find($categoryId);
        if (!$category) return;
        
        $this->editingCategoryId = $category->id;
        $this->categoryName = $category->name;
        $this->categoryIcon = $category->icon;
        $this->categoryColor = $category->color;
        $this->isEditingCategory = true;
        $this->showCategoryForm = true;
    }
    
    public function saveCategory(): void
    {
        $this->validate([
            'categoryName' => 'required|string|max:100',
            'categoryIcon' => 'required|string|max:10',
            'categoryColor' => 'required|string|max:7',
        ]);
        
        if ($this->isEditingCategory && $this->editingCategoryId) {
            $category = JournalCategory::where('user_id', Auth::id())->find($this->editingCategoryId);
            if ($category) {
                $category->update([
                    'name' => $this->categoryName,
                    'icon' => $this->categoryIcon,
                    'color' => $this->categoryColor,
                ]);
            }
        } else {
            $maxOrder = Auth::user()->journalCategories()->max('sort_order') ?? -1;
            JournalCategory::create([
                'user_id' => Auth::id(),
                'name' => $this->categoryName,
                'icon' => $this->categoryIcon,
                'color' => $this->categoryColor,
                'sort_order' => $maxOrder + 1,
            ]);
        }
        
        $this->closeCategoryForm();
    }
    
    public function deleteCategory(int $categoryId): void
    {
        $category = JournalCategory::where('user_id', Auth::id())->find($categoryId);
        if (!$category) return;
        
        // Set entries to null category
        JournalEntry::where('category_id', $categoryId)->update(['category_id' => null]);
        
        $category->delete();
        
        if ($this->filterCategory === $categoryId) {
            $this->filterCategory = null;
        }
    }
    
    public function closeCategoryForm(): void
    {
        $this->showCategoryForm = false;
        $this->resetCategoryForm();
    }
    
    private function resetCategoryForm(): void
    {
        $this->categoryName = '';
        $this->categoryIcon = '📁';
        $this->categoryColor = '#6366F1';
        $this->editingCategoryId = null;
        $this->isEditingCategory = false;
    }

    public function render()
    {
        $user = Auth::user();
        
        $query = JournalEntry::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where('content', 'like', '%' . $this->search . '%');
        }

        if ($this->filterMood) {
            $query->where('mood', $this->filterMood);
        }

        if ($this->filterMonth) {
            $date = Carbon::parse($this->filterMonth);
            $query->whereYear('created_at', $date->year)
                  ->whereMonth('created_at', $date->month);
        }
        
        if ($this->filterCategory !== null) {
            if ($this->filterCategory === 0) {
                $query->whereNull('category_id');
            } else {
                $query->where('category_id', $this->filterCategory);
            }
        }

        // Get available months for filter
        $months = JournalEntry::where('user_id', $user->id)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month')
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');
            
        // Get user categories
        $categories = $user->journalCategories;

        // Stats
        $totalEntries = JournalEntry::where('user_id', $user->id)->count();
        $thisMonthEntries = JournalEntry::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $uncategorizedCount = JournalEntry::where('user_id', $user->id)->whereNull('category_id')->count();

        return view('livewire.journal.journal-list', [
            'entries' => $query->paginate(10),
            'moods' => Mood::cases(),
            'months' => $months,
            'categories' => $categories,
            'totalEntries' => $totalEntries,
            'thisMonthEntries' => $thisMonthEntries,
            'uncategorizedCount' => $uncategorizedCount,
        ])->layout('components.layouts.app');
    }
}
