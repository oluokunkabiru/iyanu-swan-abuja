import { createContext, useContext, type ReactNode } from 'react'
import { getSettings } from '@/api/content'
import { useApiData } from '@/hooks/useApiData'
import type { SiteSettings } from '@/types'

interface SettingsContextValue {
  settings: SiteSettings | null
  isLoading: boolean
}

const SettingsContext = createContext<SettingsContextValue>({ settings: null, isLoading: true })

export function SettingsProvider({ children }: { children: ReactNode }) {
  const { data: settings, isLoading } = useApiData(getSettings, null as SiteSettings | null)

  return (
    <SettingsContext.Provider value={{ settings, isLoading }}>{children}</SettingsContext.Provider>
  )
}

export function useSettings(): SettingsContextValue {
  return useContext(SettingsContext)
}
