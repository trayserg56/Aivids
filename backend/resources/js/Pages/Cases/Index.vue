<script setup>
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import JustifiedVideoStrip from '@/Components/JustifiedVideoStrip.vue';
import { useContactModal } from '@/composables/useContactModal';

const props = defineProps({
    videos: { type: Array, required: true },
    categories: { type: Array, default: () => [] },
});

const { open: openContact } = useContactModal();
const activeCategory = ref('all');

const filteredVideos = computed(() => {
    if (activeCategory.value === 'all') {
        return props.videos;
    }

    return props.videos.filter((video) =>
        (video.categories ?? []).includes(activeCategory.value),
    );
});

function selectCategory(category) {
    activeCategory.value = category;
}
</script>

<template>
    <AppLayout>
        <section class="pt-8 pb-16 lg:pt-10 lg:pb-20">
            <div class="container-site">
                <Breadcrumbs
                    :items="[
                        { label: 'Главная', href: '/' },
                        { label: 'Кейсы' },
                    ]"
                />
                <h1 class="section-title mt-4">Кейсы</h1>
                <p class="mt-4 max-w-2xl text-lg text-text-muted">
                    Все ролики, созданные нашей командой — реклама, бизнес, мероприятия и шоу-визуал
                </p>

                <div
                    v-if="categories.length"
                    class="mt-8 flex gap-2 overflow-x-auto pb-1 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                    role="tablist"
                    aria-label="Фильтр по категориям"
                >
                    <button
                        type="button"
                        role="tab"
                        :aria-selected="activeCategory === 'all'"
                        class="shrink-0 rounded-full px-5 py-2.5 text-sm font-medium transition"
                        :class="
                            activeCategory === 'all'
                                ? 'bg-accent text-white shadow-lg shadow-accent/25'
                                : 'border border-border bg-bg-card text-text-muted hover:border-accent/50 hover:text-white'
                        "
                        @click="selectCategory('all')"
                    >
                        Все
                    </button>
                    <button
                        v-for="category in categories"
                        :key="category"
                        type="button"
                        role="tab"
                        :aria-selected="activeCategory === category"
                        class="shrink-0 rounded-full px-5 py-2.5 text-sm font-medium transition"
                        :class="
                            activeCategory === category
                                ? 'bg-accent text-white shadow-lg shadow-accent/25'
                                : 'border border-border bg-bg-card text-text-muted hover:border-accent/50 hover:text-white'
                        "
                        @click="selectCategory(category)"
                    >
                        {{ category }}
                    </button>
                </div>

                <div v-if="filteredVideos.length" class="mt-8">
                    <JustifiedVideoStrip :videos="filteredVideos" :row-height="280" :gap="12" />
                </div>
                <p v-else class="mt-10 rounded-2xl border border-border bg-bg-card px-6 py-10 text-center text-text-muted">
                    В этой категории пока нет опубликованных кейсов.
                </p>

                <div class="mt-16 text-center">
                    <button type="button" class="btn-primary" @click="openContact({ section: 'cases' })">Обсудить проект →</button>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
