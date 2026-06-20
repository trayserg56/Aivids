let scriptPromise = null;

const ONLOAD_CALLBACK = '__aividsYandexCaptchaOnload';

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
        window[ONLOAD_CALLBACK] = () => {
            resolve();
        };

        const script = document.createElement('script');
        script.src = `https://smartcaptcha.cloud.yandex.ru/captcha.js?render=onload&onload=${ONLOAD_CALLBACK}`;
        script.async = true;
        script.defer = true;
        script.onerror = () => reject(new Error('Failed to load Yandex SmartCaptcha'));
        document.head.appendChild(script);
    });

    return scriptPromise;
}

export function renderYandexSmartCaptcha(container, sitekey, { invisible = false, onSuccess, shieldPosition } = {}) {
    if (!window.smartCaptcha || !container || !sitekey) {
        return null;
    }

    container.replaceChildren();

    const params = {
        sitekey,
        hl: 'ru',
        invisible,
        callback: (token) => onSuccess?.(token),
    };

    if (shieldPosition) {
        params.shieldPosition = shieldPosition;
    }

    return window.smartCaptcha.render(container, params);
}

export function executeYandexSmartCaptcha(widgetId) {
    if (widgetId == null) {
        return;
    }

    window.smartCaptcha?.execute(widgetId);
}

export function resetYandexSmartCaptcha(widgetId) {
    if (widgetId != null) {
        window.smartCaptcha?.reset(widgetId);
    }
}

export function destroyYandexSmartCaptcha(widgetId) {
    if (widgetId != null) {
        window.smartCaptcha?.destroy(widgetId);
    }
}

export function readYandexSmartCaptchaToken(widgetId) {
    if (widgetId == null) {
        return '';
    }

    return window.smartCaptcha?.getResponse(widgetId)?.trim() ?? '';
}

/** @deprecated use readYandexSmartCaptchaToken */
export function readSmartCaptchaToken(container) {
    if (!container) {
        return '';
    }

    const input = container.querySelector('input[name="smart-token"]');

    return input?.value?.trim() ?? '';
}
