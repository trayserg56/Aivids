<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import HeroSection from '@/Components/HeroSection.vue';
import JustifiedVideoStrip from '@/Components/JustifiedVideoStrip.vue';
import ServicesSection from '@/Components/ServicesSection.vue';
import StatsSection from '@/Components/StatsSection.vue';
import VideoGallery from '@/Components/VideoGallery.vue';
import BenefitsSection from '@/Components/BenefitsSection.vue';
import PricingSection from '@/Components/PricingSection.vue';
import NewsPreview from '@/Components/NewsPreview.vue';
import FaqSection from '@/Components/FaqSection.vue';
import ContactSection from '@/Components/ContactSection.vue';

defineProps({
    hero: Object,
    services: Array,
    statsSection: Object,
    stats: Array,
    featuredVideos: Array,
    videos: Array,
    benefitsSection: Object,
    benefits: Array,
    pricingSection: Object,
    pricing: Array,
    faqSection: Object,
    faqs: Array,
    latestNews: Array,
});
</script>

<template>
    <AppLayout>
        <HeroSection :hero="hero" />
        <div class="container-site pb-10 sm:pb-14 lg:pb-20">
            <JustifiedVideoStrip
                v-if="(featuredVideos ?? []).length"
                :videos="featuredVideos ?? []"
                :row-height="220"
                :gap="12"
                :limit="8"
            />
        </div>
        <ServicesSection :services="services" />
        <StatsSection :section="statsSection" :stats="stats" />
        <VideoGallery
            :videos="videos ?? []"
            title="ИИ-видео под ключ — масштабный визуал без съёмок"
            subtitle="Примеры роликов, созданных нашей командой"
        />
        <BenefitsSection :section="benefitsSection" :benefits="benefits" />
        <PricingSection :section="pricingSection" :plans="pricing" />
        <NewsPreview :posts="latestNews" />
        <FaqSection :section="faqSection" :faqs="faqs" />
        <ContactSection />
    </AppLayout>
</template>
