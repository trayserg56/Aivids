<?php

namespace App\Services;

use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class VideoCatalog
{
    /** @return list<array<string, mixed>> */
    public function publishedPayload(
        Request $request,
        ?int $limit = null,
        ?callable $scope = null,
    ): array {
        $query = Video::published()->withMedia();

        if ($scope) {
            $scope($query);
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $this->resolve($query, $request);
    }

    /** @return list<array<string, mixed>> */
    private function resolve(Builder $query, Request $request): array
    {
        $resolved = VideoResource::collection($query->get())->resolve($request);

        return array_values($resolved['data'] ?? $resolved);
    }
}
