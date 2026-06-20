<template>
    <div class="relative h-full w-full overflow-hidden bg-gradient-to-br from-[#120a1f] to-[#07080f]">
        <div class="anim-scene absolute inset-3 overflow-hidden rounded-lg border border-white/10">
            <div
                v-for="(variant, i) in variants"
                :key="i"
                class="anim-variant absolute inset-0"
                :class="variant.class"
                :style="{ animationDelay: `${i * 1.2}s` }"
            />
            <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/80 to-transparent" />
        </div>

        <div class="absolute bottom-3 left-3 right-3 space-y-2 rounded-lg border border-white/10 bg-black/50 p-2 backdrop-blur-sm">
            <div class="flex items-center gap-2">
                <span class="text-[9px] text-white/60">Стиль</span>
                <div class="anim-slider h-1.5 flex-1 rounded-full bg-white/10">
                    <div class="anim-slider-fill h-full rounded-full bg-accent" />
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[9px] text-white/60">Свет</span>
                <div class="anim-slider h-1.5 flex-1 rounded-full bg-white/10" style="animation-delay: 0.4s">
                    <div class="anim-slider-fill h-full rounded-full bg-sky-400" style="animation-delay: 0.4s" />
                </div>
            </div>
            <div class="flex gap-1">
                <span
                    v-for="i in 4"
                    :key="i"
                    class="anim-chip h-4 flex-1 rounded bg-white/10"
                    :style="{ animationDelay: `${i * 0.25}s` }"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
const variants = [
    { class: 'bg-gradient-to-br from-orange-900/70 via-rose-800/50 to-purple-900/60' },
    { class: 'bg-gradient-to-br from-sky-900/70 via-blue-800/50 to-indigo-900/60' },
    { class: 'bg-gradient-to-br from-emerald-900/70 via-teal-800/50 to-cyan-900/60' },
];
</script>

<style scoped>
.anim-variant {
    animation: variant-fade 3.6s ease-in-out infinite;
    opacity: 0;
}
.anim-slider-fill {
    width: 35%;
    animation: slider-move 2.8s ease-in-out infinite;
    transform-origin: left center;
}
.anim-chip {
    animation: chip-active 2.8s ease-in-out infinite;
}
@keyframes variant-fade {
    0%, 100% { opacity: 0; transform: scale(1.05); }
    10%, 30% { opacity: 1; transform: scale(1); }
    40% { opacity: 0; transform: scale(0.98); }
}
@keyframes slider-move {
    0%, 100% { width: 25%; opacity: 0.6; }
    50% { width: 78%; opacity: 1; }
}
@keyframes chip-active {
    0%, 100% { background-color: rgba(255, 255, 255, 0.08); }
    50% { background-color: rgba(37, 99, 235, 0.55); }
}
</style>
