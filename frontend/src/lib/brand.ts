/**
 * Brand tokens.
 *
 * Every colour the site uses starts life as one of three variables in
 * `.env`. We read them once at boot and write them onto the document root as
 * CSS custom properties, so components keep referring to `var(--primary)`,
 * `bg-plum-700`, and friends without knowing where the value came from.
 *
 * Vite inlines `import.meta.env` at build time, so editing `.env` needs a dev
 * server restart (or a rebuild) to take effect.
 */

const fallbacks = {
  primary: '#000066',
  secondary: '#FFFF00',
  tertiary: '#FFFFFF',
  radius: '0.375rem',
} as const

export type BrandToken = keyof typeof fallbacks

function read(key: string, fallback: string): string {
  const raw = import.meta.env[key as keyof ImportMetaEnv]
  return typeof raw === 'string' && raw.trim().length > 0 ? raw.trim() : fallback
}

export const brand: Record<BrandToken, string> = {
  primary: read('VITE_PRIMARY_COLOR', fallbacks.primary),
  secondary: read('VITE_SECONDARY_COLOR', fallbacks.secondary),
  tertiary: read('VITE_TERTIARY_COLOR', fallbacks.tertiary),
  radius: read('VITE_BRAND_RADIUS', fallbacks.radius),
}

/** CSS custom property name for each token. */
const cssVariables: Record<BrandToken, string> = {
  primary: '--brand-primary',
  secondary: '--brand-secondary',
  tertiary: '--brand-tertiary',
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
