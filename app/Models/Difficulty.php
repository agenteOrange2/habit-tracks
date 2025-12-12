<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Difficulty extends Model
{
    use HasFactory;
    protected $fillable = [
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($difficulty) {
            if (empty($difficulty->slug)) {
                $difficulty->slug = Str::slug($difficulty->name);
            }
        });

        static::updating(function ($difficulty) {
            if ($difficulty->isDirty('name')) {
                $difficulty->slug = Str::slug($difficulty->name);
            }
        });
    }

    // Relationships
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
