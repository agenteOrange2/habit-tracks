<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne, BelongsToMany};
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'theme_id',
        'energy_level',
        'last_energy_update',
        'avatar_seed',
        'avatar_style',
        'custom_avatar',
        'player_class',
        'cover_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_energy_update' => 'datetime'
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the user's avatar URL (custom or DiceBear)
     * Priority: avatar_seed (gallery) > custom_avatar (uploaded photo)
     */
    public function getAvatarUrlAttribute(): string
    {
        // If user selected a gallery avatar (avatar_seed is set), use DiceBear
        if ($this->avatar_seed) {
            $style = $this->avatar_style ?? 'notionists';
            return "https://api.dicebear.com/7.x/{$style}/svg?seed={$this->avatar_seed}";
        }
        
        // If user has a custom avatar uploaded, use it
        if ($this->custom_avatar) {
            return asset('storage/' . $this->custom_avatar);
        }
        
        // Default: use email as seed for DiceBear
        $style = $this->avatar_style ?? 'notionists';
        return "https://api.dicebear.com/7.x/{$style}/svg?seed={$this->email}";
    }

    /**
     * Get the user's cover image URL
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return null;
    }

    /**
     * Get player class configuration
     */
    public function getPlayerClassConfigAttribute(): array
    {
        $classes = [
            'guerrero' => ['name' => 'Guerrero', 'icon' => '⚔️', 'bg' => 'bg-red-100', 'text' => 'text-red-700'],
            'mago' => ['name' => 'Mago', 'icon' => '🔮', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
            'sanador' => ['name' => 'Sanador', 'icon' => '🌿', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
            'arquero' => ['name' => 'Arquero', 'icon' => '🏹', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
            'programador' => ['name' => 'Programador', 'icon' => '💻', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
        ];

        return $classes[$this->player_class ?? 'programador'] ?? $classes['programador'];
    }


    /* Categories & Difficulties */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function difficulties(): HasMany
    {
        return $this->hasMany(Difficulty::class);
    }

    /* Habits Models */
    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }
    
    public function habitLogs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    public function pomodoroSessions(): HasMany
    {
        return $this->hasMany(PomodoroSession::class);
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }

    public function rewardClaims(): HasMany
    {
        return $this->hasMany(RewardClaim::class);
    }

    public function stats(): HasOne
    {
        return $this->hasOne(UserStats::class);
    }

    public function level(): HasOne
    {
        return $this->hasOne(UserLevel::class);
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot(['unlocked_at', 'progress'])
            ->withTimestamps()
            ->withCasts(['unlocked_at' => 'datetime']);
    }

    public function challenges(): BelongsToMany
    {
        return $this->belongsToMany(Challenge::class, 'user_challenges')
            ->withPivot(['status', 'progress', 'completed_at'])
            ->withTimestamps();
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function journalCategories(): HasMany
    {
        return $this->hasMany(JournalCategory::class)->ordered();
    }

    public function focusMode(): HasMany
    {
        return $this->hasMany(FocusMode::class);
    }

    public function habitReminders(): HasMany
    {
        return $this->hasMany(HabitReminder::class);
    }

    /* Notes */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function noteFolders(): HasMany
    {
        return $this->hasMany(NoteFolder::class);
    }

    public function noteTags(): HasMany
    {
        return $this->hasMany(NoteTag::class);
    }

    /* Calendar */
    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }

    public function calendarSetting(): HasOne
    {
        return $this->hasOne(CalendarSetting::class);
    }

    public function googleCalendarToken(): HasOne
    {
        return $this->hasOne(GoogleCalendarToken::class);
    }

    /* Milestone Badges */
    public function milestoneBadges(): HasMany
    {
        return $this->hasMany(MilestoneBadge::class)->ordered();
    }

    /**
     * Get today's completed Pomodoros count
     */
    public function getTodayPomodorosAttribute(): int
    {
        return $this->pomodoroSessions()
            ->whereDate('started_at', today())
            ->where('was_interrupted', false)
            ->whereNotNull('completed_at')
            ->count();
    }

    /**
     * Get today's total focus time in minutes
     */
    public function getTodayFocusTimeAttribute(): int
    {
        return $this->pomodoroSessions()
            ->whereDate('started_at', today())
            ->where('was_interrupted', false)
            ->whereNotNull('completed_at')
            ->sum('duration_minutes');
    }

    /**
     * Get user's Pomodoro settings
     */
    public function pomodoroSettings(): HasOne
    {
        return $this->hasOne(UserPomodoroSettings::class);
    }

    /**
     * Get or create Pomodoro settings for user
     */
    public function getOrCreatePomodoroSettings(): UserPomodoroSettings
    {
        return $this->pomodoroSettings()->firstOrCreate(
            ['user_id' => $this->id],
            [
                'short_break_duration' => 5,
                'long_break_duration' => 15,
                'auto_start_breaks' => true,
                'sound_enabled' => true,
                'cycle_count' => 0,
            ]
        );
    }
    
}
