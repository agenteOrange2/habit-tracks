<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use App\Models\Theme;
use App\Services\PointsService;
use Illuminate\Support\Facades\Auth;

class ThemeSelector extends Component
{
    public $themes;
    public $currentTheme;
    public $availablePoints;

    public function mount(): void
    {
        $this->loadThemes();
        $this->currentTheme = Auth::user()->theme_id;
        $this->availablePoints = Auth::user()->stats->available_points;
    }

    public function loadThemes(): void
    {
        $this->themes = Theme::all();
    }

    public function selectTheme(Theme $theme, PointsService $pointsService): void
    {
        $user = Auth::user();

        // Si es el tema actual, no hacer nada
        if ($user->theme_id === $theme->id) {
            return;
        }

        // Si el tema cuesta puntos y el usuario no lo ha desbloqueado
        if ($theme->cost_points > 0 && !$theme->isUnlockedBy($user)) {
            if (!$theme->canBeUnlockedBy($user)) {
                session()->flash('error', '❌ No tienes suficientes puntos para este tema.');
                return;
            }

            // Gastar puntos
            if (!$pointsService->spendPoints($user, $theme->cost_points)) {
                session()->flash('error', '❌ No se pudo desbloquear el tema.');
                return;
            }

            session()->flash('success', "🎨 ¡Tema '{$theme->name}' desbloqueado!");
        }

        // Aplicar tema
        $user->update(['theme_id' => $theme->id]);
        $this->currentTheme = $theme->id;
        $this->availablePoints = $user->stats->available_points;

        session()->flash('success', "✅ Tema '{$theme->name}' aplicado correctamente.");
    }

    public function render()
    {
        return view('livewire.profile.theme-selector')
            ->layout('layouts.app');
    }
}