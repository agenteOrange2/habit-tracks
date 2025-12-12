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
        'theme_id',
        'energy_level',
        'last_energy_update'
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

    /* Journal */
    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
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
