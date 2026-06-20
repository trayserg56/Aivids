<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    items: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <nav aria-label="Хлебные крошки" class="text-sm">
        <ol class="flex flex-wrap items-center gap-y-1">
            <li
                v-for="(item, index) in items"
                :key="index"
                class="flex items-center"
            >
                <span v-if="index > 0" class="mx-2 text-border" aria-hidden="true">/</span>
                <Link
                    v-if="item.href"
                    :href="item.href"
                    class="text-accent transition hover:text-white"
                >
                    {{ item.label }}
                </Link>
                <span v-else class="text-text-muted">{{ item.label }}</span>
            </li>
        </ol>
    </nav>
</template>
