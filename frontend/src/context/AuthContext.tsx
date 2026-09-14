import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from 'react'
import { changePassword, fetchMe, login, logout, register, updateMe } from '@/api/auth'
import { hasAccessToken, setAccessToken } from '@/api/client'
import type { AuthUser } from '@/types'

interface AuthContextValue {
  user: AuthUser | null
  isLoading: boolean
  signIn: (email: string, password: string) => Promise<AuthUser>
  signUp: (input: Parameters<typeof register>[0]) => Promise<AuthUser>
  signOut: () => void
  updateProfile: (payload: Parameters<typeof updateMe>[0]) => Promise<AuthUser>
  updatePassword: (payload: Parameters<typeof changePassword>[0]) => Promise<AuthUser>
  refreshUser: () => Promise<AuthUser | null>
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined)

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<AuthUser | null>(null)
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    if (!hasAccessToken()) {
      setIsLoading(false)
      return
    }

    fetchMe()
      .then(setUser)
      .catch(() => setUser(null))
      .finally(() => setIsLoading(false))
  }, [])

  const signIn = useCallback(async (email: string, password: string) => {
    const response = await login({ email, password })
    setAccessToken(response.accessToken)
    setUser(response.user)
    return response.user
  }, [])

  const signUp = useCallback(async (input: Parameters<typeof register>[0]) => {
    const response = await register(input)
    setAccessToken(response.accessToken)
    setUser(response.user)
    return response.user
  }, [])

  const signOut = useCallback(() => {
    logout().finally(() => {
      setAccessToken(null)
      setUser(null)
    })
  }, [])

  const updateProfile = useCallback(async (payload: Parameters<typeof updateMe>[0]) => {
    const next = await updateMe(payload)
    setUser(next)
    return next
  }, [])

  const updatePassword = useCallback(async (payload: Parameters<typeof changePassword>[0]) => {
    const response = await changePassword(payload)
    setAccessToken(response.accessToken)
    setUser(response.user)
    return response.user
  }, [])

  const refreshUser = useCallback(async () => {
    try {
      const next = await fetchMe()
      setUser(next)
      return next
    } catch {
      setUser(null)
      return null
    }
  }, [])

  const value = useMemo(
    () => ({ user, isLoading, signIn, signUp, signOut, updateProfile, updatePassword, refreshUser }),
    [user, isLoading, signIn, signUp, signOut, updateProfile, updatePassword, refreshUser],
  )

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

export function useAuth(): AuthContextValue {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuth must be used inside AuthProvider')
  return ctx
}
