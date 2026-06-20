<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { useVideoLightbox } from '@/composables/useVideoLightbox';

const props = defineProps({
    video: { type: Object, required: true },
    eager: { type: Boolean, default: false },
    compact: { type: Boolean, default: false },
    autoplay: { type: Boolean, default: true },
});

const { open } = useVideoLightbox();

const root = ref(null);
const isVisible = ref(props.eager);
const isPlaying = ref(false);
const videoEl = ref(null);

let observer;

function startPlayback() {
    if (!props.autoplay || !props.video.preview_url && !props.video.video_url) {
        return;
    }

    isPlaying.value = true;
    requestAnimationFrame(() => videoEl.value?.play()?.catch(() => {}));
}

function stopPlayback() {
    isPlaying.value = false;
    videoEl.value?.pause();
}

onMounted(() => {
    if (props.eager) {
        startPlayback();
        return;
    }

    observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                isVisible.value = true;
                startPlayback();
            } else {
                stopPlayback();
            }
        },
        { rootMargin: '100px', threshold: 0.25 },
    );

    if (root.value) observer.observe(root.value);
});

onUnmounted(() => {
    observer?.disconnect();
    stopPlayback();
});

watch(isVisible, (visible) => {
    if (visible && props.eager) {
        startPlayback();
    }
});

function openLightbox() {
    if (props.video.video_url || props.video.preview_url) {
        open(props.video);
    }
}

const playbackSrc = () => props.video.preview_url || props.video.video_url;
</script>

<template>
    <article
        ref="root"
        class="group shrink-0 cursor-pointer overflow-hidden rounded-2xl outline-none"
        :class="compact ? 'h-full border border-border/70' : 'card-dark'"
        tabindex="0"
        @click="openLightbox"
        @keydown.enter="openLightbox"
        @keydown.space.prevent="openLightbox"
    >
        <div class="relative h-full w-full overflow-hidden bg-bg">
            <img
                v-if="!isPlaying"
                :src="video.poster_url"
                :alt="video.title"
                class="h-full w-full object-cover"
                :loading="eager ? 'eager' : 'lazy'"
                decoding="async"
            />
            <video
                v-if="isVisible && playbackSrc()"
                ref="videoEl"
                :src="isPlaying ? playbackSrc() : undefined"
                :poster="video.poster_url"
                class="absolute inset-0 h-full w-full object-cover"
                muted
                loop
                playsinline
                :preload="eager ? 'auto' : 'none'"
            />
            <div class="absolute inset-0 bg-black/0 transition group-hover:bg-black/15" />
            <div
                v-if="video.categories?.length && !compact"
                class="absolute left-3 top-3 flex max-w-[calc(100%-1.5rem)] flex-wrap gap-1"
            >
                <span
                    v-for="category in video.categories.slice(0, 2)"
                    :key="category"
                    class="rounded-full bg-black/60 px-3 py-1 text-xs text-white backdrop-blur"
                >
                    {{ category }}
                </span>
            </div>
        </div>
        <div v-if="!compact" class="p-4">
            <h3 class="font-semibold text-white">{{ video.title }}</h3>
            <p v-if="video.description" class="mt-1 line-clamp-2 text-sm text-text-muted">
                {{ video.description }}
            </p>
        </div>
    </article>
</template>
