/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_APP_NAME: string
  readonly VITE_SITE_TAGLINE: string
  readonly VITE_CONTACT_EMAIL: string
  readonly VITE_CONTACT_PHONE: string
  readonly VITE_CONTACT_ADDRESS: string
  readonly VITE_API_URL: string

  readonly VITE_PRIMARY_COLOR: string
  readonly VITE_SECONDARY_COLOR: string
  readonly VITE_TERTIARY_COLOR: string

  readonly VITE_BRAND_RADIUS: string

  readonly VITE_ROUTER?: 'hash' | 'history'
}

interface ImportMeta {
  readonly env: ImportMetaEnv
}
