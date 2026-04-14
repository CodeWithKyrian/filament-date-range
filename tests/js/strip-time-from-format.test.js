import { describe, it, expect } from 'vitest';

/**
 * Inline copy of the stripTimeFromFormat logic from the Alpine component.
 * Keep in sync with resources/js/components/date-range-picker.js.
 */
function stripTimeFromFormat(format) {
    return format
        .replace(
            /\[([^\]]*)\]|(\s*[,T]?\s*H{1,2}(?::m{1,2})?(?::s{1,2})?\s*[Aa]?)|(\s*[,T]?\s*h{1,2}(?::m{1,2})?(?::s{1,2})?\s*[Aa]?)/g,
            (match, bracketed) => bracketed !== undefined ? `[${bracketed}]` : ''
        )
        .replace(/\s*\[[^a-z\]]*\]\s*$/, '')
        .trim()
        .replace(/[,\s]+$/, '')
        .replace(/^[,\s]+/, '');
}

describe('stripTimeFromFormat', () => {
    it('strips HH:mm from a date-time format', () => {
        expect(stripTimeFromFormat('DD.MM.YYYY HH:mm')).toBe('DD.MM.YYYY');
    });

    it('strips HH:mm:ss from a date-time format', () => {
        expect(stripTimeFromFormat('YYYY-MM-DD HH:mm:ss')).toBe('YYYY-MM-DD');
    });

    it('strips 12-hour h:mm A from a date-time format', () => {
        expect(stripTimeFromFormat('MMM D, YYYY h:mm A')).toBe('MMM D, YYYY');
    });

    it('strips T-separated HH:mm inside literal brackets', () => {
        expect(stripTimeFromFormat('YYYY-MM-DD[T]HH:mm')).toBe('YYYY-MM-DD');
    });

    it('preserves literal text in brackets while stripping time', () => {
        expect(stripTimeFromFormat('DD.MM.YYYY [um] HH:mm')).toBe('DD.MM.YYYY [um]');
    });

    it('is idempotent when no time tokens are present', () => {
        expect(stripTimeFromFormat('DD.MM.YYYY')).toBe('DD.MM.YYYY');
    });
});
