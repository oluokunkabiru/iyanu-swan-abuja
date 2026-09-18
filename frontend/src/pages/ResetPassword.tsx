import { useState, type FormEvent } from 'react'
import { Link, useNavigate, useSearchParams } from 'react-router-dom'
import { resetPassword } from '@/api/auth'
import { Section } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useSettings } from '@/context/SettingsContext'
import { defaultPasswordPolicy, passwordMeetsPolicy, passwordRequirementText } from '@/lib/password'

export default function ResetPassword() {
  const navigate = useNavigate()
  const [searchParams] = useSearchParams()
  const [error, setError] = useState<string | null>(null)
  const [submitting, setSubmitting] = useState(false)
  const { settings } = useSettings()
  const token = searchParams.get('token') ?? ''
  const email = searchParams.get('email') ?? ''
  const passwordPolicy = settings?.passwordPolicy ?? defaultPasswordPolicy

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault()
    const form = new FormData(event.currentTarget)
    const password = String(form.get('password') ?? '')
    const passwordConfirmation = String(form.get('passwordConfirmation') ?? '')

    if (!token || !email) {
      setError('This reset link is incomplete. Please request a new one.')
      return
    }

    if (!passwordMeetsPolicy(password, passwordPolicy)) {
      setError(passwordRequirementText(passwordPolicy))
      return
    }

    if (password !== passwordConfirmation) {
      setError('Your new password and confirmation do not match.')
      return
    }

    setError(null)
    setSubmitting(true)

    try {
      await resetPassword({ token, email, password, passwordConfirmation })
      navigate('/login', { replace: true, state: { passwordReset: true } })
    } catch {
      setError('This reset link is invalid or has expired. Please request a new one.')
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <Section>
      <div className="mx-auto max-w-lg border border-border bg-card p-6 md:p-8">
        <h1 className="text-3xl">Choose a new password</h1>
        <p className="mt-3 text-[0.95rem] leading-relaxed text-muted-foreground">
          Choose a password you will remember. {passwordRequirementText(passwordPolicy)}
        </p>

        <form onSubmit={handleSubmit} className="mt-8 grid gap-5">
          <div className="grid gap-2">
            <Label htmlFor="email">Email address</Label>
            <Input id="email" type="email" value={email} disabled />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="password">New password</Label>
            <Input id="password" name="password" type="password" minLength={passwordPolicy.minLength} autoComplete="new-password" />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="passwordConfirmation">Confirm new password</Label>
            <Input id="passwordConfirmation" name="passwordConfirmation" type="password" minLength={passwordPolicy.minLength} autoComplete="new-password" />
          </div>

          {error && <p role="alert" className="border-l-2 border-destructive bg-destructive/8 px-4 py-3 text-[0.88rem] text-destructive">{error}</p>}

          <Button type="submit" size="lg" disabled={submitting} className="w-full">
            {submitting ? 'Saving password…' : 'Save new password'}
          </Button>
        </form>

        <p className="mt-6 text-[0.88rem] text-muted-foreground">
          Need another link?{' '}
          <Link to="/forgot-password" className="font-semibold text-plum-700 underline-offset-4 hover:underline dark:text-primary">
            Request a new reset link
          </Link>
        </p>
      </div>
    </Section>
  )
}
