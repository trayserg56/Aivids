<script setup>
import { Link } from '@inertiajs/vue3';
import JustifiedVideoStrip from './JustifiedVideoStrip.vue';

defineProps({
    videos: { type: Array, required: true },
    title: { type: String, default: 'Примеры наших роликов' },
    subtitle: { type: String, default: '' },
});
</script>

<template>
    <section id="videos" class="section-block">
        <div class="container-site">
            <div class="mb-8 max-w-3xl sm:mb-12">
                <h2 class="section-title">{{ title }}</h2>
                <p v-if="subtitle" class="mt-3 text-base text-text-muted sm:mt-4 sm:text-lg">{{ subtitle }}</p>
            </div>

            <div class="relative">
                <JustifiedVideoStrip
                    :videos="videos"
                    :target-rows="2"
                    :row-height="280"
                    :gap="12"
                />

                <div
                    class="pointer-events-none absolute inset-x-0 bottom-0 h-44 bg-gradient-to-t from-bg from-30% via-bg/85 to-transparent sm:h-52"
                    aria-hidden="true"
                />

                <div class="absolute inset-x-0 bottom-0 z-10 flex justify-center pb-1 pt-24 sm:pt-28">
                    <Link href="/cases" class="btn-primary pointer-events-auto shadow-lg shadow-black/50">
                        Все кейсы →
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>
