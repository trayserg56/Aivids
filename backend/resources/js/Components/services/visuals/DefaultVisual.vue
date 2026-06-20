<template>
    <div class="relative h-full w-full overflow-hidden bg-[#0a0b12]">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(37,99,235,0.25),transparent_65%)]" />
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="h-16 w-16 rounded-full border border-accent/50 anim-ring" />
        </div>
    </div>
</template>

<style scoped>
.anim-ring {
    animation: ring 2.5s ease-in-out infinite;
}
@keyframes ring {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.2); opacity: 1; }
}
</style>
