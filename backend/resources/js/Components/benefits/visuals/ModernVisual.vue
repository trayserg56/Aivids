<template>
    <div class="relative h-full w-full overflow-hidden bg-[#050508]">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_40%,rgba(37,99,235,0.2),transparent_60%)]" />

        <div class="anim-portal absolute left-1/2 top-[18%] h-[55%] w-[42%] -translate-x-1/2">
            <div class="absolute inset-0 rounded-lg border border-accent/40 bg-gradient-to-b from-accent/20 to-transparent" />
            <div class="anim-grid absolute inset-2 opacity-40">
                <div v-for="i in 16" :key="i" class="border border-accent/20" />
            </div>
            <div class="anim-figure absolute bottom-[12%] left-1/2 h-16 w-8 -translate-x-1/2 rounded-t-full bg-gradient-to-b from-white/50 to-white/10" />
        </div>

        <div class="anim-scan absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-accent to-transparent" />

        <div class="absolute bottom-4 left-4 right-4 flex justify-between text-[9px] font-medium uppercase tracking-wider text-accent/80">
            <span class="anim-label">AI</span>
            <span class="anim-label" style="animation-delay: 0.5s">4K</span>
            <span class="anim-label" style="animation-delay: 1s">2026</span>
        </div>
    </div>
</template>

<style scoped>
.anim-portal {
    animation: portal-float 4s ease-in-out infinite;
    box-shadow: 0 0 40px rgba(37, 99, 235, 0.15);
}
.anim-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    grid-template-rows: repeat(4, 1fr);
    animation: grid-pulse 3s ease-in-out infinite;
}
.anim-figure {
    animation: figure-glow 3s ease-in-out infinite;
}
.anim-scan {
    animation: scan-line 2.5s linear infinite;
}
.anim-label {
    animation: label-flicker 2s ease-in-out infinite;
}
@keyframes portal-float {
    0%, 100% { transform: translateX(-50%) translateY(0); }
    50% { transform: translateX(-50%) translateY(-8px); }
}
@keyframes grid-pulse {
    0%, 100% { opacity: 0.25; }
    50% { opacity: 0.55; }
}
@keyframes figure-glow {
    0%, 100% { box-shadow: 0 0 12px rgba(255, 255, 255, 0.1); }
    50% { box-shadow: 0 0 24px rgba(37, 99, 235, 0.35); }
}
@keyframes scan-line {
    0% { top: 0; opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { top: 100%; opacity: 0; }
}
@keyframes label-flicker {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}
</style>
