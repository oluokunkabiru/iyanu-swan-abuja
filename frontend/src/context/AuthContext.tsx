import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from 'react'
import { fetchMe, login, logout, register } from '@/api/auth'
import type { AuthUser } from '@/types'

interface AuthContextValue {
  user: AuthUser | null
  isLoading: boolean
  signIn: (email: string, password: string) => Promise<AuthUser>
  signUp: (input: { name: string; email: string; password: string; phone?: string }) => Promise<AuthUser>
  signOut: () => void
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined)

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<AuthUser | null>(null)
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    fetchMe()
      .then(setUser)
      .catch(() => setUser(null))
      .finally(() => setIsLoading(false))
  }, [])

  const signIn = useCallback(async (email: string, password: string) => {
    const next = await login({ email, password })
    setUser(next)
    return next
  }, [])

  const signUp = useCallback(
    async (input: { name: string; email: string; password: string; phone?: string }) => {
      const next = await register(input)
      setUser(next)
      return next
    },
    [],
  )

  const signOut = useCallback(() => {
    logout().finally(() => setUser(null))
  }, [])

  const value = useMemo(
    () => ({ user, isLoading, signIn, signUp, signOut }),
    [user, isLoading, signIn, signUp, signOut],
  )

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

export function useAuth(): AuthContextValue {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuth must be used inside AuthProvider')
  return ctx
}
