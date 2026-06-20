<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import VideoCard from './VideoCard.vue';
import { buildJustifiedRows, selectVideosForFilledRows } from '@/composables/useJustifiedGallery';

const props = defineProps({
    videos: { type: Array, required: true },
    rowHeight: { type: Number, default: 280 },
    gap: { type: Number, default: 12 },
    limit: { type: Number, default: null },
    targetRows: { type: Number, default: null },
    poolLimit: { type: Number, default: 20 },
    class: { type: String, default: '' },
    maxItemsPerRow: { type: Number, default: null },
});

const containerRef = ref(null);
const containerWidth = ref(0);

const effectiveMaxItemsPerRow = computed(() => {
    if (props.maxItemsPerRow) {
        return props.maxItemsPerRow;
    }

    if (containerWidth.value < 640) {
        return 2;
    }

    if (containerWidth.value < 1024) {
        return 3;
    }

    return Infinity;
});

const pool = computed(() => {
    const source = props.poolLimit ? props.videos.slice(0, props.poolLimit) : props.videos;

    if (props.limit) {
        return source.slice(0, props.limit);
    }

    return source;
});

const items = computed(() => {
    if (props.targetRows && containerWidth.value > 0) {
        return selectVideosForFilledRows(
            pool.value,
            containerWidth.value,
            props.rowHeight,
            props.gap,
            effectiveMaxItemsPerRow.value,
            props.targetRows,
        );
    }

    return pool.value;
});

const rows = computed(() =>
    buildJustifiedRows(
        items.value,
        containerWidth.value,
        props.rowHeight,
        props.gap,
        effectiveMaxItemsPerRow.value,
        props.targetRows ? { fillLastRow: true } : {},
    ),
);

let resizeObserver;

function updateWidth() {
    const element = containerRef.value;

    if (!element) {
        return;
    }

    const styles = getComputedStyle(element);
    const padding =
        parseFloat(styles.paddingLeft) + parseFloat(styles.paddingRight);

    containerWidth.value = Math.max(0, element.clientWidth - padding);
}

onMounted(async () => {
    await nextTick();
    updateWidth();
    resizeObserver = new ResizeObserver(updateWidth);

    if (containerRef.value) {
        resizeObserver.observe(containerRef.value);
    }
});

onUnmounted(() => resizeObserver?.disconnect());
</script>

<template>
    <div
        v-if="videos.length"
        ref="containerRef"
        class="flex min-w-0 max-w-full flex-col"
        :class="props.class"
        :style="{ gap: `${gap}px` }"
    >
        <div
            v-for="(row, rowIndex) in rows"
            :key="rowIndex"
            class="flex min-w-0 max-w-full"
            :style="{ gap: `${gap}px`, height: `${rowHeight}px` }"
        >
            <VideoCard
                v-for="cell in row"
                :key="cell.video.id"
                :video="cell.video"
                :eager="rowIndex === 0"
                compact
                class="box-border max-w-full shrink-0"
                :style="{ width: `${cell.width}px`, maxWidth: `${cell.width}px` }"
            />
        </div>
    </div>
</template>
