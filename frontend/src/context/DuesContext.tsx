import { createContext, useCallback, useContext, useEffect, useState, type ReactNode } from 'react'
import { useLocation } from 'react-router-dom'
import { fetchDuesQuote } from '@/api/auth'
import { useAuth } from '@/context/AuthContext'
import { useApiData } from '@/hooks/useApiData'
import type { MembershipDuesQuote } from '@/types'

interface DuesContextValue {
  quote: MembershipDuesQuote | null
  isLoading: boolean
  refresh: () => void
}

const DuesContext = createContext<DuesContextValue | undefined>(undefined)

export function DuesProvider({ children }: { children: ReactNode }) {
  const { user } = useAuth()
  const { pathname } = useLocation()
  const [revision, setRevision] = useState(0)
  const refresh = useCallback(() => setRevision(value => value + 1), [])
  const year = new Date().getFullYear()
  const { data: quote, isLoading } = useApiData(
    () => fetchDuesQuote(year),
    null as MembershipDuesQuote | null,
    [user?.id, user?.dateOfBirth, user?.credential, year, pathname, revision],
  )

  useEffect(() => {
    window.addEventListener('focus', refresh)
    return () => window.removeEventListener('focus', refresh)
  }, [refresh])

  return <DuesContext.Provider value={{ quote, isLoading, refresh }}>{children}</DuesContext.Provider>
}

export function useDues() {
  const value = useContext(DuesContext)
  if (!value) throw new Error('useDues must be used inside DuesProvider')
  return value
}
