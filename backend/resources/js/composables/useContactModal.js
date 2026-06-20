import { ref, readonly } from 'vue';

const isOpen = ref(false);
const context = ref({
    section: null,
    label: null,
});

/**
 * @param {{ section?: string|null, label?: string|null }} [options]
 */
export function useContactModal() {
    function open(options = {}) {
        context.value = {
            section: options.section ?? null,
            label: options.label ?? null,
        };
        isOpen.value = true;
        document.body.style.overflow = 'hidden';
    }

    function close() {
        isOpen.value = false;
        document.body.style.overflow = '';
        context.value = { section: null, label: null };
    }

    return {
        isOpen: readonly(isOpen),
        context: readonly(context),
        open,
        close,
    };
}
