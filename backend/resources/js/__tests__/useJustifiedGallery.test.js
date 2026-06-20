import { describe, it, expect } from 'vitest';
import { buildJustifiedRows, selectVideosForFilledRows } from '@/composables/useJustifiedGallery';

describe('buildJustifiedRows', () => {
    const videos = [
        { id: 1, width: 1920, height: 1080 },
        { id: 2, width: 1080, height: 1080 },
        { id: 3, width: 1080, height: 1920 },
    ];

    it('creates rows that fill container width', () => {
        const rows = buildJustifiedRows(videos, 1200, 200, 12);
        const firstRowWidth = rows[0].reduce((sum, cell) => sum + cell.width, 0) + 12 * (rows[0].length - 1);

        expect(rows.length).toBeGreaterThan(0);
        expect(firstRowWidth).toBeCloseTo(1200, 0);
    });

    it('uses 16:9 fallback when dimensions missing', () => {
        const rows = buildJustifiedRows([{ id: 1 }], 800, 180, 8);

        expect(rows[0][0].width).toBe(Math.floor(180 * (16 / 9)));
    });

    it('wraps to the next row when max items per row is reached', () => {
        const portrait = { id: 1, width: 1080, height: 1920 };
        const rows = buildJustifiedRows(
            [portrait, portrait, portrait],
            360,
            220,
            12,
            2,
        );

        expect(rows).toHaveLength(2);
        expect(rows[0]).toHaveLength(2);
        expect(rows[1]).toHaveLength(1);
    });

    it('never exceeds container width after rounding', () => {
        const rows = buildJustifiedRows(videos, 359, 220, 12, 3);

        for (const row of rows) {
            const total = row.reduce((sum, cell) => sum + cell.width, 0) + 12 * (row.length - 1);

            expect(total).toBeLessThanOrEqual(359);
        }
    });

    it('keeps natural width for a single-item row', () => {
        const rows = buildJustifiedRows(
            [{ id: 1, width: 1080, height: 1920 }],
            360,
            220,
            12,
        );

        expect(rows[0][0].width).toBe(Math.floor(220 * (1080 / 1920)));
    });

    it('fills the last row when fillLastRow is enabled', () => {
        const rows = buildJustifiedRows(
            [{ id: 1, width: 1080, height: 1920 }],
            360,
            220,
            12,
            Infinity,
            { fillLastRow: true },
        );

        expect(rows[0][0].width).toBe(360);
    });

    it('adds more videos until the second preview row is populated', () => {
        const videos = [
            { id: 1, width: 1080, height: 1920 },
            { id: 2, width: 1080, height: 1920 },
            { id: 3, width: 1080, height: 1920 },
            { id: 4, width: 1080, height: 1920 },
            { id: 5, width: 1080, height: 1920 },
            { id: 6, width: 1920, height: 1080 },
            { id: 7, width: 1080, height: 1920 },
            { id: 8, width: 1080, height: 1920 },
        ];

        const selected = selectVideosForFilledRows(videos, 1200, 280, 12, Infinity, 2);
        const rows = buildJustifiedRows(selected, 1200, 280, 12, Infinity, { fillLastRow: true });

        expect(rows).toHaveLength(2);
        expect(selected.length).toBeGreaterThan(6);

        const secondRowWidth =
            rows[1].reduce((sum, cell) => sum + cell.width, 0) + 12 * (rows[1].length - 1);

        expect(secondRowWidth).toBe(1200);
    });
});
