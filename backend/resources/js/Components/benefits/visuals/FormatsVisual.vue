<template>
    <div class="relative h-full w-full overflow-hidden bg-[#080910] p-3">
        <div class="grid h-full grid-cols-2 grid-rows-2 gap-2">
            <div
                v-for="(tone, i) in tones"
                :key="i"
                class="anim-tile relative overflow-hidden rounded-lg border border-white/10"
                :class="tone.bg"
                :style="{ animationDelay: `${i * 0.45}s` }"
            >
                <div class="anim-tile-shine absolute inset-0 opacity-0" />
                <div class="absolute bottom-2 left-2 rounded bg-black/40 px-1.5 py-0.5 text-[9px] font-medium text-white/80">
                    {{ tone.label }}
                </div>
            </div>
        </div>
        <div class="anim-connector pointer-events-none absolute inset-0 flex items-center justify-center">
            <div class="h-8 w-8 rounded-full border-2 border-accent/60 bg-accent/20" />
        </div>
    </div>
</template>

<script setup>
const tones = [
    { bg: 'bg-gradient-to-br from-sky-600/70 to-indigo-900/80', label: 'Сайт' },
    { bg: 'bg-gradient-to-br from-fuchsia-600/60 to-purple-900/80', label: 'Reels' },
    { bg: 'bg-gradient-to-br from-amber-500/50 to-orange-900/80', label: 'Ads' },
    { bg: 'bg-gradient-to-br from-emerald-500/50 to-teal-900/80', label: 'Event' },
];
</script>

<style scoped>
.anim-tile {
    animation: tile-cycle 3.2s ease-in-out infinite;
}
.anim-tile-shine {
    background: linear-gradient(120deg, transparent 30%, rgba(255, 255, 255, 0.25) 50%, transparent 70%);
    animation: tile-shine 3.2s ease-in-out infinite;
}
.anim-connector {
    animation: connector-spin 8s linear infinite;
}
@keyframes tile-cycle {
    0%, 100% { transform: scale(0.96); opacity: 0.65; box-shadow: none; }
    25% { transform: scale(1); opacity: 1; box-shadow: 0 0 24px rgba(37, 99, 235, 0.35); }
    50% { transform: scale(0.96); opacity: 0.65; box-shadow: none; }
}
@keyframes tile-shine {
    0%, 40%, 100% { opacity: 0; transform: translateX(-100%); }
    55% { opacity: 1; transform: translateX(100%); }
}
@keyframes connector-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
