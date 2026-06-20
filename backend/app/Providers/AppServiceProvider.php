<?php

namespace App\Providers;

use App\Models\Benefit;
use App\Models\Faq;
use App\Models\NewsPost;
use App\Models\PricingPlan;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Stat;
use App\Models\Video;
use App\Observers\ContentCacheObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $observer = ContentCacheObserver::class;

        Service::observe($observer);
        Stat::observe($observer);
        Video::observe($observer);
        NewsPost::observe($observer);
        PricingPlan::observe($observer);
        Faq::observe($observer);
        Benefit::observe($observer);
        SiteSetting::observe($observer);
    }
}
