<script setup>
import { ref } from 'vue';

defineProps({
    section: { type: Object, required: true },
    faqs: { type: Array, required: true },
});

const openId = ref(null);

function toggle(id) {
    openId.value = openId.value === id ? null : id;
}
</script>

<template>
    <section id="faq" class="section-block">
        <div class="container-site max-w-3xl">
            <h2 class="section-title mb-8 text-center sm:mb-12 lg:mb-6">{{ section.title }}</h2>

            <div class="space-y-3">
                <article
                    v-for="(faq, index) in faqs"
                    :key="faq.id"
                    class="card-dark overflow-hidden"
                >
                    <button
                        class="flex w-full items-center justify-between gap-4 p-5 text-left"
                        @click="toggle(faq.id)"
                    >
                        <span class="flex gap-4">
                            <span class="text-accent">{{ index + 1 }}.</span>
                            <span class="font-semibold text-white">{{ faq.question }}</span>
                        </span>
                        <span class="text-text-muted">{{ openId === faq.id ? '−' : '+' }}</span>
                    </button>
                    <div v-show="openId === faq.id" class="border-t border-border px-5 pb-5 pt-2 text-sm leading-relaxed text-text-muted">
                        {{ faq.answer }}
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>
