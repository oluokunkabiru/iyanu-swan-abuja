# SWAN Abuja Chapter

Front end for the Society of Women Accountants of Nigeria, Abuja Chapter. React 19,
TypeScript, Vite and Tailwind v4. All content is static and lives in the repository —
there is no API to run.

```bash
npm install
npm run dev      # http://localhost:5176
npm run build    # typecheck + production build
npm run lint
```

## Changing the colours

Every colour on the site comes from `.env`. Change a value there and the whole site
follows — you never edit a component to restyle the brand.

```env
VITE_BRAND_PURPLE_700=#5A2D91   # primary
VITE_BRAND_GOLD_500=#B8892B     # accent, rules, marks
VITE_BRAND_PAPER=#FAF8FC        # page background
VITE_BRAND_INK=#1B1024          # body text
VITE_BRAND_RADIUS=0.375rem      # corner radius everywhere
```

**Restart the dev server after editing `.env`.** Vite inlines environment variables at
build time, so a running server will not pick up the change on its own.

How it flows:

1. `.env` holds the raw values. `.env.example` documents every variable.
2. `src/lib/brand.ts` reads them (with hard-coded fallbacks) and `applyBrand()` writes
   each one onto `<html>` as a CSS custom property — `--swan-purple-700`, and so on.
   `src/main.tsx` calls this before the first render.
3. `src/index.css` declares the same variables with identical defaults, so the page
   still paints correctly if the script never runs, then derives every semantic token
   from them: `--primary`, `--background`, `--border`, `--muted`, `--accent`, and the
   rest. Dark mode overrides only the semantic layer, using `color-mix()` against the
   dark surface tokens.
4. Components use semantic classes (`bg-card`, `text-primary`, `border-border`) or the
   brand ramp (`bg-plum-800`, `text-gold-300`) for the always-dark surfaces — the hero,
   the page headers and the footer.

Because `applyBrand()` sets the brand variables as inline styles on `<html>`, a `.dark`
rule cannot override them. That is deliberate: dark mode belongs in the semantic layer.
If you need a surface to change between light and dark, reach for `bg-secondary` or
`bg-accent` rather than `bg-plum-50` or `bg-gold-50`.

Site identity is env-driven too: `VITE_APP_NAME`, `VITE_SITE_TAGLINE`,
`VITE_CONTACT_EMAIL`, `VITE_CONTACT_PHONE`, `VITE_CONTACT_ADDRESS`.

## Content

Everything the site displays is typed static data under `src/data`, exported through a
single barrel. To change copy, edit the data file — not the component.

| File | Holds |
| --- | --- |
| `site.ts` | Settings, vision and mission, core values, statistics, registration steps, member benefits |
| `executives.ts` | The twelve-member chapter council |
| `events.ts` | Events and the four-tier ticket structure |
| `news.ts` | Articles, announcements, forthcoming programme |
| `faqs.ts` | Frequently asked questions by topic |
| `gallery.ts` | Photographs and affiliate bodies |
| `library.ts` | Publications, forms and downloads, committees, training calendar |
| `directory.ts` | Members directory, registered firms, job listings |
| `account.ts` | Demonstration member record, CPD log, dues history, tickets |
| `navigation.ts` | Mega-menu and footer structure |

Types for all of it are in `src/types/index.ts`.

## Authentication

`src/context/AuthContext.tsx` is a stand-in. Any credentials sign you into the
demonstration record in `src/data/account.ts`, and the session is kept in
`localStorage` under `swan-session`. Replace the three methods — `signIn`, `signUp`,
`signOut` — when a real backend exists; nothing else needs to change.

The member rate on events keys off `user.membershipStatus === 'active'`, which is what
`EventDetail` reads to decide which ticket tiers to show.

## Structure

```
src/
  components/
    common/      Primitives (page and section headers, stats, tags) and content cards
    layout/      Header with mega menu, footer, page layout, members-area shell
    sections/    Home-page modules
    ui/          shadcn/radix primitives
  context/       Auth and theme
  data/          All static content
  lib/           brand.ts (env to CSS variables), format.ts, utils.ts
  pages/         Route components, with the members area under pages/members/
  routes/        Router and the protected-route guard
```

## Typography

Newsreader for headings, Public Sans for body and tabular figures, both loaded from
Google Fonts in `index.html`. Numbers in tables and figures use `font-variant-numeric:
tabular-nums` so columns align.
