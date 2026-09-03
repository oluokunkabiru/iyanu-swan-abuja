import { createContext, useContext, useEffect, useState, type ReactNode } from 'react'
import { getSettings } from '@/api/content'
import type { SiteSettings } from '@/types'

const SettingsContext = createContext<SiteSettings | null>(null)

export function SettingsProvider({ children }: { children: ReactNode }) {
  const [settings, setSettings] = useState<SiteSettings | null>(null)

  useEffect(() => {
    getSettings()
      .then(setSettings)
      .catch(() => setSettings(null))
  }, [])

  return <SettingsContext.Provider value={settings}>{children}</SettingsContext.Provider>
}

export function useSettings(): SiteSettings | null {
  return useContext(SettingsContext)
}
