<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class GeneralSettings extends Component
{
    public $name;
    public $email;

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ];
    }

    public function save(): void
    {
        $this->validate();

        Auth::user()->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        session()->flash('success', 'Configuración actualizada correctamente.');
    }

    public function render()
    {
        return view('livewire.settings.general-settings')
            ->layout('layouts.app');
    }
}