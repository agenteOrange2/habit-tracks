<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LevelProgress extends Component
{
    public $level;
    public $percentage;
    public $title;

    protected $listeners = [
        'levelUpdated' => 'refresh',
    ];

    public function mount(): void
    {
        $this->refresh();
    }

    public function refresh(): void
    {
        $userLevel = Auth::user()->level;
        
        $this->level = $userLevel->current_level;
        $this->percentage = $userLevel->progress_percentage;
        $this->title = $userLevel->level_title;
    }

    public function render()
    {
        return view('livewire.profile.level-progress');
    }
}