<script setup>
defineProps({
    section: { type: Object, required: true },
    stats: { type: Array, required: true },
});
</script>

<template>
    <section class="section-block">
        <div class="container-site">
            <h2 class="section-title mb-8 text-center sm:mb-12">{{ section.title }}</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="stat in stats"
                    :key="stat.label"
                    class="flex min-h-[180px] flex-col justify-center rounded-2xl p-8 text-center"
                    :class="stat.is_highlighted ? 'bg-accent text-white' : 'card-dark'"
                >
                    <p class="text-4xl font-bold lg:text-5xl">{{ stat.value }}</p>
                    <p class="mt-3 text-sm leading-relaxed" :class="stat.is_highlighted ? 'text-blue-100' : 'text-text-muted'">
                        {{ stat.label }}
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
