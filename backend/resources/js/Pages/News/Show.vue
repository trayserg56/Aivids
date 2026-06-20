<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

defineProps({
    post: Object,
    related: Array,
});
</script>

<template>
    <AppLayout>
        <article class="pt-8 pb-16 lg:pt-10 lg:pb-20">
            <div class="container-site max-w-3xl">
                <Breadcrumbs
                    :items="[
                        { label: 'Главная', href: '/' },
                        { label: 'Новости', href: '/news' },
                        { label: post.title },
                    ]"
                />

                <p class="mt-4 text-sm text-accent">
                    {{ post.category }} · {{ post.published_at }} · {{ post.reading_time }} мин чтения
                </p>

                <h1 class="mt-4 text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-5xl">
                    {{ post.title }}
                </h1>

                <p class="mt-6 text-xl text-text-muted">{{ post.excerpt }}</p>

                <div v-if="post.image_url" class="mt-10 overflow-hidden rounded-2xl">
                    <img :src="post.image_url" :alt="post.title" class="w-full object-cover" />
                </div>

                <div class="prose prose-invert mt-10 max-w-none text-text-muted" v-html="post.body" />
            </div>

            <aside v-if="related.length" class="container-site mt-16 max-w-3xl border-t border-border pt-10">
                <h2 class="text-xl font-semibold text-white">Читайте также</h2>
                <ul class="mt-6 space-y-4">
                    <li v-for="item in related" :key="item.slug">
                        <Link :href="`/news/${item.slug}`" class="text-accent hover:underline">
                            {{ item.title }}
                        </Link>
                    </li>
                </ul>
            </aside>
        </article>
    </AppLayout>
</template>
