---
name: aivids-design
description: Design system and UI guidelines for the AiVids AI video landing site. Use when creating or editing Vue components, layouts, styling, or visual sections. Reference neurofilms.ru aesthetic — dark cinematic theme, electric blue accents, premium spacing.
---

# AiVids Design System

## Reference

Primary visual reference: [neurofilms.ru](https://neurofilms.ru/) — dark landing for AI video production.

## Tokens (Tailwind `@theme` in `resources/css/app.css`)

| Token | Value | Usage |
|-------|-------|-------|
| `bg` | `#050508` | Page background |
| `bg-elevated` | `#0f1018` | Header/footer |
| `bg-card` | `#14151f` | Cards |
| `border` | `#252636` | Borders |
| `accent` | `#2563eb` | CTAs, highlights |
| `text` | `#f4f4f5` | Headings |
| `text-muted` | `#a1a1aa` | Body secondary |

## Layout

- Max width: `container-site` (max-w-7xl, responsive padding)
- Section vertical rhythm: `py-20 lg:py-28`
- Headings: `section-title` (3xl–5xl, bold)
- Cards: `card-dark` (rounded-2xl, border, bg-card)
- Primary CTA: `btn-primary` (rounded-full, blue)
- Secondary: `btn-outline`

## Landing sections (order)

1. Hero — badge, H1, subtitle, CTA, 2×4 video thumbnail grid
2. Services — horizontal scroll carousel, image + title + description
3. Stats — 4 metric cards, one highlighted with `bg-accent`
4. Video gallery — lazy-loaded grid, hover-to-preview
5. Benefits — numbered cards (01–05)
6. Pricing — 3 tiers, middle "Рекомендуем"
7. News preview — featured + list, link to `/news`
8. FAQ — accordion
9. Footer — logo, nav, contacts

## Performance rules

- Posters: WebP preferred, lazy `loading="lazy"` except first 4 hero/gallery items
- Videos: `preload="none"`, load src only on hover/play
- No autoplay with sound; muted loop on hover only
- Avoid heavy animations; prefer CSS transitions ≤ 500ms

## Typography

- Font: Inter (system fallback)
- Russian copy, sentence case for UI labels
- Keep line length ≤ 65ch for body text

## Do / Don't

- Do: generous whitespace, high contrast, subtle gradients (`radial-gradient` blue glow in hero)
- Don't: light theme, cluttered grids, inline video on page load, stock-photo aesthetic
