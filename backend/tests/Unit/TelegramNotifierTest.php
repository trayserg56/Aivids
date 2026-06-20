<?php

namespace Tests\Unit;

use App\Models\ContactSubmission;
use App\Services\TelegramNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramNotifierTest extends TestCase
{
    use RefreshDatabase;

    public function test_notify_skipped_when_not_configured(): void
    {
        config(['telegram.bot_token' => null, 'telegram.chat_id' => null]);

        Http::fake();

        $submission = ContactSubmission::query()->create([
            'name' => 'Test',
            'phone' => '+7 (999) 123-45-67',
            'message' => '',
        ]);

        $notifier = new TelegramNotifier;

        $this->assertFalse($notifier->isEnabled());
        $this->assertFalse($notifier->notifyContactSubmission($submission));
        Http::assertNothingSent();
    }

    public function test_notify_sends_formatted_message(): void
    {
        config([
            'telegram.bot_token' => 'test-token',
            'telegram.chat_id' => '-100123',
        ]);

        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true, 'result' => []]),
        ]);

        $submission = ContactSubmission::query()->create([
            'name' => 'Иван',
            'phone' => '+7 (999) 123-45-67',
            'email' => 'ivan@example.com',
            'message' => 'Нужен ролик',
            'source_section' => 'hero',
        ]);

        $notifier = new TelegramNotifier;

        $this->assertTrue($notifier->notifyContactSubmission($submission));

        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'sendMessage')
            && $request['chat_id'] === '-100123');
    }

    public function test_format_escapes_html_in_user_input(): void
    {
        $notifier = new TelegramNotifier;

        $submission = new ContactSubmission([
            'name' => '<script>alert(1)</script>',
            'phone' => '+7 (999) 123-45-67',
            'message' => 'Test & demo',
        ]);

        $text = $notifier->formatContactSubmission($submission);

        $this->assertStringContainsString('&lt;script&gt;', $text);
        $this->assertStringContainsString('Test &amp; demo', $text);
    }
}
