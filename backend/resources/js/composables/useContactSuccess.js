import { ref, readonly } from 'vue';

const AUTO_CLOSE_MS = 5000;

const isOpen = ref(false);
let autoCloseTimer = null;

function clearAutoCloseTimer() {
    if (autoCloseTimer !== null) {
        clearTimeout(autoCloseTimer);
        autoCloseTimer = null;
    }
}

export function useContactSuccess() {
    function show() {
        clearAutoCloseTimer();
        isOpen.value = true;
        autoCloseTimer = setTimeout(close, AUTO_CLOSE_MS);
    }

    function close() {
        clearAutoCloseTimer();
        isOpen.value = false;
    }

    return {
        isOpen: readonly(isOpen),
        show,
        close,
    };
}
