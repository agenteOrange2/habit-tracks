<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'default_duration',
        'working_hours_start',
        'working_hours_end',
        'auto_sync',
        'default_view',
        'default_reminder',
    ];

    protected $casts = [
        'auto_sync' => 'boolean',
        'default_duration' => 'integer',
        'default_reminder' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getOrCreateForUser(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'default_duration' => 60,
                'working_hours_start' => '09:00',
                'working_hours_end' => '18:00',
                'auto_sync' => false,
                'default_view' => 'month',
                'default_reminder' => 15,
            ]
        );
    }
}
