import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from 'react'
import { demoUser } from '@/data'
import type { AuthUser } from '@/types'

const STORAGE_KEY = 'swan-session'

interface AuthContextValue {
  user: AuthUser | null
  isLoading: boolean
  signIn: (email: string, password: string) => Promise<AuthUser>
  signUp: (input: { name: string; email: string; membershipNumber: string }) => Promise<AuthUser>
  signOut: () => void
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined)

function readSession(): AuthUser | null {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    return raw ? (JSON.parse(raw) as AuthUser) : null
  } catch {
    return null
  }
}

function writeSession(user: AuthUser | null) {
  try {
    if (user) localStorage.setItem(STORAGE_KEY, JSON.stringify(user))
    else localStorage.removeItem(STORAGE_KEY)
  } catch {
    /* storage unavailable — session stays in memory for this tab */
  }
}

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<AuthUser | null>(null)
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    setUser(readSession())
    setIsLoading(false)
  }, [])

  const signIn = useCallback(async (email: string, _password: string) => {
    // No backend in this build: any credentials open the demonstration record.
    const next: AuthUser = { ...demoUser, email: email || demoUser.email }
    setUser(next)
    writeSession(next)
    return next
  }, [])

  const signUp = useCallback(
    async (input: { name: string; email: string; membershipNumber: string }) => {
      const next: AuthUser = {
        ...demoUser,
        name: input.name,
        email: input.email,
        membershipNumber: input.membershipNumber || demoUser.membershipNumber,
        membershipStatus: 'pending',
        joinedAt: new Date().toISOString().slice(0, 10),
      }
      setUser(next)
      writeSession(next)
      return next
    },
    [],
  )

  const signOut = useCallback(() => {
    setUser(null)
    writeSession(null)
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
