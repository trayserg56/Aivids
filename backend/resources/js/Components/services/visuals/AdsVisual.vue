<template>
    <div class="relative h-full w-full overflow-hidden bg-[#0a0a0f]">
        <div class="anim-card absolute left-[8%] top-[18%] h-32 w-24 rotate-[-12deg] rounded-xl border border-white/10 bg-gradient-to-br from-amber-200/90 to-orange-300/80 shadow-xl" />
        <div class="anim-card absolute left-[32%] top-[12%] h-36 w-28 rotate-[6deg] rounded-xl border border-white/10 bg-gradient-to-br from-sky-200/90 to-blue-300/80 shadow-xl" style="animation-delay: 0.3s" />
        <div class="anim-card absolute right-[10%] top-[20%] h-[7.5rem] w-[6.5rem] rotate-[14deg] rounded-xl border border-white/10 bg-gradient-to-br from-emerald-200/90 to-teal-300/80 shadow-xl" style="animation-delay: 0.6s" />
        <div class="anim-shine absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent" />
        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 rounded-full bg-accent px-4 py-1.5 text-xs font-semibold text-white anim-cta">
            AI AD
        </div>
    </div>
</template>

<style scoped>
.anim-card {
    animation: card-float 3.5s ease-in-out infinite;
}
.anim-shine {
    animation: shine-sweep 3s ease-in-out infinite;
}
.anim-cta {
    animation: cta-pulse 2s ease-in-out infinite;
}
@keyframes card-float {
    0%, 100% { transform: translateY(0) rotate(var(--r, -12deg)); }
    50% { transform: translateY(-8px) rotate(calc(var(--r, -12deg) + 3deg)); }
}
@keyframes shine-sweep {
    0% { transform: translateX(-120%) skewX(-15deg); }
    60%, 100% { transform: translateX(120%) skewX(-15deg); }
}
@keyframes cta-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4); }
    50% { box-shadow: 0 0 20px 4px rgba(37, 99, 235, 0.5); }
}
</style>
