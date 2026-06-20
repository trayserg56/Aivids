<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'description',
        'poster_path',
        'width',
        'height',
        'video_path',
        'preview_path',
        'external_url',
        'source_filename',
        'file_size_bytes',
        'categories',
        'sort_order',
        'is_featured',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'categories' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('sort_order');
    }

    public function scopeWithMedia($query)
    {
        return $query->where(function ($query) {
            $query->whereNotNull('video_path')
                ->orWhereNotNull('external_url');
        });
    }

    public function getPosterUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->poster_path);
    }

    public function getPreviewUrlAttribute(): ?string
    {
        if ($this->preview_path) {
            return Storage::disk('public')->url($this->preview_path);
        }

        return $this->video_url;
    }

    public function getVideoUrlAttribute(): ?string
    {
        if ($this->external_url) {
            return $this->external_url;
        }

        if ($this->video_path) {
            return Storage::disk('public')->url($this->video_path);
        }

        return null;
    }
}
