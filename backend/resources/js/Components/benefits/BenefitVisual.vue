<script setup>
import LogisticsVisual from './visuals/LogisticsVisual.vue';
import FormatsVisual from './visuals/FormatsVisual.vue';
import ScaleVisual from './visuals/ScaleVisual.vue';
import EditVisual from './visuals/EditVisual.vue';
import ModernVisual from './visuals/ModernVisual.vue';

defineProps({
    index: { type: Number, required: true },
    active: { type: Boolean, default: false },
});

const visuals = [
    LogisticsVisual,
    FormatsVisual,
    ScaleVisual,
    EditVisual,
    ModernVisual,
];
</script>

<template>
    <div
        class="benefit-anim h-full w-full"
        :class="{ 'benefit-anim--active': active }"
    >
        <component :is="visuals[index % visuals.length]" />
    </div>
</template>
