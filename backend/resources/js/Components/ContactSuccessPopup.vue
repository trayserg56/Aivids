<script setup>
import { useContactSuccess } from '@/composables/useContactSuccess';

const { isOpen, close } = useContactSuccess();

function onBackdropClick(event) {
    if (event.target === event.currentTarget) {
        close();
    }
}
</script>

<template>
    <Teleport to="body">
        <div
            v-if="isOpen"
            class="fixed inset-0 z-[110] flex items-center justify-center bg-black/70 p-4 backdrop-blur-sm"
            role="alertdialog"
            aria-modal="true"
            aria-labelledby="contact-success-title"
            @click="onBackdropClick"
            @keydown.esc.window="close"
        >
            <div
                class="relative w-full max-w-md rounded-2xl border border-green-500/30 bg-bg-elevated p-8 shadow-2xl"
                @click.stop
            >
                <button
                    type="button"
                    class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full border border-border text-2xl text-white transition hover:border-accent hover:text-accent"
                    aria-label="Закрыть"
                    @click="close"
                >
                    ×
                </button>

                <div class="pr-10">
                    <div
                        class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-500/15 text-2xl text-green-400"
                        aria-hidden="true"
                    >
                        ✓
                    </div>
                    <h2 id="contact-success-title" class="text-xl font-bold text-white">
                        Заявка отправлена!
                    </h2>
                    <p class="mt-2 text-sm leading-relaxed text-text-muted">
                        Мы свяжемся с вами в ближайшее время.
                    </p>
                </div>
            </div>
        </div>
    </Teleport>
</template>
