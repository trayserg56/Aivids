<script setup>
import ContactForm from './ContactForm.vue';
</script>

<template>
    <section id="contact" class="section-block border-t border-border bg-bg-elevated">
        <div class="container-site">
            <div class="grid gap-12 lg:grid-cols-2 lg:items-start">
                <div>
                    <h2 class="section-title">Обсудить проект</h2>
                    <p class="mt-4 max-w-lg text-lg text-text-muted">
                        Расскажите о задаче — подберём формат, сроки и бюджет. Ответим в рабочее время.
                    </p>
                    <ul class="mt-8 space-y-3 text-sm text-text-muted">
                        <li><a href="tel:+79516786346" class="hover:text-white">+7 951 678 6346</a></li>
                        <li><a href="mailto:info@aivids.ru" class="hover:text-white">info@aivids.ru</a></li>
                    </ul>
                </div>

                <div class="card-dark p-6 lg:p-8">
                    <ContactForm field-id-prefix="contact-section" source-section="contact" />
                </div>
            </div>
        </div>
    </section>
</template>
