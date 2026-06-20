<template>
    <div class="relative h-full w-full overflow-hidden bg-gradient-to-br from-[#1a1030] to-[#080910]">
        <div class="anim-bounce absolute left-[18%] top-[22%] h-20 w-20 rounded-full bg-gradient-to-br from-yellow-300 to-orange-500 shadow-lg shadow-orange-500/40" />
        <div class="anim-bounce absolute right-[20%] top-[30%] h-14 w-14 rounded-full bg-gradient-to-br from-sky-400 to-accent shadow-lg shadow-accent/40" style="animation-delay: 0.4s" />
        <div class="anim-bounce absolute bottom-[22%] left-[42%] h-10 w-16 rounded-full bg-gradient-to-br from-pink-400 to-purple-500" style="animation-delay: 0.8s" />
        <div class="absolute bottom-4 left-4 right-4 flex justify-between">
            <span v-for="i in 3" :key="i" class="h-2 flex-1 rounded-full bg-white/20 anim-wave" :style="{ animationDelay: `${i * 0.3}s` }" />
        </div>
    </div>
</template>

<style scoped>
.anim-bounce {
    animation: char-bounce 2.2s ease-in-out infinite;
}
.anim-wave {
    animation: wave-line 1.6s ease-in-out infinite;
}
@keyframes char-bounce {
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-12px) scale(1.08); }
}
@keyframes wave-line {
    0%, 100% { transform: scaleY(1); opacity: 0.4; }
    50% { transform: scaleY(2.5); opacity: 1; }
}
</style>
