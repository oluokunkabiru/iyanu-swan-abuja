import { useState, type FormEvent } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { Check } from 'lucide-react'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useAuth } from '@/context/AuthContext'
import { registrationSteps, site } from '@/data'
import { formatNaira } from '@/lib/format'

const paymentMethods = ['Debit card', 'Bank transfer', 'USSD'] as const

export default function MembershipRegister() {
  const { signUp } = useAuth()
  const navigate = useNavigate()
  const [method, setMethod] = useState<(typeof paymentMethods)[number]>('Debit card')
  const [error, setError] = useState<string | null>(null)
  const [submitting, setSubmitting] = useState(false)

  const total = site.subscriptionFee + site.welfareFee

  async function handleSubmit(e: FormEvent<HTMLFormElement>) {
    e.preventDefault()
    setError(null)
    const form = new FormData(e.currentTarget)
    const name = String(form.get('name') ?? '').trim()
    const email = String(form.get('email') ?? '').trim()
    const membershipNumber = String(form.get('membershipNumber') ?? '').trim()

    if (!name || !email || !membershipNumber) {
      setError('Enter your name, email and ICAN membership number to continue.')
      return
    }

    setSubmitting(true)
    await signUp({ name, email, membershipNumber })
    navigate('/members')
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
                  <Label htmlFor="membershipNumber">ICAN membership number</Label>
                  <Input id="membershipNumber" name="membershipNumber" placeholder="ICAN/000000" />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="email">Email address</Label>
                  <Input id="email" name="email" type="email" autoComplete="email" placeholder="you@example.com" />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="phone">Phone number</Label>
                  <Input id="phone" name="phone" type="tel" autoComplete="tel" placeholder="0800 000 0000" />
                </div>
              </div>

              <fieldset className="space-y-2">
                <legend className="mb-2 text-sm font-medium">How would you like to pay?</legend>
                <div className="flex flex-wrap gap-2">
                  {paymentMethods.map((m) => (
                    <button
                      key={m}
                      type="button"
                      onClick={() => setMethod(m)}
                      aria-pressed={method === m}
                      className={
                        method === m
                          ? 'rounded-sm border border-plum-700 bg-plum-700 px-4 py-2 text-[0.86rem] font-medium text-white dark:border-primary dark:bg-primary dark:text-primary-foreground'
                          : 'rounded-sm border border-border bg-card px-4 py-2 text-[0.86rem] font-medium text-muted-foreground hover:text-foreground'
                      }
                    >
                      {m}
                    </button>
                  ))}
                </div>
              </fieldset>

              {error && (
                <p role="alert" className="border-l-2 border-destructive bg-destructive/8 px-4 py-3 text-[0.88rem] text-destructive">
                  {error}
                </p>
              )}

              <Button type="submit" size="lg" disabled={submitting}>
                {submitting ? 'Opening your record…' : `Pay ${formatNaira(total)} and register`}
              </Button>

              <p className="text-[0.82rem] leading-relaxed text-muted-foreground">
                This build has no payment gateway connected. Submitting opens the members area with
                a demonstration record so you can see what a member sees.
              </p>
            </form>
          </div>

          <aside className="space-y-6">
            <div className="border border-border bg-card p-6">
              <h2 className="text-[1.05rem]">What you are paying</h2>
              <dl className="mt-4 space-y-3 text-[0.9rem]">
                <div className="flex items-baseline justify-between gap-4">
                  <dt className="text-muted-foreground">Annual subscription</dt>
                  <dd className="tnum font-medium">{formatNaira(site.subscriptionFee)}</dd>
                </div>
                <div className="flex items-baseline justify-between gap-4">
                  <dt className="text-muted-foreground">Welfare levy</dt>
                  <dd className="tnum font-medium">{formatNaira(site.welfareFee)}</dd>
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
