<?php

namespace App\Models;

use App\Support\VideoConversionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        'conversion_status',
        'conversion_progress',
        'conversion_step',
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

    public function isConverting(): bool
    {
        return VideoConversionStatus::isActive($this->conversion_status);
    }

    public function markConversionQueued(): void
    {
        $this->update([
            'conversion_status' => VideoConversionStatus::Queued,
            'conversion_progress' => 0,
            'conversion_step' => 'В очереди',
        ]);
    }

    public function markConversionProcessing(string $step = 'Подготовка'): void
    {
        $this->update([
            'conversion_status' => VideoConversionStatus::Processing,
            'conversion_progress' => 0,
            'conversion_step' => $step,
        ]);
    }

    public function updateConversionProgress(int $progress, string $step): void
    {
        $this->update([
            'conversion_status' => VideoConversionStatus::Processing,
            'conversion_progress' => max(0, min(100, $progress)),
            'conversion_step' => $step,
        ]);
    }

    public function markConversionCompleted(): void
    {
        $this->update([
            'conversion_status' => VideoConversionStatus::Completed,
            'conversion_progress' => 100,
            'conversion_step' => 'Готово',
        ]);
    }

    public function markConversionFailed(string $message): void
    {
        $this->update([
            'conversion_status' => VideoConversionStatus::Failed,
            'conversion_step' => Str::limit($message, 120),
        ]);
    }
}
