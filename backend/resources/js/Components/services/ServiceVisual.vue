<script setup>
import BusinessVisual from './visuals/BusinessVisual.vue';
import EventsVisual from './visuals/EventsVisual.vue';
import AnimationVisual from './visuals/AnimationVisual.vue';
import AdsVisual from './visuals/AdsVisual.vue';
import MusicVisual from './visuals/MusicVisual.vue';
import AvatarsVisual from './visuals/AvatarsVisual.vue';
import StageVisual from './visuals/StageVisual.vue';
import DefaultVisual from './visuals/DefaultVisual.vue';

defineProps({
    slug: { type: String, required: true },
    active: { type: Boolean, default: false },
});
</script>

<template>
    <div
        class="service-anim h-full w-full"
        :class="{ 'service-anim--active': active }"
    >
        <BusinessVisual v-if="slug === 'ii-video-dlia-biznesa'" />
        <EventsVisual v-else-if="slug === 'videokontent-dlia-meropriiatii'" />
        <AnimationVisual v-else-if="slug === 'multfilmy-i-animacionnye-roliki'" />
        <AdsVisual v-else-if="slug === 'reklamnye-roliki'" />
        <MusicVisual v-else-if="slug === 'muzykalnye-klipy'" />
        <AvatarsVisual v-else-if="slug === 'ii-avatary'" />
        <StageVisual v-else-if="slug === 'sceniceskii-vizual'" />
        <DefaultVisual v-else />
    </div>
</template>
