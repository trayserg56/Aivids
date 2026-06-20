<template>
    <div class="relative h-full w-full overflow-hidden bg-[#111118]">
        <div class="anim-stack-wrap absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-[45%]">
            <div class="anim-stack h-36 w-28 rotate-[-8deg] rounded-2xl border border-white/20 bg-white shadow-lg">
                <div class="flex h-full flex-col items-center justify-center gap-1 p-2">
                    <div class="anim-silhouette h-8 w-8 rounded-full bg-black/80" />
                    <div class="anim-silhouette h-10 w-6 rounded-t-full bg-black/80" style="animation-delay: 0.2s" />
                </div>
            </div>
        </div>
        <div
            class="anim-stack-wrap absolute left-1/2 top-1/2 -translate-x-[35%] -translate-y-[38%]"
            style="animation-delay: 0.2s"
        >
            <div class="anim-stack h-36 w-28 rotate-[4deg] rounded-2xl border border-white/20 bg-white/95 shadow-lg">
                <div class="flex h-full items-end justify-center pb-4">
                    <div class="flex gap-1">
                        <span
                            v-for="i in 3"
                            :key="i"
                            class="anim-eq w-1 rounded-full bg-black/70"
                            :style="{ animationDelay: `${i * 0.15}s` }"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div
            class="anim-stack-wrap absolute left-1/2 top-1/2 -translate-x-[65%] -translate-y-[32%]"
            style="animation-delay: 0.4s"
        >
            <div class="anim-stack h-36 w-28 rotate-[12deg] rounded-2xl border border-white/20 bg-white shadow-lg">
                <div class="anim-vinyl absolute inset-3 rounded-xl bg-gradient-to-br from-accent/30 to-purple-500/40" />
            </div>
        </div>
    </div>
</template>

<style scoped>
.anim-stack-wrap {
    animation: stack-drift 4s ease-in-out infinite;
}
.anim-silhouette {
    animation: sway 2s ease-in-out infinite;
}
.anim-eq {
    height: 8px;
    animation: eq 0.8s ease-in-out infinite;
}
.anim-vinyl {
    animation: spin-slow 6s linear infinite;
}
@keyframes stack-drift {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-4px); }
}
@keyframes sway {
    0%, 100% { transform: rotate(-3deg); }
    50% { transform: rotate(3deg); }
}
@keyframes eq {
    0%, 100% { height: 8px; }
    50% { height: 24px; }
}
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
