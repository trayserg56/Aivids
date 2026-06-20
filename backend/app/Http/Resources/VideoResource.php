<?php

namespace App\Http\Resources;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Video */
class VideoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'categories' => $this->categories ?? [],
            'width' => $this->width,
            'height' => $this->height,
            'poster_url' => $this->poster_url,
            'preview_url' => $this->preview_url,
            'video_url' => $this->video_url,
            'is_featured' => $this->is_featured,
        ];
    }
}
