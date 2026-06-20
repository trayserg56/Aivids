<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    posts: { type: Array, default: () => [] },
});
</script>

<template>
    <section class="section-block">
        <div class="container-site">
            <div class="mb-8 flex flex-col gap-4 sm:mb-10 lg:mb-5">
                <h2 class="section-title max-w-2xl">Новости и статьи об AI-видео</h2>
                <p class="max-w-2xl text-sm leading-relaxed text-text-muted sm:text-base">
                    Разборы проектов, идеи для бизнеса и практические материалы о создании видео с помощью нейросетей
                </p>
                <Link href="/news" class="btn-outline self-start">Все новости →</Link>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <Link
                    v-if="posts[0]"
                    :href="`/news/${posts[0].slug}`"
                    class="card-dark group overflow-hidden lg:row-span-2"
                >
                    <div class="aspect-[16/10] bg-bg-elevated">
                        <img
                            v-if="posts[0].image_url"
                            :src="posts[0].image_url"
                            :alt="posts[0].title"
                            class="h-full w-full object-cover transition group-hover:scale-105"
                            loading="lazy"
                        />
                    </div>
                    <div class="p-5 sm:p-6">
                        <p class="text-sm text-accent">{{ posts[0].category }} · {{ posts[0].published_at }}</p>
                        <h3 class="mt-2 text-xl font-bold text-white group-hover:text-accent sm:text-2xl">{{ posts[0].title }}</h3>
                        <p class="mt-3 text-sm text-text-muted sm:text-base">{{ posts[0].excerpt }}</p>
                    </div>
                </Link>

                <div v-if="posts.length > 1" class="flex flex-col gap-6 lg:row-span-2">
                    <Link
                        v-for="post in posts.slice(1)"
                        :key="post.id"
                        :href="`/news/${post.slug}`"
                        class="card-dark group flex flex-1 flex-col overflow-hidden"
                    >
                        <div class="aspect-[16/10] shrink-0 bg-bg-elevated">
                            <img
                                v-if="post.image_url"
                                :src="post.image_url"
                                :alt="post.title"
                                class="h-full w-full object-cover transition group-hover:scale-105"
                                loading="lazy"
                            />
                        </div>
                        <div class="flex flex-1 flex-col p-5">
                            <p class="text-xs text-accent">{{ post.category }} · {{ post.published_at }}</p>
                            <h3 class="mt-2 font-semibold text-white group-hover:text-accent">{{ post.title }}</h3>
                            <p class="mt-2 line-clamp-2 text-sm text-text-muted">{{ post.excerpt }}</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>
