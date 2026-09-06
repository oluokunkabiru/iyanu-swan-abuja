import { useState, type FormEvent } from 'react'
import { Link } from 'react-router-dom'
import { Check } from 'lucide-react'
import { paySubscriptionDues } from '@/api/auth'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useAuth } from '@/context/AuthContext'
import { useSettings } from '@/context/SettingsContext'
import { formatNaira } from '@/lib/format'

export default function MembershipRegister() {
  const { signUp } = useAuth()
  const { settings } = useSettings()
  const [error, setError] = useState<string | null>(null)
  const [submitting, setSubmitting] = useState(false)

  const subscriptionFee = settings?.subscriptionFee ?? 0
  const welfareFee = settings?.welfareFee ?? 0
  const total = subscriptionFee + welfareFee
  const registrationSteps = settings?.registrationSteps ?? []

  async function handleSubmit(e: FormEvent<HTMLFormElement>) {
    e.preventDefault()
    setError(null)
    const form = new FormData(e.currentTarget)
    const name = String(form.get('name') ?? '').trim()
    const email = String(form.get('email') ?? '').trim()
    const password = String(form.get('password') ?? '')
    const phone = String(form.get('phone') ?? '').trim()

    if (!name || !email || password.length < 8) {
      setError('Enter your name and email, and choose a password of at least 8 characters.')
      return
    }

    setSubmitting(true)
    try {
      await signUp({ name, email, password, phone: phone || undefined })
      const { authorizationUrl } = await paySubscriptionDues(new Date().getFullYear())
      window.location.href = authorizationUrl
    } catch {
      setError('We could not complete that registration. Check your details and try again.')
      setSubmitting(false)
    }
  }

  return (
    <>
      <PageHeader
        breadcrumb={[
          { label: 'Home', to: '/' },
          { label: 'Membership', to: '/membership' },
          { label: 'Register' },
        ]}
        title="Register and pay your dues"
        intro="Your details are matched against the ICAN roll, so use the name and membership number exactly as they appear on your record."
      />

      <Section>
        <div className="grid gap-12 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)]">
          <div>
            <SectionHeading title="Your details" className="mb-6" />
            <form onSubmit={handleSubmit} className="space-y-5">
              <div className="grid gap-5 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="name">Full name</Label>
                  <Input id="name" name="name" autoComplete="name" placeholder="As it appears on your ICAN record" />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="email">Email address</Label>
                  <Input id="email" name="email" type="email" autoComplete="email" placeholder="you@example.com" />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="phone">Phone number</Label>
                  <Input id="phone" name="phone" type="tel" autoComplete="tel" placeholder="0800 000 0000" />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="password">Choose a password</Label>
                  <Input id="password" name="password" type="password" autoComplete="new-password" placeholder="At least 8 characters" />
                </div>
              </div>


              {error && (
                <p role="alert" className="border-l-2 border-destructive bg-destructive/8 px-4 py-3 text-[0.88rem] text-destructive">
                  {error}
                </p>
              )}

              <Button type="submit" size="lg" disabled={submitting}>
                {submitting ? 'Taking you to payment…' : `Pay ${formatNaira(total)} and register`}
              </Button>

              <p className="text-[0.82rem] leading-relaxed text-muted-foreground">
                You&rsquo;ll be taken to a secure payment page to complete your dues by card, bank
                transfer or USSD. Your account opens immediately as pending, and moves to active once
                payment is confirmed.
              </p>
            </form>
          </div>

          <aside className="space-y-6">
            <div className="border border-border bg-card p-6">
              <h2 className="text-[1.05rem]">What you are paying</h2>
              <dl className="mt-4 space-y-3 text-[0.9rem]">
                <div className="flex items-baseline justify-between gap-4">
                  <dt className="text-muted-foreground">Annual subscription</dt>
                  <dd className="tnum font-medium">{formatNaira(subscriptionFee)}</dd>
                </div>
                <div className="flex items-baseline justify-between gap-4">
                  <dt className="text-muted-foreground">Welfare levy</dt>
                  <dd className="tnum font-medium">{formatNaira(welfareFee)}</dd>
                </div>
                <div className="flex items-baseline justify-between gap-4 border-t border-border pt-3">
                  <dt className="font-medium">Total</dt>
                  <dd className="tnum font-heading text-xl text-plum-700 dark:text-primary">
                    {formatNaira(total)}
                  </dd>
                </div>
              </dl>
            </div>

            <div className="border border-border bg-card p-6">
              <h2 className="text-[1.05rem]">What happens next</h2>
              <ol className="mt-4 space-y-3.5">
                {registrationSteps.map((s) => (
                  <li key={s.step} className="flex gap-3">
                    <Check aria-hidden="true" className="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                    <span className="text-[0.88rem] leading-relaxed">
                      <span className="font-medium">{s.title}.</span>{' '}
                      <span className="text-muted-foreground">{s.description}</span>
                    </span>
                  </li>
                ))}
              </ol>
              <Link
                to="/faqs"
                className="mt-5 inline-block border-b border-plum-700 pb-0.5 text-[0.85rem] font-semibold text-plum-700 dark:border-primary dark:text-primary"
              >
                Payment questions
              </Link>
            </div>
          </aside>
        </div>
      </Section>
    </>
  )
}
