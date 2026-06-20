import { ref, readonly } from 'vue';

const activeVideo = ref(null);

export function useVideoLightbox() {
    function open(video) {
        activeVideo.value = video;
        document.body.style.overflow = 'hidden';
    }

    function close() {
        activeVideo.value = null;
        document.body.style.overflow = '';
    }

    return {
        activeVideo: readonly(activeVideo),
        open,
        close,
    };
}
