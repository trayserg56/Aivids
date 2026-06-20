<template>
    <div class="relative h-full w-full overflow-hidden bg-black">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_50%_100%,rgba(37,99,235,0.4),transparent_60%)]" />
        <div class="absolute bottom-0 left-0 right-0 grid grid-cols-8 gap-1 p-3 opacity-80">
            <span
                v-for="i in 16"
                :key="i"
                class="anim-led aspect-square rounded-sm"
                :style="{ animationDelay: `${(i % 8) * 0.1}s`, background: ledColor(i) }"
            />
        </div>
        <div class="anim-laser absolute left-1/2 top-0 h-full w-1 -translate-x-1/2 bg-gradient-to-b from-white/60 via-accent/80 to-transparent" />
        <div class="absolute left-1/2 top-[35%] h-20 w-20 -translate-x-1/2 rounded-full border border-white/20 bg-white/5 anim-ring" />
    </div>
</template>

<script setup>
function ledColor(i) {
    const colors = ['#2563eb', '#7c3aed', '#ec4899', '#06b6d4', '#f59e0b'];
    return colors[i % colors.length];
}
</script>

<style scoped>
.anim-led {
    animation: led-flash 1.8s ease-in-out infinite;
}
.anim-laser {
    animation: laser-sweep 2.5s ease-in-out infinite;
}
.anim-ring {
    animation: ring-pulse 2s ease-in-out infinite;
}
@keyframes led-flash {
    0%, 100% { opacity: 0.25; transform: scale(0.9); }
    50% { opacity: 1; transform: scale(1); }
}
@keyframes laser-sweep {
    0%, 100% { transform: translateX(-50%) rotate(-8deg); opacity: 0.4; }
    50% { transform: translateX(-50%) rotate(8deg); opacity: 0.9; }
}
@keyframes ring-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.3); transform: translateX(-50%) scale(1); }
    50% { box-shadow: 0 0 30px 10px rgba(37, 99, 235, 0.2); transform: translateX(-50%) scale(1.1); }
}
</style>
