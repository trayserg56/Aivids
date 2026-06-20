<script setup>
import { useContactModal } from '@/composables/useContactModal';

defineProps({
    section: { type: Object, required: true },
    plans: { type: Array, required: true },
});

const { open: openContact } = useContactModal();
</script>

<template>
    <section id="pricing" class="section-block">
        <div class="container-site">
            <h2 class="section-title mb-8 text-center sm:mb-12">{{ section.title }}</h2>

            <div class="grid gap-6 lg:grid-cols-3">
                <article
                    v-for="plan in plans"
                    :key="plan.id"
                    class="card-dark relative flex flex-col p-8"
                    :class="plan.is_recommended ? 'border-accent ring-1 ring-accent/30' : ''"
                >
                    <span
                        v-if="plan.is_recommended"
                        class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-accent px-4 py-1 text-xs font-semibold text-white"
                    >
                        Рекомендуем
                    </span>

                    <h3 class="text-xl font-semibold text-white">{{ plan.name }}</h3>
                    <p class="mt-2 text-sm text-text-muted">{{ plan.description }}</p>
                    <p class="mt-6 text-3xl font-bold text-white">{{ plan.price_label }}</p>

                    <ul class="mt-8 flex-1 space-y-3">
                        <li
                            v-for="feature in plan.features"
                            :key="feature"
                            class="flex gap-2 text-sm text-text-muted"
                        >
                            <span class="text-accent">✓</span> {{ feature }}
                        </li>
                    </ul>

                    <button
                        type="button"
                        class="btn-primary mt-8 w-full"
                        @click="openContact({ section: 'pricing', label: `${plan.name} (${plan.price_label})` })"
                    >
                        Обсудить проект
                    </button>
                </article>
            </div>

            <p class="mt-8 text-center text-sm text-text-muted">
                {{ section.footer }}
            </p>
        </div>
    </section>
</template>
