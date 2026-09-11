import { useEffect, useState } from 'react'
import { Link, useParams, useSearchParams } from 'react-router-dom'
import { verifyEmail } from '@/api/auth'
import { Section } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { useAuth } from '@/context/AuthContext'

type Status = 'verifying' | 'success' | 'invalid'

export default function EmailVerified() {
  const { id, hash } = useParams<{ id: string; hash: string }>()
  const [params] = useSearchParams()
  const expires = params.get('expires')
  const signature = params.get('signature')
  const { user, refreshUser } = useAuth()
  const [status, setStatus] = useState<Status>('verifying')

  useEffect(() => {
    if (!id || !hash || !expires || !signature) {
      setStatus('invalid')
      return
    }

    let active = true
    verifyEmail(id, hash, expires, signature)
      .then(() => {
        if (!active) return
        setStatus('success')
        // Picks up the now-verified email_verified_at (and any activation
        // that followed it) for whoever is signed in on this browser — a
        // no-op if this link was opened somewhere the member isn't logged in.
        refreshUser()
      })
      .catch(() => {
        if (active) setStatus('invalid')
      })

    return () => {
      active = false
    }
  }, [id, hash, expires, signature, refreshUser])

  return (
    <Section>
      <div className="mx-auto max-w-md text-center">
        {status === 'verifying' ? (
          <>
            <h1 className="text-2xl">Confirming your email…</h1>
            <p className="mt-3 text-[0.92rem] text-muted-foreground">This will only take a moment.</p>
          </>
        ) : status === 'success' ? (
          <>
            <h1 className="text-2xl">Email verified</h1>
            <p className="mt-3 text-[0.92rem] text-muted-foreground">
              Thank you — your registered email is confirmed. Once your dues are also paid, your
              membership will be marked active automatically.
            </p>
          </>
        ) : (
          <>
            <h1 className="text-2xl">We couldn&rsquo;t verify that link</h1>
            <p className="mt-3 text-[0.92rem] text-muted-foreground">
              This verification link is invalid or has expired. Sign in and request a new one from
              your dashboard.
            </p>
          </>
        )}

        {status !== 'verifying' && (
          <Button asChild className="mt-8">
            <Link to={user ? '/members' : '/login'}>{user ? 'Go to members area' : 'Sign in'}</Link>
          </Button>
        )}
      </div>
    </Section>
  )
}
