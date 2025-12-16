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
        // If user selected a gallery avatar, use DiceBear
        if ($this->avatar_seed) {
            return "https://api.dicebear.com/7.x/notionists/svg?seed={$this->avatar_seed}";
        }
        
        // If there's a custom avatar uploaded, use it
        $customAvatar = Auth::user()->custom_avatar;
        if ($customAvatar) {
            return asset('storage/' . $customAvatar);
        }
        
        // Default: use email as seed
        return "https://api.dicebear.com/7.x/notionists/svg?seed=" . Auth::user()->email;
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
        $user = Auth::user();
        
        // Update local state
        $this->avatar_seed = $seed;
        
        // Save to database
        $user->avatar_seed = $seed;
        $user->save();
        
        // Refresh user to ensure changes are persisted
        $user->refresh();
        
        session()->flash('message', 'Apariencia actualizada correctamente');
        $this->dispatch('profile-updated', name: $user->name);
    }
    
    public function selectCustomAvatar(): void
    {
        $user = Auth::user();
        
        if ($user->custom_avatar) {
            // Update local state
            $this->custom_avatar = $user->custom_avatar;
            $this->avatar_seed = null;
            
            // Save to database
            $user->avatar_seed = null;
            $user->save();
            
            // Refresh user
            $user->refresh();
            
            session()->flash('message', 'Foto personalizada seleccionada');
            $this->dispatch('profile-updated', name: $user->name);
        }
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

        // Store new avatar in user-specific folder
        $path = $this->newAvatar->store('avatars/' . $user->id, 'public');
        $this->custom_avatar = $path;
        $this->avatar_seed = null; // Clear seed when using custom avatar
        
        // Save immediately
        $user->update([
            'custom_avatar' => $path,
            'avatar_seed' => null,
        ]);

        $this->newAvatar = null;
        session()->flash('message', 'Avatar actualizado correctamente');
        $this->dispatch('profile-updated', name: $user->name);
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

        // Store new cover in user-specific folder
        $path = $this->newCover->store('covers/' . $user->id, 'public');
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
            'player_class' => ['required', 'string', 'in:guerrero,mago,sanador,arquero,programador'],
        ]);

        // Only update name, email, and player_class
        // avatar_seed is managed separately by selectAvatar() and selectCustomAvatar()
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
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

    public function runOptimizeClear(): void
    {
        try {
            \Artisan::call('optimize:clear');
            session()->flash('message', 'Cache limpiada correctamente (optimize:clear)');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al limpiar cache: ' . $e->getMessage());
        }
    }

    public function runStorageLink(): void
    {
        try {
            \Artisan::call('storage:link');
            session()->flash('message', 'Storage link creado correctamente');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear storage link: ' . $e->getMessage());
        }
    }

    public function runCacheClear(): void
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');
            session()->flash('message', 'Todas las caches limpiadas correctamente');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al limpiar cache: ' . $e->getMessage());
        }
    }

    #[\Livewire\Attributes\Computed]
    public function debugInfo(): array
    {
        $user = Auth::user();
        $habits = $user->habits()->select('id', 'name', 'user_id')->get();
        
        return [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'habits_count' => $habits->count(),
            'habits' => $habits->map(fn($h) => "ID:{$h->id} - {$h->name} (user_id:{$h->user_id})")->toArray(),
            'app_url' => config('app.url'),
            'app_env' => config('app.env'),
            'livewire_url' => url('/livewire/update'),
            'asset_url' => config('app.asset_url') ?? 'not set',
        ];
    }

    public function render()
    {
        return view('livewire.settings.profile');
    }
}
