<?php

namespace App\Jobs;

use App\Models\ContactSubmission;
use App\Services\TelegramNotifier;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyContactSubmissionTelegramJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public int $submissionId,
    ) {}

    public function handle(TelegramNotifier $notifier): void
    {
        if (! $notifier->isEnabled()) {
            return;
        }

        $submission = ContactSubmission::query()->find($this->submissionId);

        if ($submission === null) {
            return;
        }

        $notifier->notifyContactSubmission($submission);
    }
}
