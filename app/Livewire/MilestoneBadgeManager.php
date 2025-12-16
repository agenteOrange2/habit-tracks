<?php

namespace App\Livewire;

use App\Models\MilestoneBadge;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MilestoneBadgeManager extends Component
{
    public bool $showForm = false;
    public bool $isEditing = false;
    public ?int $editingBadgeId = null;
    
    // Form fields
    public string $name = '';
    public string $icon = '⭐';
    public int $levelRequired = 10;
    public string $description = '';
    
    // Available emoji icons
    public array $availableIcons = [
        '⭐', '🌟', '✨', '💫', '🏆', '🎖️', '🥇', '🥈', '🥉',
        '💎', '👑', '🔥', '⚡', '🚀', '🎯', '💪', '🏅', '🎉',
        '🌈', '🦋', '🌸', '🍀', '🎸', '🎨', '📚', '💡', '🔮',
    ];

    protected $rules = [
        'name' => 'required|string|max:100',
        'icon' => 'required|string|max:10',
        'levelRequired' => 'required|integer|min:1|max:1000',
        'description' => 'nullable|string|max:255',
    ];

    #[Computed]
    public function badges()
    {
        return Auth::user()->milestoneBadges()->ordered()->get();
    }

    #[Computed]
    public function userLevel(): int
    {
        return Auth::user()->level?->current_level ?? 1;
    }

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->isEditing = false;
    }

    public function openEditForm(int $badgeId): void
    {
        $badge = MilestoneBadge::where('user_id', Auth::id())->find($badgeId);
        
        if (!$badge) {
            session()->flash('error', 'Insignia no encontrada.');
            return;
        }
        
        $this->editingBadgeId = $badge->id;
        $this->name = $badge->name;
        $this->icon = $badge->icon;
        $this->levelRequired = $badge->level_required;
        $this->description = $badge->description ?? '';
        $this->showForm = true;
        $this->isEditing = true;
    }

    public function save(): void
    {
        $this->validate();
        
        if ($this->isEditing && $this->editingBadgeId) {
            $badge = MilestoneBadge::where('user_id', Auth::id())->find($this->editingBadgeId);
            
            if ($badge) {
                $badge->update([
                    'name' => $this->name,
                    'icon' => $this->icon,
                    'level_required' => $this->levelRequired,
                    'description' => $this->description ?: null,
                ]);
                session()->flash('success', 'Insignia actualizada correctamente.');
            }
        } else {
            // Get max sort_order for new badge
            $maxOrder = Auth::user()->milestoneBadges()->max('sort_order') ?? -1;
            
            MilestoneBadge::create([
                'user_id' => Auth::id(),
                'name' => $this->name,
                'icon' => $this->icon,
                'level_required' => $this->levelRequired,
                'description' => $this->description ?: null,
                'is_default' => false,
                'sort_order' => $maxOrder + 1,
            ]);
            session()->flash('success', 'Insignia creada correctamente.');
        }
        
        // Clear computed property cache to refresh the badges list
        unset($this->badges);
        
        $this->closeForm();
    }

    public function delete(int $badgeId): void
    {
        $badge = MilestoneBadge::where('user_id', Auth::id())->find($badgeId);
        
        if (!$badge) {
            session()->flash('error', 'Insignia no encontrada.');
            return;
        }
        
        if ($badge->is_default) {
            session()->flash('error', 'No puedes eliminar insignias predeterminadas.');
            return;
        }
        
        $badge->delete();
        
        // Clear computed property cache to refresh the badges list
        unset($this->badges);
        
        session()->flash('success', 'Insignia eliminada correctamente.');
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->icon = '⭐';
        $this->levelRequired = 10;
        $this->description = '';
        $this->editingBadgeId = null;
        $this->isEditing = false;
    }

    public function selectIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function render()
    {
        return view('livewire.milestone-badge-manager');
    }
}
