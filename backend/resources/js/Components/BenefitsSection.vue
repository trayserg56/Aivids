<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import BenefitVisual from './benefits/BenefitVisual.vue';

defineProps({
    section: { type: Object, required: true },
    benefits: { type: Array, required: true },
});

const sectionRef = ref(null);
const activeIds = ref(new Set());
let observer;

function activate(id) {
    if (!activeIds.value.has(id)) {
        activeIds.value = new Set([...activeIds.value, id]);
    }
}

onMounted(async () => {
    await nextTick();

    const blocks = sectionRef.value?.querySelectorAll('[data-benefit-id]') ?? [];

    observer = new IntersectionObserver(
        (entries) => {
            const next = new Set(activeIds.value);

            entries.forEach((entry) => {
                const id = Number(entry.target.dataset.benefitId);

                if (entry.isIntersecting) {
                    next.add(id);
                } else {
                    next.delete(id);
                }
            });

            activeIds.value = next;
        },
        { threshold: 0.35, rootMargin: '-10% 0px -10% 0px' },
    );

    blocks.forEach((block) => observer.observe(block));
});

onBeforeUnmount(() => {
    observer?.disconnect();
});
</script>

<template>
    <section id="benefits" ref="sectionRef" class="section-block">
        <div class="container-site">
            <div class="lg:grid lg:grid-cols-[minmax(0,20rem)_minmax(0,1fr)] lg:gap-12 xl:grid-cols-[minmax(0,24rem)_minmax(0,1fr)] xl:gap-20">
                <div class="mb-10 min-w-0 max-w-full lg:sticky lg:top-28 lg:mb-0 lg:self-start lg:pr-2">
                    <h2 class="max-w-full text-2xl font-bold tracking-tight break-words text-white sm:text-3xl xl:text-4xl">
                        {{ section.title }}
                    </h2>
                    <p class="mt-6 max-w-full text-base leading-relaxed break-words text-text-muted lg:text-lg">
                        {{ section.subtitle }}
                    </p>
                </div>

                <div class="min-w-0 divide-y divide-border/50">
                    <article
                        v-for="(item, index) in benefits"
                        :key="item.id"
                        :data-benefit-id="item.id"
                        class="py-12 first:pt-0 last:pb-0 lg:py-8"
                    >
                        <h3 class="flex items-start gap-3 text-xl font-semibold leading-snug text-white lg:text-2xl">
                            <span class="mt-2.5 h-2 w-2 shrink-0 rounded-full bg-accent" aria-hidden="true" />
                            {{ item.title }}
                        </h3>

                        <div class="mt-6 flex flex-col gap-6 lg:mt-8 lg:flex-row lg:items-start lg:gap-8">
                            <p class="flex-1 text-sm leading-relaxed text-text-muted lg:text-base lg:leading-7">
                                {{ item.text }}
                            </p>

                            <div
                                class="benefit-visual-shell w-full shrink-0 overflow-hidden rounded-2xl border border-border/80 bg-bg-card lg:w-[min(100%,420px)]"
                                @mouseenter="activate(item.id)"
                            >
                                <div class="aspect-[16/10] w-full">
                                    <BenefitVisual
                                        :index="index"
                                        :active="activeIds.has(item.id)"
                                    />
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
</template>
