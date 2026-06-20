import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import VideoCard from '@/Components/VideoCard.vue';

describe('VideoCard', () => {
    const video = {
        id: 1,
        title: 'Test Video',
        description: 'Description',
        poster_url: '/storage/posters/test.webp',
        preview_url: '/storage/videos/test-preview.mp4',
        video_url: '/storage/videos/test.mp4',
        categories: ['Реклама'],
        width: 1920,
        height: 1080,
    };

    it('renders video title in full mode', () => {
        const wrapper = mount(VideoCard, {
            props: { video, eager: true },
        });

        expect(wrapper.text()).toContain('Test Video');
    });

    it('hides title in compact mode', () => {
        const wrapper = mount(VideoCard, {
            props: { video, eager: true, compact: true },
        });

        expect(wrapper.text()).not.toContain('Test Video');
    });

    it('shows poster image before playback starts', () => {
        const wrapper = mount(VideoCard, {
            props: { video, autoplay: false },
        });

        expect(wrapper.find('img').attributes('src')).toBe(video.poster_url);
    });
});
