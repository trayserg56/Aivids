<?php

namespace Tests\Unit;

use App\Services\YandexSmartCaptchaValidator;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class YandexSmartCaptchaValidatorTest extends TestCase
{
    public function test_verify_skipped_when_server_key_not_configured(): void
    {
        config(['captcha.yandex.server_key' => null]);

        $validator = new YandexSmartCaptchaValidator;

        $this->assertFalse($validator->isEnabled());
        $this->assertTrue($validator->verify(null));
    }

    public function test_verify_rejects_empty_token_when_enabled(): void
    {
        config(['captcha.yandex.server_key' => 'ysc2_test']);

        $validator = new YandexSmartCaptchaValidator;

        $this->assertFalse($validator->verify(''));
        $this->assertFalse($validator->verify(null));
    }

    public function test_verify_accepts_ok_response(): void
    {
        config(['captcha.yandex.server_key' => 'ysc2_test']);

        Http::fake([
            'smartcaptcha.cloud.yandex.ru/validate' => Http::response([
                'status' => 'ok',
                'message' => '',
                'host' => 'adsaivideo.ru',
            ]),
        ]);

        $validator = new YandexSmartCaptchaValidator;

        $this->assertTrue($validator->verify('valid-token', '127.0.0.1'));
    }

    public function test_verify_rejects_failed_response(): void
    {
        config(['captcha.yandex.server_key' => 'ysc2_test']);

        Http::fake([
            'smartcaptcha.cloud.yandex.ru/validate' => Http::response([
                'status' => 'failed',
                'message' => '',
            ]),
        ]);

        $validator = new YandexSmartCaptchaValidator;

        $this->assertFalse($validator->verify('bot-token', '127.0.0.1'));
    }
}
