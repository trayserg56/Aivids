<script setup>
import { useVideoLightbox } from '@/composables/useVideoLightbox';

const { activeVideo, close } = useVideoLightbox();

function onBackdropClick(event) {
    if (event.target === event.currentTarget) {
        close();
    }
}
</script>

<template>
    <Teleport to="body">
        <div
            v-if="activeVideo"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 p-4 backdrop-blur-sm"
            role="dialog"
            aria-modal="true"
            :aria-label="activeVideo.title"
            @click="onBackdropClick"
            @keydown.esc.window="close"
        >
            <button
                class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-black/50 text-2xl text-white transition hover:border-accent hover:text-accent"
                aria-label="Закрыть"
                @click="close"
            >
                ×
            </button>

            <div class="w-full max-w-5xl">
                <video
                    :key="activeVideo.id"
                    :src="activeVideo.video_url || activeVideo.preview_url"
                    :poster="activeVideo.poster_url"
                    class="max-h-[80vh] w-full rounded-2xl bg-black shadow-2xl"
                    controls
                    autoplay
                    playsinline
                />
                <div class="mt-4 text-center">
                    <h3 class="text-xl font-semibold text-white">{{ activeVideo.title }}</h3>
                    <p v-if="activeVideo.categories?.length" class="mt-1 text-sm text-text-muted">
                        {{ activeVideo.categories.join(' · ') }}
                    </p>
                </div>
            </div>
        </div>
    </Teleport>
</template>
