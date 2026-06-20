import { describe, expect, it } from 'vitest';
import { formatPhoneInput, isValidPhone, normalizePhone } from '@/utils/phone';

describe('phone utils', () => {
    it('accepts +7 format', () => {
        expect(isValidPhone('+79991234567')).toBe(true);
        expect(normalizePhone('+79991234567')).toBe('+7 (999) 123-45-67');
    });

    it('accepts 8 prefix and formats to +7', () => {
        expect(isValidPhone('8 (999) 123-45-67')).toBe(true);
        expect(normalizePhone('8 (999) 123-45-67')).toBe('+7 (999) 123-45-67');
    });

    it('rejects too short numbers', () => {
        expect(isValidPhone('12345')).toBe(false);
    });

    it('limits input mask to 11 digits', () => {
        expect(formatPhoneInput('333333333333333')).toBe('+7 (333) 333-33-33');
    });

    it('formats digits while typing', () => {
        expect(formatPhoneInput('9991234567')).toBe('+7 (999) 123-45-67');
    });
});
