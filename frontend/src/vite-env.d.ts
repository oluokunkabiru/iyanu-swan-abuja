/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_APP_NAME: string
  readonly VITE_SITE_TAGLINE: string
  readonly VITE_CONTACT_EMAIL: string
  readonly VITE_CONTACT_PHONE: string
  readonly VITE_CONTACT_ADDRESS: string

  readonly VITE_BRAND_PURPLE_900: string
  readonly VITE_BRAND_PURPLE_800: string
  readonly VITE_BRAND_PURPLE_700: string
  readonly VITE_BRAND_PURPLE_500: string
  readonly VITE_BRAND_PURPLE_200: string
  readonly VITE_BRAND_PURPLE_050: string

  readonly VITE_BRAND_GOLD_700: string
  readonly VITE_BRAND_GOLD_500: string
  readonly VITE_BRAND_GOLD_300: string
  readonly VITE_BRAND_GOLD_050: string

  readonly VITE_BRAND_PAPER: string
  readonly VITE_BRAND_INK: string
  readonly VITE_BRAND_SUCCESS: string
  readonly VITE_BRAND_DANGER: string

  readonly VITE_BRAND_DARK_BG: string
  readonly VITE_BRAND_DARK_SURFACE: string
  readonly VITE_BRAND_DARK_PRIMARY: string

  readonly VITE_BRAND_RADIUS: string

  readonly VITE_ROUTER?: 'hash' | 'history'
}

interface ImportMeta {
  readonly env: ImportMetaEnv
}
