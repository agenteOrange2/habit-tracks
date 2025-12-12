<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Note extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'folder_id',
        'title',
        'content',
        'icon',
        'is_pinned',
        'pinned_at',
    ];

    protected $casts = [
        'content' => 'array',
        'is_pinned' => 'boolean',
        'pinned_at' => 'datetime',
    ];

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(NoteFolder::class, 'folder_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(NoteTag::class, 'note_tag');
    }

    // Scopes

    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }

    public function scopeUnpinned(Builder $query): Builder
    {
        return $query->where('is_pinned', false);
    }

    public function scopeInFolder(Builder $query, ?int $folderId): Builder
    {
        if ($folderId === null) {
            return $query->whereNull('folder_id');
        }
        return $query->where('folder_id', $folderId);
    }

    public function scopeWithTag(Builder $query, int $tagId): Builder
    {
        return $query->whereHas('tags', fn ($q) => $q->where('note_tags.id', $tagId));
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhereRaw("JSON_EXTRACT(content, '$.html') LIKE ?", ["%{$term}%"])
              ->orWhereRaw("JSON_EXTRACT(content, '$.text') LIKE ?", ["%{$term}%"]);
        });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('is_pinned')
                     ->orderByDesc('pinned_at')
                     ->orderByDesc('updated_at');
    }

    // Methods

    public function pin(): void
    {
        $this->update([
            'is_pinned' => true,
            'pinned_at' => now(),
        ]);
    }

    public function unpin(): void
    {
        $this->update([
            'is_pinned' => false,
            'pinned_at' => null,
        ]);
    }

    public function moveToFolder(?int $folderId): void
    {
        $this->update(['folder_id' => $folderId]);
    }

    public function getContentHtmlAttribute(): string
    {
        if (empty($this->content)) {
            return '';
        }

        // Si el contenido tiene formato HTML directo
        if (isset($this->content['html'])) {
            return $this->content['html'];
        }

        // Si tiene formato de texto simple (legacy)
        if (isset($this->content['text'])) {
            return nl2br(e($this->content['text']));
        }

        // Formato antiguo (JSON estructurado)
        return $this->extractTextFromContent($this->content);
    }

    public function getContentTextAttribute(): string
    {
        if (empty($this->content)) {
            return '';
        }

        // Si es HTML, extraer texto plano
        if (isset($this->content['html'])) {
            return strip_tags($this->content['html']);
        }

        // Si tiene formato de texto simple (legacy)
        if (isset($this->content['text'])) {
            return $this->content['text'];
        }

        return $this->extractTextFromContent($this->content);
    }

    protected function extractTextFromContent(array $content): string
    {
        $lines = [];

        if (isset($content['content'])) {
            foreach ($content['content'] as $node) {
                $lines[] = $this->extractTextFromNode($node);
            }
        }

        return implode("\n", $lines);
    }

    protected function extractTextFromNode(array $node): string
    {
        $text = '';

        if (isset($node['text'])) {
            $text .= $node['text'];
        }

        if (isset($node['content'])) {
            foreach ($node['content'] as $child) {
                $text .= $this->extractTextFromNode($child);
            }
        }

        return $text;
    }
}
