<template>
    <div class="relative h-full w-full overflow-hidden bg-[#030308]">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(59,130,246,0.15),transparent_70%)]" />

        <div class="anim-horse anim-horse-1 absolute bottom-[22%] left-[8%] h-20 w-28">
            <svg viewBox="0 0 120 80" class="h-full w-full" fill="none">
                <path d="M15 55 Q35 20 55 35 T95 30 L105 45 L90 58 L70 52 L45 62 Z" stroke="#3b82f6" stroke-width="2" class="anim-stroke" />
                <circle cx="88" cy="38" r="3" fill="#60a5fa" class="anim-eye" />
            </svg>
        </div>
        <div class="anim-horse anim-horse-2 absolute bottom-[28%] left-[32%] h-16 w-24 opacity-70">
            <svg viewBox="0 0 120 80" class="h-full w-full" fill="none">
                <path d="M15 55 Q35 20 55 35 T95 30 L105 45 L90 58 L70 52 L45 62 Z" stroke="#2563eb" stroke-width="1.5" class="anim-stroke" />
            </svg>
        </div>
        <div class="anim-horse anim-horse-3 absolute bottom-[24%] right-[10%] h-24 w-32">
            <svg viewBox="0 0 120 80" class="h-full w-full" fill="none">
                <path d="M15 55 Q35 20 55 35 T95 30 L105 45 L90 58 L70 52 L45 62 Z" stroke="#93c5fd" stroke-width="2.5" class="anim-stroke" />
                <circle cx="88" cy="38" r="3" fill="#bfdbfe" class="anim-eye" />
            </svg>
        </div>

        <div
            v-for="i in 12"
            :key="i"
            class="anim-particle absolute h-1 w-1 rounded-full bg-blue-400/80"
            :style="{
                left: `${8 + (i * 7) % 85}%`,
                top: `${15 + (i * 11) % 60}%`,
                animationDelay: `${i * 0.2}s`,
            }"
        />
    </div>
</template>

<style scoped>
.anim-horse {
    animation: horse-gallop 1.4s ease-in-out infinite;
    filter: drop-shadow(0 0 8px rgba(59, 130, 246, 0.6));
}
.anim-horse-1 { animation-delay: 0s; }
.anim-horse-2 { animation-delay: 0.25s; }
.anim-horse-3 { animation-delay: 0.5s; }
.anim-stroke {
    stroke-dasharray: 120;
    animation: stroke-dash 2s ease-in-out infinite;
}
.anim-eye {
    animation: eye-blink 2.5s ease-in-out infinite;
}
.anim-particle {
    animation: particle-rise 2.8s ease-in-out infinite;
}
@keyframes horse-gallop {
    0%, 100% { transform: translateY(0) scaleX(1); }
    25% { transform: translateY(-8px) scaleX(1.02); }
    50% { transform: translateY(0) scaleX(0.98); }
    75% { transform: translateY(-5px) scaleX(1.01); }
}
@keyframes stroke-dash {
    0%, 100% { stroke-dashoffset: 20; opacity: 0.7; }
    50% { stroke-dashoffset: 0; opacity: 1; }
}
@keyframes eye-blink {
    0%, 90%, 100% { opacity: 1; }
    95% { opacity: 0.2; }
}
@keyframes particle-rise {
    0%, 100% { transform: translateY(0); opacity: 0.2; }
    50% { transform: translateY(-14px); opacity: 0.9; }
}
</style>
