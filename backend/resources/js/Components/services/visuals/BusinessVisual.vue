<template>
    <div class="relative h-full w-full overflow-hidden bg-[#080910]">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(37,99,235,0.35),transparent_55%)]" />
        <div class="absolute bottom-4 left-4 right-4 rounded-xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm anim-float">
            <div class="mb-2 flex gap-1">
                <span v-for="i in 5" :key="i" class="anim-bar flex-1 rounded-sm bg-accent/80" :style="{ animationDelay: `${i * 0.15}s` }" />
            </div>
            <div class="h-1.5 w-2/3 rounded-full bg-white/20" />
        </div>
        <div class="absolute right-6 top-6 h-16 w-24 rounded-lg border border-accent/30 bg-accent/10 anim-pulse-soft" />
    </div>
</template>

<style scoped>
.anim-float {
    animation: float-y 4s ease-in-out infinite;
}
.anim-bar {
    height: 4px;
    animation: bar-grow 1.8s ease-in-out infinite;
}
.anim-pulse-soft {
    animation: pulse-soft 3s ease-in-out infinite;
}
@keyframes float-y {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}
@keyframes bar-grow {
    0%, 100% { height: 4px; opacity: 0.5; }
    50% { height: 22px; opacity: 1; }
}
@keyframes pulse-soft {
    0%, 100% { opacity: 0.4; transform: scale(1); }
    50% { opacity: 0.9; transform: scale(1.05); }
}
</style>
