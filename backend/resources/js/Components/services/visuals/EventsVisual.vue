<template>
    <div class="relative h-full w-full overflow-hidden bg-[#06070d]">
        <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-accent/20 to-transparent" />
        <div class="absolute left-1/2 top-1/2 h-28 w-44 -translate-x-1/2 -translate-y-1/2 rounded-lg border border-white/15 bg-black/60 anim-screen">
            <div class="absolute inset-1 overflow-hidden rounded-md">
                <div class="anim-scan h-full w-full bg-gradient-to-b from-accent/40 via-purple-500/30 to-transparent" />
            </div>
        </div>
        <div class="anim-beam absolute left-[20%] top-0 h-full w-16 origin-top rotate-[18deg] bg-gradient-to-b from-white/25 to-transparent" />
        <div class="anim-beam absolute right-[25%] top-0 h-full w-12 origin-top -rotate-[12deg] bg-gradient-to-b from-accent/30 to-transparent" style="animation-delay: 0.6s" />
        <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-2">
            <span v-for="i in 4" :key="i" class="h-1 w-6 rounded-full bg-white/30 anim-dot" :style="{ animationDelay: `${i * 0.2}s` }" />
        </div>
    </div>
</template>

<style scoped>
.anim-screen {
    animation: screen-glow 2.5s ease-in-out infinite;
}
.anim-scan {
    animation: scan 2s linear infinite;
}
.anim-beam {
    animation: beam 3s ease-in-out infinite;
}
.anim-dot {
    animation: dot-pulse 1.5s ease-in-out infinite;
}
@keyframes screen-glow {
    0%, 100% { box-shadow: 0 0 20px rgba(37, 99, 235, 0.2); }
    50% { box-shadow: 0 0 40px rgba(37, 99, 235, 0.55); }
}
@keyframes scan {
    0% { transform: translateY(100%); }
    100% { transform: translateY(-100%); }
}
@keyframes beam {
    0%, 100% { opacity: 0.15; }
    50% { opacity: 0.55; }
}
@keyframes dot-pulse {
    0%, 100% { opacity: 0.3; transform: scaleX(1); }
    50% { opacity: 1; transform: scaleX(1.4); }
}
</style>
