---
name: aivids-vue
description: Vue 3 + Inertia.js frontend conventions for AiVids. Use when building pages, components, composables, or frontend tests in resources/js/.
---

# AiVids Vue Frontend

## Stack

- Vue 3 (Composition API, `<script setup>`)
- Inertia.js v2 with `@inertiajs/vue3`
- Tailwind CSS v4 via `@import 'tailwindcss'`
- Vite 8, alias `@` → `resources/js`

## Structure

```
resources/js/
├── app.js              # Inertia bootstrap
├── Layouts/AppLayout.vue
├── Pages/
│   ├── Home.vue
│   └── News/Index.vue, Show.vue
└── Components/         # Reusable sections
```

## Conventions

- Pages receive props from Laravel controllers; no client-side data fetching on landing
- Use `Link` from `@inertiajs/vue3` for internal navigation; `<a href="#...">` for anchor sections
- Section components: one file per landing block (HeroSection, VideoGallery, etc.)
- Props: define with `defineProps`, typed objects/arrays

## Video performance pattern

Use `VideoCard.vue` pattern:
- Intersection Observer with `rootMargin: '200px'`
- `eager` prop for above-the-fold items (first 4)
- Poster always shown; `<video>` src set only when playing
- `preload="none"`, `muted`, `playsinline`, `loop`

## Adding a page

1. Create `resources/js/Pages/MyPage.vue`
2. Add route + controller returning `Inertia::render('MyPage', [...])`
3. Wrap content in `AppLayout` unless standalone

## Tests

- Vitest + `@vue/test-utils` in `resources/js/__tests__/`
- Run: `npm test` inside backend or `docker compose --profile dev run --rm node npm test`

## Build

- Dev: `docker compose --profile dev up node` → http://localhost:5175
- Prod: `docker exec aivids_app npm run build`
