<script setup>
import { ref } from 'vue';
import ServiceVisual from './services/ServiceVisual.vue';

defineProps({
    services: { type: Array, required: true },
});

const scrollRef = ref(null);
const hoveredId = ref(null);

function scroll(direction) {
    const el = scrollRef.value;
    if (!el) return;
    el.scrollBy({ left: direction * 380, behavior: 'smooth' });
}
</script>

<template>
    <section id="services" class="section-block">
        <div class="container-site">
            <div class="mb-8 flex flex-col gap-4 sm:mb-10 sm:flex-row sm:items-end sm:justify-between">
                <h2 class="section-title max-w-xl">Услуги ИИ-видеопродакшна</h2>
                <div class="flex shrink-0 gap-2 self-start sm:self-auto">
                    <button
                        type="button"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-border bg-bg-card text-lg text-white transition hover:border-accent hover:text-accent"
                        aria-label="Назад"
                        @click="scroll(-1)"
                    >
                        ←
                    </button>
                    <button
                        type="button"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-border bg-bg-card text-lg text-white transition hover:border-accent hover:text-accent"
                        aria-label="Вперёд"
                        @click="scroll(1)"
                    >
                        →
                    </button>
                </div>
            </div>

            <div class="-mx-4 min-w-0 overflow-hidden sm:-mx-6 lg:mx-0">
                <div
                    ref="scrollRef"
                    class="flex gap-4 overflow-x-auto px-4 pb-2 sm:gap-5 sm:px-6 lg:px-0 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                >
                <article
                    v-for="service in services"
                    :key="service.id"
                    class="h-[24rem] w-[min(300px,78vw)] shrink-0 snap-start sm:h-[26.5rem] sm:w-[360px]"
                    @mouseenter="hoveredId = service.id"
                    @mouseleave="hoveredId = null"
                >
                    <div class="flex h-full flex-col overflow-hidden rounded-2xl border border-border/80 bg-bg-card">
                        <div class="aspect-[4/3] shrink-0 overflow-hidden">
                            <ServiceVisual :slug="service.slug" :active="hoveredId === service.id" />
                        </div>
                        <div class="flex min-h-0 flex-1 flex-col p-5">
                            <h3 class="shrink-0 text-lg font-semibold leading-snug text-white">
                                {{ service.title }}
                            </h3>
                            <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-text-muted">
                                {{ service.description }}
                            </p>
                        </div>
                    </div>
                </article>
                </div>
            </div>
        </div>
    </section>
</template>
