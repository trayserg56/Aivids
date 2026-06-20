<template>
    <div class="relative h-full w-full overflow-hidden bg-[#0c0d14]">
        <div class="anim-frame absolute left-[12%] top-[20%] h-36 w-28 rotate-[-6deg] overflow-hidden rounded-2xl border-2 border-white/15 bg-gradient-to-b from-slate-600 to-slate-800 shadow-xl">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_30%,rgba(255,255,255,0.25),transparent_60%)]" />
            <div class="absolute bottom-0 left-0 right-0 h-1/2 bg-gradient-to-t from-black/50 to-transparent" />
            <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1">
                <span class="h-1 w-1 rounded-full bg-green-400 anim-blink" />
            </div>
        </div>
        <div class="anim-frame absolute right-[10%] top-[16%] h-40 w-[7.5rem] rotate-[8deg] overflow-hidden rounded-2xl border-2 border-accent/40 bg-gradient-to-b from-indigo-500/80 to-slate-900 shadow-xl" style="animation-delay: 0.5s">
            <div class="absolute inset-x-3 top-3 h-16 rounded-xl bg-black/30" />
            <div class="anim-mouth absolute bottom-8 left-1/2 h-2 w-6 -translate-x-1/2 rounded-full bg-white/40" />
        </div>
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-[10px] uppercase tracking-widest text-white/40">
            AI presenter
        </div>
    </div>
</template>

<style scoped>
.anim-frame {
    animation: frame-float 3.8s ease-in-out infinite;
}
.anim-blink {
    animation: blink 2.5s ease-in-out infinite;
}
.anim-mouth {
    animation: talk 1.2s ease-in-out infinite;
}
@keyframes frame-float {
    0%, 100% { transform: translateY(0) rotate(-6deg); }
    50% { transform: translateY(-10px) rotate(-3deg); }
}
@keyframes blink {
    0%, 90%, 100% { opacity: 1; }
    95% { opacity: 0.2; }
}
@keyframes talk {
    0%, 100% { transform: translateX(-50%) scaleX(1); }
    50% { transform: translateX(-50%) scaleX(1.4); }
}
</style>
