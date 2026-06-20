let scriptPromise = null;

export function loadYandexSmartCaptchaScript() {
    if (typeof window === 'undefined') {
        return Promise.resolve();
    }

    if (window.smartCaptcha) {
        return Promise.resolve();
    }

    if (scriptPromise) {
        return scriptPromise;
    }

    scriptPromise = new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = 'https://smartcaptcha.cloud.yandex.ru/captcha.js';
        script.defer = true;
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('Failed to load Yandex SmartCaptcha'));
        document.head.appendChild(script);
    });

    return scriptPromise;
}

export function readSmartCaptchaToken(container) {
    if (!container) {
        return '';
    }

    const input = container.querySelector('input[name="smart-token"]');

    return input?.value?.trim() ?? '';
}
