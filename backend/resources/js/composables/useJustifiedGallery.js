/** @typedef {{ fillLastRow?: boolean, fillAllRows?: boolean, maxUpscale?: number, fillPartialRows?: boolean }} LayoutOptions */

export const PREVIEW_LAYOUT_OPTIONS = {
    fillLastRow: true,
    maxUpscale: 1.12,
};

export const MOBILE_LAYOUT_OPTIONS = {
    fillLastRow: true,
    fillPartialRows: true,
};

export function getLayoutOptions(containerWidth, isPreview = false) {
    if (containerWidth < 640) {
        return MOBILE_LAYOUT_OPTIONS;
    }

    return isPreview ? PREVIEW_LAYOUT_OPTIONS : {};
}

/**
 * Pack videos into justified rows with fixed height and variable width.
 *
 * @param {Array<{width?: number, height?: number}>} videos
 * @param {LayoutOptions} [options]
 * @returns {Array<Array<{video: object, width: number}>>}
 */
export function buildJustifiedRows(
    videos,
    containerWidth,
    rowHeight,
    gap = 12,
    maxItemsPerRow = Infinity,
    options = {},
) {
    if (!videos.length || containerWidth <= 0) {
        return [];
    }

    const availableWidth = Math.floor(containerWidth);
    const rows = [];
    let currentRow = [];
    let currentWidth = 0;

    for (const video of videos) {
        const aspect = aspectRatio(video);
        const naturalWidth = rowHeight * aspect;

        const wouldOverflow =
            currentRow.length > 0 && currentWidth + gap + naturalWidth > availableWidth;
        const hitsMaxItems = currentRow.length >= maxItemsPerRow;

        if (wouldOverflow || hitsMaxItems) {
            rows.push(
                scaleRow(
                    currentRow,
                    availableWidth,
                    rowHeight,
                    gap,
                    resolveAllowUpscale(currentRow, false, options),
                    options,
                ),
            );
            currentRow = [video];
            currentWidth = naturalWidth;
        } else {
            currentRow.push(video);
            currentWidth += naturalWidth + (currentRow.length > 1 ? gap : 0);
        }
    }

    if (currentRow.length) {
        rows.push(
            scaleRow(
                currentRow,
                availableWidth,
                rowHeight,
                gap,
                resolveAllowUpscale(currentRow, true, options),
                options,
            ),
        );
    }

    return rows;
}

/**
 * Pick enough latest videos to fill a fixed number of justified rows without gaps.
 *
 * @returns {Array<object>}
 */
export function selectVideosForFilledRows(
    videos,
    containerWidth,
    rowHeight,
    gap = 12,
    maxItemsPerRow = Infinity,
    targetRows = 2,
    layoutOptions = PREVIEW_LAYOUT_OPTIONS,
) {
    if (!videos.length || containerWidth <= 0) {
        return [];
    }

    let selected = [videos[0]];

    for (let index = 1; index <= videos.length; index += 1) {
        const candidate = videos.slice(0, index);
        const rows = buildJustifiedRows(
            candidate,
            containerWidth,
            rowHeight,
            gap,
            maxItemsPerRow,
            layoutOptions,
        );

        if (rows.length > targetRows) {
            break;
        }

        selected = candidate;

        if (rows.length < targetRows) {
            continue;
        }

        if (rowsAreBalanced(rows, containerWidth, gap, layoutOptions)) {
            break;
        }
    }

    return selected;
}

export function rowContentWidth(row, gap) {
    return row.reduce((sum, cell) => sum + cell.width, 0) + gap * Math.max(0, row.length - 1);
}

export function rowsAreBalanced(rows, containerWidth, gap, layoutOptions = {}, minFillRatio = 0.9) {
    if (layoutOptions.fillPartialRows) {
        return rows.every((row) => row.length <= 2 || rowContentWidth(row, gap) >= containerWidth * minFillRatio);
    }

    return rows.every((row) => {
        const width = rowContentWidth(row, gap);

        return width >= containerWidth * minFillRatio || row.length >= 3;
    });
}

function resolveAllowUpscale(row, isLastRow, options) {
    if (options.fillAllRows) {
        return true;
    }

    if (isLastRow && options.fillLastRow) {
        return true;
    }

    return row.length > 2;
}

function aspectRatio(video) {
    const w = video.width || 16;
    const h = video.height || 9;

    return w / h;
}

function distributeWidths(rawWidths, totalWidth) {
    const widths = rawWidths.map((width) => Math.floor(width));
    let used = widths.reduce((sum, width) => sum + width, 0);
    let remaining = totalWidth - used;

    const order = rawWidths
        .map((width, index) => ({ index, fraction: width - widths[index] }))
        .sort((a, b) => b.fraction - a.fraction);

    for (const { index } of order) {
        if (remaining <= 0) {
            break;
        }

        widths[index]++;
        remaining--;
    }

    while (used > totalWidth && widths.length) {
        const last = widths.length - 1;
        widths[last] = Math.max(0, widths[last] - 1);
        used = widths.reduce((sum, width) => sum + width, 0);
    }

    return widths;
}

function scaleRow(
    videos,
    containerWidth,
    rowHeight,
    gap,
    allowUpscale = true,
    options = {},
) {
    const { maxUpscale = Infinity, fillPartialRows = false } = options;
    const gaps = gap * (videos.length - 1);
    const contentWidth = containerWidth - gaps;
    const naturalTotal = videos.reduce(
        (sum, video) => sum + rowHeight * aspectRatio(video),
        0,
    );

    const shouldFillPartialRow = fillPartialRows && videos.length <= 2;
    const effectiveAllowUpscale = shouldFillPartialRow || allowUpscale;

    let scale = contentWidth / naturalTotal;

    if (effectiveAllowUpscale) {
        if (!shouldFillPartialRow) {
            scale = Math.min(scale, maxUpscale);
        }
    } else {
        scale = Math.min(scale, 1);
    }

    const rawWidths = videos.map((video) => rowHeight * aspectRatio(video) * scale);
    const scaledTotal = naturalTotal * scale;
    const targetWidth = effectiveAllowUpscale
        ? Math.min(contentWidth, Math.floor(scaledTotal))
        : Math.min(contentWidth, Math.floor(scaledTotal));
    const widths = distributeWidths(rawWidths, targetWidth);

    return videos.map((video, index) => ({
        video,
        width: widths[index],
    }));
}
