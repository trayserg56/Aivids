const PHONE_ERROR = 'Укажите номер в формате +7 XXX XXX-XX-XX.';

export function formatPhoneInput(value) {
    let digits = (value ?? '').replace(/\D/g, '');

    if (digits.startsWith('8')) {
        digits = `7${digits.slice(1)}`;
    } else if (digits.length > 0 && !digits.startsWith('7')) {
        digits = `7${digits}`;
    }

    digits = digits.slice(0, 11);

    if (digits.length === 0) {
        return '';
    }

    if (digits.length === 1) {
        return '+7';
    }

    let formatted = `+7 (${digits.slice(1, 4)}`;

    if (digits.length >= 4) {
        formatted += `) ${digits.slice(4, 7)}`;
    }

    if (digits.length >= 7) {
        formatted += `-${digits.slice(7, 9)}`;
    }

    if (digits.length >= 9) {
        formatted += `-${digits.slice(9, 11)}`;
    }

    return formatted;
}

export function normalizePhone(value) {
    if (!value?.trim()) {
        return null;
    }

    let digits = value.replace(/\D/g, '');

    if (digits.startsWith('8') && digits.length === 11) {
        digits = `7${digits.slice(1)}`;
    }

    if (digits.length === 10) {
        digits = `7${digits}`;
    }

    if (!/^7\d{10}$/.test(digits)) {
        return null;
    }

    return `+7 (${digits.slice(1, 4)}) ${digits.slice(4, 7)}-${digits.slice(7, 9)}-${digits.slice(9, 11)}`;
}

export function isValidPhone(value) {
    return normalizePhone(value) !== null;
}

export { PHONE_ERROR };
