<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

defineProps({
    posts: Object,
});
</script>

<template>
    <AppLayout>
        <section class="pt-8 pb-16 lg:pt-10 lg:pb-20">
            <div class="container-site">
                <Breadcrumbs
                    :items="[
                        { label: 'Главная', href: '/' },
                        { label: 'Новости' },
                    ]"
                />
                <h1 class="section-title mt-4">Новости и статьи</h1>
                <p class="mt-4 max-w-2xl text-lg text-text-muted">
                    Разборы проектов, идеи для бизнеса и практические материалы о создании видео с помощью нейросетей
                </p>

                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="post in posts.data"
                        :key="post.id"
                        :href="`/news/${post.slug}`"
                        class="card-dark group overflow-hidden"
                    >
                        <div class="aspect-[16/10] bg-bg-elevated">
                            <img
                                v-if="post.image_url"
                                :src="post.image_url"
                                :alt="post.title"
                                class="h-full w-full object-cover transition group-hover:scale-105"
                                loading="lazy"
                            />
                        </div>
                        <div class="p-5">
                            <p class="text-xs text-accent">{{ post.category }} · {{ post.published_at }}</p>
                            <h2 class="mt-2 text-lg font-semibold text-white group-hover:text-accent">{{ post.title }}</h2>
                            <p class="mt-2 line-clamp-3 text-sm text-text-muted">{{ post.excerpt }}</p>
                        </div>
                    </Link>
                </div>

                <div v-if="posts.links?.length > 3" class="mt-10 flex flex-wrap justify-center gap-2">
                    <Link
                        v-for="link in posts.links"
                        :key="link.label"
                        :href="link.url"
                        class="rounded-lg px-4 py-2 text-sm"
                        :class="link.active ? 'bg-accent text-white' : 'border border-border text-text-muted hover:text-white'"
                        v-html="link.label"
                    />
                </div>
            </div>
        </section>
    </AppLayout>
</template>
