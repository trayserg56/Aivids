<?php

namespace App\Support;

final class VideoConversionStatus
{
    public const Idle = 'idle';

    public const Queued = 'queued';

    public const Processing = 'processing';

    public const Completed = 'completed';

    public const Failed = 'failed';

    public static function isActive(?string $status): bool
    {
        return in_array($status, [self::Queued, self::Processing], true);
    }
}
