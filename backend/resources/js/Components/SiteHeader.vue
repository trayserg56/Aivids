<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useContactModal } from '@/composables/useContactModal';

const mobileOpen = ref(false);
const { open: openContact } = useContactModal();

function openContactForm() {
    mobileOpen.value = false;
    openContact({ section: 'header' });
}

const nav = [
    { label: 'Услуги', href: '/#services' },
    { label: 'Кейсы', href: '/cases' },
    { label: 'Преимущества', href: '/#benefits' },
    { label: 'Стоимость', href: '/#pricing' },
    { label: 'Новости', href: '/news' },
    { label: 'FAQ', href: '/#faq' },
    { label: 'Контакты', href: '/#contact' },
];
</script>

<template>
    <header class="sticky top-0 z-50 border-b border-border/60 bg-bg/90 backdrop-blur-md">
        <div class="container-site flex h-16 items-center justify-between lg:h-20">
            <Link href="/" class="text-lg font-bold tracking-tight text-white">
                Ai<span class="text-accent">Vids</span>
            </Link>

            <nav class="hidden items-center gap-6 lg:flex">
                <a
                    v-for="item in nav"
                    :key="item.label"
                    :href="item.href"
                    class="text-sm text-text-muted transition hover:text-white"
                >
                    {{ item.label }}
                </a>
            </nav>

            <div class="hidden items-center gap-4 lg:flex">
                <a href="tel:+79516786346" class="text-sm text-text-muted hover:text-white">
                    +7 951 678 6346
                </a>
                <button type="button" class="btn-primary" @click="openContactForm">Связаться с нами</button>
            </div>

            <button
                class="rounded-lg p-2 text-white lg:hidden"
                aria-label="Меню"
                @click="mobileOpen = !mobileOpen"
            >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <div v-if="mobileOpen" class="border-t border-border bg-bg-elevated px-4 py-4 lg:hidden">
            <a
                v-for="item in nav"
                :key="item.label"
                :href="item.href"
                class="block py-2 text-sm text-text-muted"
                @click="mobileOpen = false"
            >
                {{ item.label }}
            </a>
            <button type="button" class="btn-primary mt-4 w-full" @click="openContactForm">Связаться с нами</button>
        </div>
    </header>
</template>
