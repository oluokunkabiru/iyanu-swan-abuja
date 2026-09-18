import { useState, type FormEvent } from 'react'
import { Link } from 'react-router-dom'
import { requestPasswordReset } from '@/api/auth'
import { Section } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

export default function ForgotPassword() {
  const [message, setMessage] = useState<string | null>(null)
  const [error, setError] = useState<string | null>(null)
  const [submitting, setSubmitting] = useState(false)

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault()
    const email = String(new FormData(event.currentTarget).get('email') ?? '').trim()

    if (!email) {
      setError('Enter your email address.')
      return
    }

    setError(null)
    setSubmitting(true)

    try {
      const response = await requestPasswordReset(email)
      setMessage(response.message)
    } catch {
      setError('We could not send a reset link. Please try again shortly.')
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <Section>
      <div className="mx-auto max-w-lg border border-border bg-card p-6 md:p-8">
        <h1 className="text-3xl">Reset your password</h1>
        <p className="mt-3 text-[0.95rem] leading-relaxed text-muted-foreground">
          Enter the email address you use for your membership account. We will send you a secure link to choose a new password.
        </p>

        <form onSubmit={handleSubmit} className="mt-8 grid gap-5">
          <div className="grid gap-2">
            <Label htmlFor="email">Email address</Label>
            <Input id="email" name="email" type="email" autoComplete="email" placeholder="you@example.com" />
          </div>

          {message && <p role="status" className="border-l-2 border-primary bg-primary/8 px-4 py-3 text-[0.88rem] text-foreground">{message}</p>}
          {error && <p role="alert" className="border-l-2 border-destructive bg-destructive/8 px-4 py-3 text-[0.88rem] text-destructive">{error}</p>}

          <Button type="submit" size="lg" disabled={submitting} className="w-full">
            {submitting ? 'Sending link…' : 'Send reset link'}
          </Button>
        </form>

        <p className="mt-6 text-[0.88rem] text-muted-foreground">
          Remembered your password?{' '}
          <Link to="/login" className="font-semibold text-plum-700 underline-offset-4 hover:underline dark:text-primary">
            Sign in
          </Link>
        </p>
      </div>
    </Section>
  )
}
