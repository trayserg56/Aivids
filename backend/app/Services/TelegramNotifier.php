<?php

namespace App\Services;

use App\Models\ContactSubmission;
use App\Support\ContactSources;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotifier
{
    public function isEnabled(): bool
    {
        return filled(config('telegram.bot_token')) && filled(config('telegram.chat_id'));
    }

    public function notifyContactSubmission(ContactSubmission $submission): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        $token = config('telegram.bot_token');
        $chatId = config('telegram.chat_id');

        $response = Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $this->formatContactSubmission($submission),
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ]);

        if (! $response->successful() || ! $response->json('ok')) {
            Log::warning('Telegram notification failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'submission_id' => $submission->id,
            ]);

            return false;
        }

        return true;
    }

    public function formatContactSubmission(ContactSubmission $submission): string
    {
        $lines = [
            '<b>Новая заявка с сайта</b>',
            '',
            '<b>Имя:</b> '.$this->escape($submission->name),
            '<b>Телефон:</b> '.$this->escape($submission->phone),
        ];

        if (filled($submission->email)) {
            $lines[] = '<b>Email:</b> '.$this->escape($submission->email);
        }

        if (filled($submission->message)) {
            $lines[] = '';
            $lines[] = '<b>О проекте:</b>';
            $lines[] = $this->escape($submission->message);
        }

        $source = $this->formatSource($submission);
        if ($source !== null) {
            $lines[] = '';
            $lines[] = '<b>Источник:</b> '.$this->escape($source);
        }

        $lines[] = '';
        $lines[] = '<i>'.$submission->created_at?->timezone(config('app.timezone'))->format('d.m.Y H:i').'</i>';

        return implode("\n", $lines);
    }

    private function formatSource(ContactSubmission $submission): ?string
    {
        if (filled($submission->source_label)) {
            return $submission->source_label;
        }

        $section = ContactSources::sectionLabel($submission->source_section);

        return $section;
    }

    private function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '—', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
