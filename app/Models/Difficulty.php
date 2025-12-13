<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Difficulty extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'points',
        'icon',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'points' => 'integer',
    ];

    protected static function booted(): void
    {
        // Auto-assign user_id on creation
        static::creating(function ($difficulty) {
            if (empty($difficulty->user_id) && auth()->check()) {
                $difficulty->user_id = auth()->id();
            }
            if (empty($difficulty->slug)) {
                $difficulty->slug = Str::slug($difficulty->name);
            }
        });

        static::updating(function ($difficulty) {
            if ($difficulty->isDirty('name')) {
                $difficulty->slug = Str::slug($difficulty->name);
            }
        });

        // Global scope to filter by authenticated user
        static::addGlobalScope('user', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('difficulties.user_id', auth()->id());
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }

    public function activeHabits(): HasMany
    {
        return $this->hasMany(Habit::class)->where('is_active', true);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order', 'asc');
    }

    public function scopeForUser(Builder $query, ?int $userId = null): Builder
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    // Methods
    public function canBeDeleted(): bool
    {
        return $this->activeHabits()->count() === 0;
    }

    public function getHabitsCount(): int
    {
        return $this->habits()->count();
    }
}
