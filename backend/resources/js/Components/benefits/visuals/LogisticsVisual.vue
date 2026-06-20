<template>
    <div class="relative h-full w-full overflow-hidden bg-[#07080f]">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_30%,rgba(37,99,235,0.25),transparent_55%)]" />

        <div class="anim-desk absolute bottom-[18%] left-[12%] h-10 w-24 rounded-md bg-white/10" />
        <div class="anim-person absolute bottom-[28%] left-[18%] h-14 w-5 rounded-full bg-white/30" />

        <div class="anim-screen absolute right-[8%] top-[16%] h-[58%] w-[52%] overflow-hidden rounded-xl border border-accent/30 bg-black/60 shadow-[0_0_40px_rgba(37,99,235,0.2)]">
            <div class="anim-scene absolute inset-0 bg-gradient-to-br from-indigo-900/80 via-sky-800/60 to-emerald-900/50" />
            <div class="anim-float-island absolute left-[10%] top-[20%] h-16 w-24 rounded-full bg-emerald-400/30 blur-[1px]" />
            <div class="anim-float-island absolute right-[15%] top-[35%] h-12 w-20 rounded-full bg-sky-400/25" style="animation-delay: 0.8s" />
            <div class="absolute bottom-3 left-3 right-3 flex gap-1">
                <span v-for="i in 4" :key="i" class="anim-timeline h-1 flex-1 rounded-full bg-accent/70" :style="{ animationDelay: `${i * 0.35}s` }" />
            </div>
        </div>

        <div class="anim-cross absolute left-[8%] top-[14%] flex h-8 w-8 items-center justify-center rounded-full border border-red-400/40 text-xs text-red-300/80">
            ✕
        </div>
        <div class="anim-cross absolute left-[22%] top-[8%] flex h-7 w-7 items-center justify-center rounded-full border border-red-400/30 text-[10px] text-red-300/60" style="animation-delay: 0.5s">
            ✕
        </div>
    </div>
</template>

<style scoped>
.anim-desk {
    animation: desk-glow 3s ease-in-out infinite;
}
.anim-person {
    animation: person-sway 4s ease-in-out infinite;
}
.anim-screen {
    animation: screen-rise 4s ease-in-out infinite;
}
.anim-scene {
    animation: scene-shift 6s ease-in-out infinite;
}
.anim-float-island {
    animation: island-float 5s ease-in-out infinite;
}
.anim-timeline {
    animation: timeline-fill 2.4s ease-in-out infinite;
    transform-origin: left center;
}
.anim-cross {
    animation: cross-fade 3s ease-in-out infinite;
}
@keyframes desk-glow {
    0%, 100% { box-shadow: 0 0 0 rgba(37, 99, 235, 0); }
    50% { box-shadow: 0 0 16px rgba(37, 99, 235, 0.25); }
}
@keyframes person-sway {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-3px); }
}
@keyframes screen-rise {
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-6px) scale(1.02); }
}
@keyframes scene-shift {
    0%, 100% { filter: hue-rotate(0deg) brightness(1); }
    50% { filter: hue-rotate(18deg) brightness(1.15); }
}
@keyframes island-float {
    0%, 100% { transform: translateY(0) translateX(0); opacity: 0.7; }
    50% { transform: translateY(-10px) translateX(4px); opacity: 1; }
}
@keyframes timeline-fill {
    0%, 100% { transform: scaleX(0.35); opacity: 0.45; }
    50% { transform: scaleX(1); opacity: 1; }
}
@keyframes cross-fade {
    0%, 100% { opacity: 0.35; transform: scale(0.9); }
    50% { opacity: 1; transform: scale(1); }
}
</style>
