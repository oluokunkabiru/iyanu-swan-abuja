/**
 * Brand tokens.
 *
 * Every colour the site uses starts life as a `VITE_BRAND_*` variable in
 * `.env`. We read them once at boot and write them onto the document root as
 * CSS custom properties, so components keep referring to `var(--primary)`,
 * `bg-plum-700`, and friends without knowing where the value came from.
 *
 * Vite inlines `import.meta.env` at build time, so editing `.env` needs a dev
 * server restart (or a rebuild) to take effect.
 */

const fallbacks = {
  purple900: '#2C1250',
  purple800: '#3D1A6B',
  purple700: '#5A2D91',
  purple500: '#7C4DBE',
  purple200: '#DDD0EF',
  purple050: '#F4EEFB',
  gold700: '#8F6A17',
  gold500: '#B8892B',
  gold300: '#E5C76B',
  gold050: '#FBF4E2',
  paper: '#FAF8FC',
  ink: '#1B1024',
  success: '#1F6F43',
  danger: '#B3261E',
  darkBg: '#150C1E',
  darkSurface: '#1E1229',
  darkPrimary: '#B58CE6',
  radius: '0.375rem',
} as const

export type BrandToken = keyof typeof fallbacks

function read(key: string, fallback: string): string {
  const raw = import.meta.env[key as keyof ImportMetaEnv]
  return typeof raw === 'string' && raw.trim().length > 0 ? raw.trim() : fallback
}

export const brand: Record<BrandToken, string> = {
  purple900: read('VITE_BRAND_PURPLE_900', fallbacks.purple900),
  purple800: read('VITE_BRAND_PURPLE_800', fallbacks.purple800),
  purple700: read('VITE_BRAND_PURPLE_700', fallbacks.purple700),
  purple500: read('VITE_BRAND_PURPLE_500', fallbacks.purple500),
  purple200: read('VITE_BRAND_PURPLE_200', fallbacks.purple200),
  purple050: read('VITE_BRAND_PURPLE_050', fallbacks.purple050),
  gold700: read('VITE_BRAND_GOLD_700', fallbacks.gold700),
  gold500: read('VITE_BRAND_GOLD_500', fallbacks.gold500),
  gold300: read('VITE_BRAND_GOLD_300', fallbacks.gold300),
  gold050: read('VITE_BRAND_GOLD_050', fallbacks.gold050),
  paper: read('VITE_BRAND_PAPER', fallbacks.paper),
  ink: read('VITE_BRAND_INK', fallbacks.ink),
  success: read('VITE_BRAND_SUCCESS', fallbacks.success),
  danger: read('VITE_BRAND_DANGER', fallbacks.danger),
  darkBg: read('VITE_BRAND_DARK_BG', fallbacks.darkBg),
  darkSurface: read('VITE_BRAND_DARK_SURFACE', fallbacks.darkSurface),
  darkPrimary: read('VITE_BRAND_DARK_PRIMARY', fallbacks.darkPrimary),
  radius: read('VITE_BRAND_RADIUS', fallbacks.radius),
}

/** CSS custom property name for each token. */
const cssVariables: Record<BrandToken, string> = {
  purple900: '--swan-purple-900',
  purple800: '--swan-purple-800',
  purple700: '--swan-purple-700',
  purple500: '--swan-purple-500',
  purple200: '--swan-purple-200',
  purple050: '--swan-purple-050',
  gold700: '--swan-gold-700',
  gold500: '--swan-gold-500',
  gold300: '--swan-gold-300',
  gold050: '--swan-gold-050',
  paper: '--swan-paper',
  ink: '--swan-ink',
  success: '--swan-success',
  danger: '--swan-danger',
  darkBg: '--swan-dark-bg',
  darkSurface: '--swan-dark-surface',
  darkPrimary: '--swan-dark-primary',
  radius: '--radius',
}

/**
 * Push the tokens onto `<html>`. `index.css` declares the same variables with
 * identical defaults, so there is no unstyled flash if this never runs.
 */
export function applyBrand(root: HTMLElement = document.documentElement): void {
  for (const key of Object.keys(cssVariables) as BrandToken[]) {
    root.style.setProperty(cssVariables[key], brand[key])
  }
}
