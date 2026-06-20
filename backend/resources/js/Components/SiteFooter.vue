<script setup>
import { Link } from '@inertiajs/vue3';
import { useContactModal } from '@/composables/useContactModal';

const { open: openContact } = useContactModal();
</script>

<template>
    <footer class="border-t border-border bg-bg-elevated py-16">
        <div class="container-site">
            <div class="flex flex-col gap-10 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-md">
                    <p class="text-2xl font-bold text-white">
                        Ai<span class="text-accent">Vids</span>
                    </p>
                    <p class="mt-4 text-text-muted">
                        ИИ видео под ключ — полный цикл продакшна: идея, генерация, монтаж, озвучка
                    </p>
                    <button type="button" class="btn-primary mt-6" @click="openContact({ section: 'footer' })">Обсудить проект</button>
                </div>

                <div class="grid grid-cols-2 gap-8 sm:grid-cols-3">
                    <div>
                        <p class="mb-3 text-sm font-semibold text-white">Навигация</p>
                        <ul class="space-y-2 text-sm text-text-muted">
                            <li><a href="/#services" class="hover:text-white">Услуги</a></li>
                            <li><Link href="/cases" class="hover:text-white">Кейсы</Link></li>
                            <li><Link href="/news" class="hover:text-white">Новости</Link></li>
                            <li><a href="/#faq" class="hover:text-white">FAQ</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="mb-3 text-sm font-semibold text-white">Контакты</p>
                        <ul class="space-y-2 text-sm text-text-muted">
                            <li><a href="tel:+79516786346" class="hover:text-white">+7 951 678 6346</a></li>
                            <li><a href="mailto:info@aivids.ru" class="hover:text-white">info@aivids.ru</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mt-12 flex flex-col gap-4 border-t border-border pt-8 text-sm text-text-muted sm:flex-row sm:justify-between">
                <p>© {{ new Date().getFullYear() }}. Все права защищены</p>
                <div class="flex gap-4">
                    <a href="#" class="hover:text-white">Политика конфиденциальности</a>
                    <a href="#" class="hover:text-white">Пользовательское соглашение</a>
                </div>
            </div>
        </div>
    </footer>
</template>
