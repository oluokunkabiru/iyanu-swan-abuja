import { useEffect } from 'react'
import { Link, useSearchParams } from 'react-router-dom'
import { Section } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { useAuth } from '@/context/AuthContext'

export default function EmailVerified() {
  const [params] = useSearchParams()
  const status = params.get('status')
  const verified = status === 'success'
  const { user, refreshUser } = useAuth()

  useEffect(() => {
    // Picks up the now-verified email_verified_at (and any activation that
    // followed it) for whoever is signed in on this browser — a no-op if
    // this link was opened somewhere the member isn't logged in.
    if (verified) {
      refreshUser()
    }
  }, [verified, refreshUser])

  return (
    <Section>
      <div className="mx-auto max-w-md text-center">
        {verified ? (
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

        <Button asChild className="mt-8">
          <Link to={user ? '/members' : '/login'}>{user ? 'Go to members area' : 'Sign in'}</Link>
        </Button>
      </div>
    </Section>
  )
}
