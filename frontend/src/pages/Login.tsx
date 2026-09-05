import { useState, type FormEvent } from 'react'
import { Link, useLocation, useNavigate } from 'react-router-dom'
import { Section } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useAuth } from '@/context/AuthContext'

export default function Login() {
  const { signIn } = useAuth()
  const navigate = useNavigate()
  const location = useLocation()
  const [error, setError] = useState<string | null>(null)
  const [submitting, setSubmitting] = useState(false)

  const from = (location.state as { from?: string } | null)?.from ?? '/members'

  async function handleSubmit(e: FormEvent<HTMLFormElement>) {
    e.preventDefault()
    const form = new FormData(e.currentTarget)
    const email = String(form.get('email') ?? '').trim()
    const password = String(form.get('password') ?? '')

    if (!email || !password) {
      setError('Enter your email address and password.')
      return
    }

    setError(null)
    setSubmitting(true)
    try {
      await signIn(email, password)
      navigate(from, { replace: true })
    } catch {
      setError('Those details did not match an active account. Check your email and password.')
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <Section>
      <div className="mx-auto grid max-w-4xl gap-10 md:grid-cols-2">
        <div>
          <h1 className="text-3xl">Sign in</h1>
          <p className="mt-3 max-w-[45ch] text-[0.95rem] leading-relaxed text-muted-foreground">
            Signing in applies your member rate at checkout, opens your CPD record, and shows the
            dues on your account.
          </p>

          <form onSubmit={handleSubmit} className="mt-8 space-y-5">
            <div className="space-y-2">
              <Label htmlFor="email">Email address</Label>
              <Input id="email" name="email" type="email" autoComplete="email" placeholder="you@example.com" />
            </div>
            <div className="space-y-2">
              <Label htmlFor="password">Password</Label>
              <Input id="password" name="password" type="password" autoComplete="current-password" />
            </div>

            {error && (
              <p role="alert" className="border-l-2 border-destructive bg-destructive/8 px-4 py-3 text-[0.88rem] text-destructive">
                {error}
              </p>
            )}

            <Button type="submit" size="lg" disabled={submitting} className="w-full">
              {submitting ? 'Signing in…' : 'Sign in'}
            </Button>
          </form>

          <p className="mt-6 text-[0.88rem] text-muted-foreground">
            Not yet on the active roll?{' '}
            <Link to="/membership/register" className="font-semibold text-plum-700 underline-offset-4 hover:underline dark:text-primary">
              Register and pay your dues
            </Link>
          </p>
        </div>

        <aside className="border border-border bg-card p-6">
          <h2 className="text-[1.05rem]">What signing in unlocks</h2>
          <p className="mt-3 text-[0.9rem] leading-relaxed text-muted-foreground">
            Your CPD tracker, subscription and welfare dues history, event tickets and profile —
            all matched to your chapter membership record.
          </p>
        </aside>
      </div>
    </Section>
  )
}
