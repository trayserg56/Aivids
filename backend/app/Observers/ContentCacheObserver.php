<?php

namespace App\Observers;

use App\Models\Benefit;
use App\Models\Faq;
use App\Models\NewsPost;
use App\Models\PricingPlan;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Stat;
use App\Models\Video;
use Illuminate\Support\Facades\Cache;

class ContentCacheObserver
{
    public function saved(Service|Stat|Video|NewsPost|PricingPlan|Faq|Benefit|SiteSetting $model): void
    {
        Cache::forget('home.page');
    }

    public function deleted(Service|Stat|Video|NewsPost|PricingPlan|Faq|Benefit|SiteSetting $model): void
    {
        Cache::forget('home.page');
    }
}
