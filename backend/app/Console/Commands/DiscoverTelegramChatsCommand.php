<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DiscoverTelegramChatsCommand extends Command
{
    protected $signature = 'telegram:discover-chats';

    protected $description = 'List Telegram chat IDs from recent bot updates (send a message in the group first)';

    public function handle(): int
    {
        $token = config('telegram.bot_token');

        if (! $token) {
            $this->error('TELEGRAM_BOT_TOKEN is not set in .env');

            return self::FAILURE;
        }

        $response = Http::timeout(10)->get("https://api.telegram.org/bot{$token}/getUpdates");

        if (! $response->successful() || ! $response->json('ok')) {
            $this->error('Telegram API error: '.$response->body());

            return self::FAILURE;
        }

        $updates = $response->json('result', []);

        if ($updates === []) {
            $this->warn('No updates yet. In the Telegram group:');
            $this->line('  1. Make sure the bot is a member (admin is best)');
            $this->line('  2. Send any message, e.g. /start or «тест»');
            $this->line('  3. Run this command again');
            $this->newLine();
            $this->line('If the bot has Privacy Mode on, mention it: @YourBotName тест');

            return self::SUCCESS;
        }

        $chats = [];

        foreach ($updates as $update) {
            $message = $update['message'] ?? $update['channel_post'] ?? null;

            if ($message === null) {
                continue;
            }

            $chat = $message['chat'];
            $chatId = (string) $chat['id'];
            $title = $chat['title'] ?? trim(($chat['first_name'] ?? '').' '.($chat['last_name'] ?? ''));
            $type = $chat['type'] ?? 'unknown';

            $chats[$chatId] = [
                'id' => $chatId,
                'type' => $type,
                'title' => $title !== '' ? $title : $chatId,
            ];
        }

        if ($chats === []) {
            $this->warn('Updates received, but no chats found.');

            return self::SUCCESS;
        }

        $this->info('Found chats — copy the group id into TELEGRAM_CHAT_ID:');
        $this->newLine();

        foreach ($chats as $chat) {
            $this->line(sprintf(
                '  %s  [%s]  %s',
                $chat['id'],
                $chat['type'],
                $chat['title'],
            ));
        }

        return self::SUCCESS;
    }
}
