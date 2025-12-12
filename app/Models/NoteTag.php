<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NoteTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'color',
    ];

    public const COLORS = [
        '#E9E9E7' => 'gray',
        '#EAE4F2' => 'purple',
        '#FAEBDD' => 'orange',
        '#DDEDEA' => 'green',
        '#DDEBF1' => 'blue',
        '#FBE4E4' => 'red',
        '#FBF3DB' => 'yellow',
        '#F6F3F9' => 'lavender',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notes(): BelongsToMany
    {
        return $this->belongsToMany(Note::class, 'note_tag');
    }

    public function getNotesCountAttribute(): int
    {
        return $this->notes()->count();
    }
}
