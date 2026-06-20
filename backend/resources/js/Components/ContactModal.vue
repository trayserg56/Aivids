<script setup>
import { ref, watch } from 'vue';
import ContactForm from './ContactForm.vue';
import { useContactModal } from '@/composables/useContactModal';

const { isOpen, close } = useContactModal();
const formRef = ref(null);

watch(isOpen, (open) => {
    if (!open) {
        formRef.value?.reset();
    }
});

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
            class="fixed inset-0 z-[100] flex items-end justify-center bg-black/80 p-0 backdrop-blur-sm sm:items-center sm:p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="contact-modal-title"
            @click="onBackdropClick"
            @keydown.esc.window="close"
        >
            <div class="relative max-h-[92vh] w-full overflow-y-auto rounded-t-2xl border border-border bg-bg-elevated p-6 shadow-2xl sm:max-w-lg sm:rounded-2xl sm:p-8">
                <button
                    type="button"
                    class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full border border-border text-2xl text-white transition hover:border-accent hover:text-accent"
                    aria-label="Закрыть"
                    @click="close"
                >
                    ×
                </button>

                <h2 id="contact-modal-title" class="pr-10 text-2xl font-bold text-white">
                    Обсудить проект
                </h2>
                <p class="mt-2 text-sm text-text-muted">
                    Расскажите о задаче — подберём формат, сроки и бюджет.
                </p>

                <div class="mt-6">
                    <ContactForm ref="formRef" field-id-prefix="contact-modal" />
                </div>
            </div>
        </div>
    </Teleport>
</template>
