import { useState, type FormEvent } from 'react'
import { Navigate, useNavigate } from 'react-router-dom'
import { Section } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useAuth } from '@/context/AuthContext'

export default function ChangePassword() {
  const { user, isLoading, updatePassword } = useAuth()
  const navigate = useNavigate()
  const [error, setError] = useState<string | null>(null)
  const [submitting, setSubmitting] = useState(false)

  if (!isLoading && !user) {
    return <Navigate to="/login" replace />
  }

  if (!isLoading && user && !user.mustChangePassword) {
    return <Navigate to="/members" replace />
  }

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault()
    const form = new FormData(event.currentTarget)
    const currentPassword = String(form.get('currentPassword') ?? '')
    const password = String(form.get('password') ?? '')
    const passwordConfirmation = String(form.get('passwordConfirmation') ?? '')

    if (!currentPassword || !password || !passwordConfirmation) {
      setError('Complete all password fields.')
      return
    }

    if (password !== passwordConfirmation) {
      setError('Your new password and confirmation do not match.')
      return
    }

    setError(null)
    setSubmitting(true)

    try {
      await updatePassword({ currentPassword, password, passwordConfirmation })
      navigate('/members', { replace: true })
    } catch {
      setError('We could not change your password. Check your temporary password and try again.')
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <Section>
      <div className="mx-auto max-w-lg border border-border bg-card p-6 md:p-8">
        <h1 className="text-3xl">Choose a new password</h1>
        <p className="mt-3 text-[0.95rem] leading-relaxed text-muted-foreground">
          For your security, replace the temporary password emailed to you before accessing your member account.
        </p>

        <form onSubmit={handleSubmit} className="mt-8 grid gap-5">
          <div className="grid gap-2">
            <Label htmlFor="currentPassword">Temporary password</Label>
            <Input id="currentPassword" name="currentPassword" type="password" autoComplete="current-password" />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="password">New password</Label>
            <Input id="password" name="password" type="password" minLength={8} autoComplete="new-password" />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="passwordConfirmation">Confirm new password</Label>
            <Input id="passwordConfirmation" name="passwordConfirmation" type="password" minLength={8} autoComplete="new-password" />
          </div>

          {error && <p role="alert" className="border-l-2 border-destructive bg-destructive/8 px-4 py-3 text-[0.88rem] text-destructive">{error}</p>}

          <Button type="submit" size="lg" disabled={submitting} className="w-full">
            {submitting ? 'Saving password…' : 'Save new password'}
          </Button>
        </form>
      </div>
    </Section>
  )
}
