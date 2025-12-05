<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    public function pomodoroSession(): HasMany
    {
        return $this->hasMany(pomodoroSession::class);
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }

    public function rewardClaims(): HasMany
    {
        return $this->hasMany(rewardClaims::class);
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
        return $this->belongsToMany(achievements::class, 'user_achievements')
            ->withPivot(['unlocked_at', 'progress'])
            ->withTimestamps();
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
        return $this->hasMany(journalEntry::class);
    }

    public function focusMode(): HasMany
    {
        return $this->hasMany(FocusMode::class);
    }

    public function habitReminders(): HasMany
    {
        return $this->hasMany(habitReminders::class);
    }
    
}
