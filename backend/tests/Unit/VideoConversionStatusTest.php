<?php

namespace Tests\Unit;

use App\Support\VideoConversionStatus;
use Tests\TestCase;

class VideoConversionStatusTest extends TestCase
{
    public function test_active_statuses(): void
    {
        $this->assertTrue(VideoConversionStatus::isActive(VideoConversionStatus::Queued));
        $this->assertTrue(VideoConversionStatus::isActive(VideoConversionStatus::Processing));
        $this->assertFalse(VideoConversionStatus::isActive(VideoConversionStatus::Completed));
        $this->assertFalse(VideoConversionStatus::isActive(VideoConversionStatus::Failed));
    }
}
