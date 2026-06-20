import { describe, it, expect } from 'vitest';
import {
    buildJustifiedRows,
    selectVideosForFilledRows,
    PREVIEW_LAYOUT_OPTIONS,
    FULL_LAYOUT_OPTIONS,
    MOBILE_LAYOUT_OPTIONS,
    orderPoolForPreview,
    rowContentWidth,
} from '@/composables/useJustifiedGallery';

describe('buildJustifiedRows', () => {
    const videos = [
        { id: 1, width: 1920, height: 1080 },
        { id: 2, width: 1080, height: 1080 },
        { id: 3, width: 1080, height: 1920 },
    ];

    it('creates rows that fill container width', () => {
        const landscapeVideos = [
            { id: 1, width: 1920, height: 1080 },
            { id: 2, width: 1920, height: 1080 },
            { id: 3, width: 1920, height: 1080 },
        ];
        const rows = buildJustifiedRows(landscapeVideos, 1200, 200, 12);
        const firstRowWidth = rowContentWidth(rows[0], 12);

        expect(rows.length).toBeGreaterThan(0);
        expect(firstRowWidth).toBeGreaterThanOrEqual(1195);
        expect(firstRowWidth).toBeLessThanOrEqual(1200);
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
            expect(rowContentWidth(row, 12)).toBeLessThanOrEqual(359);
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

    it('caps upscale on preview rows instead of stretching to full width', () => {
        const naturalWidth = Math.floor(220 * (1080 / 1920));
        const rows = buildJustifiedRows(
            [{ id: 1, width: 1080, height: 1920 }],
            360,
            220,
            12,
            Infinity,
            PREVIEW_LAYOUT_OPTIONS,
        );

        expect(rows[0][0].width).toBeLessThanOrEqual(Math.ceil(naturalWidth * PREVIEW_LAYOUT_OPTIONS.maxUpscale));
        expect(rows[0][0].width).toBeLessThan(360);
    });

    it('fills mobile rows with one or two portrait videos', () => {
        const portrait = { id: 1, width: 1080, height: 1920 };

        const singleRow = buildJustifiedRows(
            [portrait],
            360,
            220,
            12,
            2,
            MOBILE_LAYOUT_OPTIONS,
        );
        expect(rowContentWidth(singleRow[0], 12)).toBe(360);

        const pairRow = buildJustifiedRows(
            [portrait, { id: 2, width: 1080, height: 1920 }],
            360,
            220,
            12,
            2,
            MOBILE_LAYOUT_OPTIONS,
        );
        expect(rowContentWidth(pairRow[0], 12)).toBe(360);
    });

    it('keeps portrait and landscape videos on separate rows in preview mode', () => {
        const rows = buildJustifiedRows(
            [
                { id: 1, width: 1080, height: 1920 },
                { id: 2, width: 1080, height: 1920 },
                { id: 3, width: 1440, height: 1080 },
            ],
            1200,
            280,
            12,
            Infinity,
            PREVIEW_LAYOUT_OPTIONS,
        );

        expect(rows).toHaveLength(2);
        expect(rows[0].map((cell) => cell.video.id)).toEqual([1, 2]);
        expect(rows[1][0].video.id).toBe(3);
        expect(rows[1][0].width).toBeGreaterThan(rows[0][0].width);
    });

    it('fills full-width gallery rows even when orientations are mixed', () => {
        const rows = buildJustifiedRows(
            [
                { id: 1, width: 1080, height: 1920 },
                { id: 2, width: 1080, height: 1920 },
                { id: 3, width: 1920, height: 1080 },
            ],
            1200,
            220,
            12,
            Infinity,
            FULL_LAYOUT_OPTIONS,
        );

        expect(rowContentWidth(rows[0], 12)).toBeGreaterThanOrEqual(1195);
    });

    it('caps upscale per tile instead of stretching square videos to fill a row', () => {
        const square = { id: 1, width: 1080, height: 1080 };
        const naturalWidth = Math.floor(280 * (1080 / 1080));
        const rows = buildJustifiedRows(
            [square, { id: 2, width: 1080, height: 1080 }, { id: 3, width: 1920, height: 1080 }],
            1200,
            280,
            12,
            Infinity,
            PREVIEW_LAYOUT_OPTIONS,
        );

        for (const cell of rows[0]) {
            if (cell.video.id <= 2) {
                expect(cell.width).toBeLessThanOrEqual(Math.ceil(naturalWidth * PREVIEW_LAYOUT_OPTIONS.maxUpscale));
            }
        }
    });

    it('keeps selecting preview videos until capped rows fill the container', () => {
        const portraits = Array.from({ length: 10 }, (_, index) => ({
            id: index + 1,
            width: 1080,
            height: 1920,
        }));
        const landscapes = [
            { id: 11, width: 1920, height: 1080 },
            { id: 12, width: 1920, height: 1080 },
            { id: 13, width: 1920, height: 1080 },
        ];

        const selected = selectVideosForFilledRows(
            orderPoolForPreview([...portraits, ...landscapes]),
            1200,
            280,
            12,
            Infinity,
            2,
            PREVIEW_LAYOUT_OPTIONS,
        );
        const rows = buildJustifiedRows(
            selected,
            1200,
            280,
            12,
            Infinity,
            PREVIEW_LAYOUT_OPTIONS,
        );

        expect(rows).toHaveLength(2);
        expect(rowContentWidth(rows[0], 12)).toBeGreaterThanOrEqual(1080);
        expect(rowContentWidth(rows[1], 12)).toBeGreaterThanOrEqual(1080);
    });

    it('adds more videos until the second preview row is populated', () => {
        const videos = [
            { id: 1, width: 1080, height: 1920 },
            { id: 2, width: 1080, height: 1920 },
            { id: 3, width: 1080, height: 1920 },
            { id: 4, width: 1080, height: 1920 },
            { id: 5, width: 1080, height: 1920 },
            { id: 6, width: 1920, height: 1080 },
            { id: 7, width: 1920, height: 1080 },
            { id: 8, width: 1920, height: 1080 },
        ];

        const selected = selectVideosForFilledRows(videos, 1200, 280, 12, Infinity, 2);
        const rows = buildJustifiedRows(selected, 1200, 280, 12, Infinity, PREVIEW_LAYOUT_OPTIONS);

        expect(rows).toHaveLength(2);
        expect(selected.length).toBeGreaterThanOrEqual(6);
        expect(rows[1].length).toBeGreaterThanOrEqual(2);
    });
});
