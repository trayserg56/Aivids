<script setup>
import { useContactModal } from '@/composables/useContactModal';

defineProps({
    hero: { type: Object, required: true },
});

const { open: openContact } = useContactModal();
</script>

<template>
    <section class="relative overflow-hidden pt-6 pb-10 sm:pt-8 sm:pb-14 lg:pt-10 lg:pb-20">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top,_rgba(37,99,235,0.15),_transparent_60%)]" />

        <div class="container-site relative text-center">
            <p class="mx-auto inline-flex max-w-full items-center gap-2 rounded-full border border-border bg-bg-card px-3 py-2 text-xs text-text-muted sm:px-4 sm:text-sm">
                <span class="text-accent">✓</span> {{ hero.badge }}
            </p>

            <h1 class="mx-auto mt-6 max-w-5xl text-2xl font-bold leading-[1.15] tracking-tight text-white sm:mt-8 sm:text-4xl lg:text-5xl">
                {{ hero.title }}
            </h1>

            <p class="mx-auto mt-4 max-w-2xl text-base text-text-muted sm:mt-6 sm:text-lg">
                {{ hero.subtitle }}
            </p>

            <button type="button" class="btn-primary mt-10 gap-2" @click="openContact({ section: 'hero' })">
                {{ hero.cta }}
                <span aria-hidden="true">→</span>
            </button>
        </div>
    </section>
</template>
