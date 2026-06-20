<script setup>
import SiteHeader from '@/Components/SiteHeader.vue';
import SiteFooter from '@/Components/SiteFooter.vue';
import VideoLightbox from '@/Components/VideoLightbox.vue';
import ContactModal from '@/Components/ContactModal.vue';
</script>

<template>
    <div class="min-h-screen overflow-x-clip bg-bg">
        <SiteHeader />
        <main>
            <slot />
        </main>
        <SiteFooter />
        <VideoLightbox />
        <ContactModal />
    </div>
</template>
