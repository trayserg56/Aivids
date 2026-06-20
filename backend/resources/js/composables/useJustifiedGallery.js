/** @typedef {{ fillLastRow?: boolean, fillAllRows?: boolean, maxUpscale?: number, fillPartialRows?: boolean }} LayoutOptions */

export const PREVIEW_LAYOUT_OPTIONS = {
    fillLastRow: true,
    maxUpscale: 1.12,
};

export const MOBILE_LAYOUT_OPTIONS = {
    fillLastRow: true,
    fillPartialRows: true,
};

export const DEFAULT_LAYOUT_OPTIONS = {
    maxUpscale: 1.12,
};

export function getLayoutOptions(containerWidth, isPreview = false) {
    if (containerWidth < 640) {
        return MOBILE_LAYOUT_OPTIONS;
    }

    return isPreview ? PREVIEW_LAYOUT_OPTIONS : DEFAULT_LAYOUT_OPTIONS;
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
        const naturalWidth = cellNaturalWidth(video, rowHeight);

        const wouldOverflow =
            currentRow.length > 0 && currentWidth + gap + naturalWidth > availableWidth;
        const hitsMaxItems = currentRow.length >= maxItemsPerRow;
        const aspectSplit = shouldSplitRowForAspect(currentRow, video);

        if (wouldOverflow || hitsMaxItems || aspectSplit) {
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
        let candidate = videos.slice(0, index);
        candidate = augmentSelection(
            videos,
            candidate,
            containerWidth,
            rowHeight,
            gap,
            maxItemsPerRow,
            targetRows,
            layoutOptions,
        );

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

function augmentSelection(
    allVideos,
    selected,
    containerWidth,
    rowHeight,
    gap,
    maxItemsPerRow,
    targetRows,
    layoutOptions,
) {
    let current = [...selected];
    let rows = buildJustifiedRows(
        current,
        containerWidth,
        rowHeight,
        gap,
        maxItemsPerRow,
        layoutOptions,
    );

    if (rows.length > targetRows || rowsAreBalanced(rows, containerWidth, gap, layoutOptions)) {
        return current;
    }

    const selectedIds = new Set(current.map((video) => video.id));
    const remaining = allVideos.filter((video) => !selectedIds.has(video.id));
    const lastRowVideos = rows[rows.length - 1]?.map((cell) => cell.video) ?? [];
    const preferredCategory = lastRowVideos.length
        ? aspectCategory(lastRowVideos[0])
        : null;

    for (const video of remaining) {
        if (
            preferredCategory
            && aspectCategory(video) !== preferredCategory
            && shouldSplitRowForAspect(lastRowVideos, video)
        ) {
            continue;
        }

        const candidate = [...current, video];
        const candidateRows = buildJustifiedRows(
            candidate,
            containerWidth,
            rowHeight,
            gap,
            maxItemsPerRow,
            layoutOptions,
        );

        if (candidateRows.length > targetRows) {
            continue;
        }

        return augmentSelection(
            allVideos,
            candidate,
            containerWidth,
            rowHeight,
            gap,
            maxItemsPerRow,
            targetRows,
            layoutOptions,
        );
    }

    return current;
}

export function rowContentWidth(row, gap) {
    return row.reduce((sum, cell) => sum + cell.width, 0) + gap * Math.max(0, row.length - 1);
}

export function aspectCategory(video) {
    const ratio = aspectRatio(video);

    if (ratio < 0.85) {
        return 'portrait';
    }

    if (ratio <= 1.25) {
        return 'square';
    }

    return 'landscape';
}

export function shouldSplitRowForAspect(currentRow, nextVideo) {
    if (!currentRow.length) {
        return false;
    }

    const rowCategory = aspectCategory(currentRow[0]);
    const nextCategory = aspectCategory(nextVideo);

    return rowCategory === 'portrait'
        ? nextCategory !== 'portrait'
        : nextCategory === 'portrait';
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

function cellNaturalWidth(video, rowHeight) {
    return rowHeight * aspectRatio(video);
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

function distributeWithCaps(naturals, caps, targetWidth) {
    const naturalTotal = naturals.reduce((sum, width) => sum + width, 0);

    if (naturalTotal <= 0) {
        return naturals.map(() => 0);
    }

    let scale = targetWidth / naturalTotal;
    let raw = naturals.map((natural, index) => Math.min(natural * scale, caps[index]));
    let total = raw.reduce((sum, width) => sum + width, 0);

    if (total > targetWidth) {
        scale = targetWidth / total;
        raw = raw.map((width) => width * scale);
        total = raw.reduce((sum, width) => sum + width, 0);
    }

    return distributeWidths(raw, Math.min(targetWidth, Math.floor(total)));
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
    const naturals = videos.map((video) => cellNaturalWidth(video, rowHeight));
    const naturalTotal = naturals.reduce((sum, width) => sum + width, 0);

    const shouldFillPartialRow = fillPartialRows && videos.length <= 2;
    const effectiveAllowUpscale = shouldFillPartialRow || allowUpscale;

    if (naturalTotal > contentWidth) {
        const scale = contentWidth / naturalTotal;

        return videos.map((video, index) => ({
            video,
            width: distributeWidths(
                naturals.map((natural) => natural * scale),
                contentWidth,
            )[index],
        }));
    }

    if (!effectiveAllowUpscale) {
        const widths = distributeWidths(naturals, Math.min(contentWidth, Math.floor(naturalTotal)));

        return videos.map((video, index) => ({
            video,
            width: widths[index],
        }));
    }

    if (shouldFillPartialRow) {
        const scale = contentWidth / naturalTotal;
        const widths = distributeWidths(
            naturals.map((natural) => natural * scale),
            contentWidth,
        );

        return videos.map((video, index) => ({
            video,
            width: widths[index],
        }));
    }

    const caps = naturals.map((natural) => natural * maxUpscale);
    const maxTotal = caps.reduce((sum, width) => sum + width, 0);
    const targetTotal = Math.min(contentWidth, Math.floor(maxTotal));
    const widths = distributeWithCaps(naturals, caps, targetTotal);

    return videos.map((video, index) => ({
        video,
        width: widths[index],
    }));
}
