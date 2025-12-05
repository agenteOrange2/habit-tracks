<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class FocusMode extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'start_time',
        'end_time',
        'blocked_categories',
        'is_active',
    ];

    protected $casts = [
        'blocked_categories' => 'array',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActiveNow(): bool
    {
        if (!$this->is_active)
        {
            return false;
        }

        $currentTime = now()->format('H:i:s');

        return $currentTime >= $this->start_time
            && $currentTime <= $this->end_time;
    }

    public function blocksCategory(string $category): bool
    {
        return in_array($category, $this->blocked_categories);
    }


    
}
