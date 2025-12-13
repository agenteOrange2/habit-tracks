<?php

namespace App\Livewire\Settings;

use App\Livewire\Actions\Logout;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public ?string $avatar_seed = null;
    public ?string $custom_avatar = null;
    public ?string $cover_image = null;
    public string $player_class = 'programador';
    public string $deletePassword = '';
    
    // Password fields
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';
    
    // File uploads
    public $newAvatar = null;
    public $newCover = null;

    // Available avatar seeds (male and female options)
    public array $avatarOptions = [
        ['seed' => 'Felix', 'name' => 'Felix', 'bg' => 'bg-red-100'],
        ['seed' => 'Aneka', 'name' => 'Aneka', 'bg' => 'bg-blue-100'],
        ['seed' => 'Milo', 'name' => 'Milo', 'bg' => 'bg-green-100'],
        ['seed' => 'Luna', 'name' => 'Luna', 'bg' => 'bg-purple-100'],
        ['seed' => 'Max', 'name' => 'Max', 'bg' => 'bg-yellow-100'],
        ['seed' => 'Sofia', 'name' => 'Sofia', 'bg' => 'bg-pink-100'],
        ['seed' => 'Leo', 'name' => 'Leo', 'bg' => 'bg-orange-100'],
        ['seed' => 'Emma', 'name' => 'Emma', 'bg' => 'bg-teal-100'],
    ];

    // Available player classes
    public array $playerClasses = [
        ['id' => 'guerrero', 'name' => 'Guerrero', 'icon' => '⚔️', 'bg' => 'bg-red-100', 'text' => 'text-red-700'],
        ['id' => 'mago', 'name' => 'Mago', 'icon' => '🔮', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
        ['id' => 'sanador', 'name' => 'Sanador', 'icon' => '🌿', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
        ['id' => 'arquero', 'name' => 'Arquero', 'icon' => '🏹', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
        ['id' => 'programador', 'name' => 'Programador', 'icon' => '💻', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
    ];


    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->avatar_seed = $user->avatar_seed;
        $this->custom_avatar = $user->custom_avatar;
        $this->cover_image = $user->cover_image;
        $this->player_class = $user->player_class ?? 'programador';
    }

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function currentAvatarUrl(): string
    {
        // If there's a custom avatar uploaded, use it
        if ($this->custom_avatar) {
            return asset('storage/' . $this->custom_avatar);
        }
        // Otherwise use DiceBear
        $seed = $this->avatar_seed ?? Auth::user()->email;
        return "https://api.dicebear.com/7.x/notionists/svg?seed={$seed}";
    }

    #[Computed]
    public function currentCoverUrl(): ?string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return null;
    }

    #[Computed]
    public function selectedClassConfig(): array
    {
        return collect($this->playerClasses)->firstWhere('id', $this->player_class) 
            ?? $this->playerClasses[4];
    }

    #[Computed]
    public function userLevel()
    {
        return Auth::user()->level;
    }

    public function selectAvatar(string $seed): void
    {
        $this->avatar_seed = $seed;
        $this->custom_avatar = null; // Clear custom avatar when selecting from catalog
    }

    public function selectClass(string $classId): void
    {
        $this->player_class = $classId;
    }

    public function updatedNewAvatar(): void
    {
        $this->validate([
            'newAvatar' => 'image|max:2048', // 2MB max
        ]);

        $user = Auth::user();
        
        // Delete old custom avatar if exists
        if ($user->custom_avatar) {
            Storage::disk('public')->delete($user->custom_avatar);
        }

        // Store new avatar
        $path = $this->newAvatar->store('avatars', 'public');
        $this->custom_avatar = $path;
        $this->avatar_seed = null; // Clear seed when using custom avatar
        
        // Save immediately
        $user->update([
            'custom_avatar' => $path,
            'avatar_seed' => null,
        ]);

        $this->newAvatar = null;
        session()->flash('message', 'Avatar actualizado correctamente');
    }

    public function updatedNewCover(): void
    {
        $this->validate([
            'newCover' => 'image|max:4096', // 4MB max
        ]);

        $user = Auth::user();
        
        // Delete old cover if exists
        if ($user->cover_image) {
            Storage::disk('public')->delete($user->cover_image);
        }

        // Store new cover
        $path = $this->newCover->store('covers', 'public');
        $this->cover_image = $path;
        
        // Save immediately
        $user->update(['cover_image' => $path]);

        $this->newCover = null;
        session()->flash('message', 'Portada actualizada correctamente');
    }

    public function removeCustomAvatar(): void
    {
        $user = Auth::user();
        
        if ($user->custom_avatar) {
            Storage::disk('public')->delete($user->custom_avatar);
            $user->update(['custom_avatar' => null]);
            $this->custom_avatar = null;
        }
        
        session()->flash('message', 'Avatar personalizado eliminado');
    }


    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'avatar_seed' => ['nullable', 'string', 'max:100'],
            'player_class' => ['required', 'string', 'in:guerrero,mago,sanador,arquero,programador'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'avatar_seed' => $this->custom_avatar ? null : $validated['avatar_seed'],
            'player_class' => $validated['player_class'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        session()->flash('message', 'Perfil actualizado correctamente');
        $this->dispatch('profile-updated', name: $user->name);
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('admin.dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', PasswordRule::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');
        session()->flash('passwordMessage', 'Contraseña actualizada correctamente');
    }

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'deletePassword' => ['required', 'string', 'current_password'],
        ]);

        $user = Auth::user();
        
        // Delete user's uploaded files
        if ($user->custom_avatar) {
            Storage::disk('public')->delete($user->custom_avatar);
        }
        if ($user->cover_image) {
            Storage::disk('public')->delete($user->cover_image);
        }

        tap($user, $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.settings.profile');
    }
}
