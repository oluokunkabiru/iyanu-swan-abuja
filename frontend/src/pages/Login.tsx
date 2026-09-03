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
    await signIn(email, password)
    navigate(from, { replace: true })
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
              <Input id="email" name="email" type="email" autoComplete="email" defaultValue="member@swanabujachapter.com" />
            </div>
            <div className="space-y-2">
              <Label htmlFor="password">Password</Label>
              <Input id="password" name="password" type="password" autoComplete="current-password" defaultValue="demo-password" />
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
          <h2 className="text-[1.05rem]">About this build</h2>
          <p className="mt-3 text-[0.9rem] leading-relaxed text-muted-foreground">
            There is no backend connected. Any credentials sign you into a demonstration member
            record so the whole members area can be reviewed — CPD tracker, dues history, tickets
            and profile.
          </p>
          <p className="mt-3 text-[0.9rem] leading-relaxed text-muted-foreground">
            The session is kept in browser storage and cleared when you sign out.
          </p>
        </aside>
      </div>
    </Section>
  )
}
