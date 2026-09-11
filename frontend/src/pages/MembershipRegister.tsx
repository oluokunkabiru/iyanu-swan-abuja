import { useState, type FormEvent } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { Check } from 'lucide-react'
import { getMembershipLevels } from '@/api/content'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Textarea } from '@/components/ui/textarea'
import { useAuth } from '@/context/AuthContext'
import { useSettings } from '@/context/SettingsContext'
import { useApiData } from '@/hooks/useApiData'
import { formatNaira } from '@/lib/format'
import type { MembershipLevel } from '@/types'

export default function MembershipRegister() {
  const { signUp } = useAuth()
  const { settings } = useSettings()
  const navigate = useNavigate()
  const [error, setError] = useState<string | null>(null)
  const [submitting, setSubmitting] = useState(false)
  const [credential, setCredential] = useState<'ACA' | 'FCA' | ''>('')

  const { data: levels } = useApiData(getMembershipLevels, [] as MembershipLevel[])
  const registrationSteps = settings?.registrationSteps ?? []

  async function handleSubmit(e: FormEvent<HTMLFormElement>) {
    e.preventDefault()
    setError(null)
    const form = new FormData(e.currentTarget)
    const name = String(form.get('name') ?? '').trim()
    const email = String(form.get('email') ?? '').trim()
    const password = String(form.get('password') ?? '')
    const membershipNumber = String(form.get('membershipNumber') ?? '').trim()
    const phone = String(form.get('phone') ?? '').trim()
    const residentialAddress = String(form.get('residentialAddress') ?? '').trim()
    const placeOfWork = String(form.get('placeOfWork') ?? '').trim()
    const dateOfBirth = String(form.get('dateOfBirth') ?? '').trim()

    if (
      !name ||
      !email ||
      password.length < 8 ||
      !membershipNumber ||
      !credential ||
      !phone ||
      !residentialAddress ||
      !placeOfWork
    ) {
      setError('Fill in every field — a password of at least 8 characters and your ICAN level are both required.')
      return
    }

    setSubmitting(true)
    try {
      await signUp({
        name,
        email,
        password,
        membershipNumber,
        credential,
        phone,
        residentialAddress,
        placeOfWork,
        dateOfBirth: dateOfBirth || undefined,
      })
      navigate('/members')
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
        title="Create your account"
        intro="Registration itself is free. Your details are matched against the ICAN roll, so use the name and membership number exactly as they appear on your record — you'll choose a membership level and pay your dues from your dashboard afterwards."
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
                  <Input id="membershipNumber" name="membershipNumber" placeholder="As it appears on your ICAN record" />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="credential">ICAN level</Label>
                  <Select value={credential || undefined} onValueChange={(value) => setCredential(value as 'ACA' | 'FCA')}>
                    <SelectTrigger id="credential" className="w-full">
                      <SelectValue placeholder="Select ACA or FCA" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="ACA">ACA</SelectItem>
                      <SelectItem value="FCA">FCA</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="email">Email address</Label>
                  <Input id="email" name="email" type="email" autoComplete="email" placeholder="you@example.com" />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="phone">WhatsApp telephone number</Label>
                  <Input id="phone" name="phone" type="tel" autoComplete="tel" placeholder="0800 000 0000" />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="password">Choose a password</Label>
                  <Input id="password" name="password" type="password" autoComplete="new-password" placeholder="At least 8 characters" />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="placeOfWork">Place of work</Label>
                  <Input id="placeOfWork" name="placeOfWork" autoComplete="organization" placeholder="Employer or firm name" />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="dateOfBirth">Date of birth (optional)</Label>
                  <Input id="dateOfBirth" name="dateOfBirth" type="date" autoComplete="bday" />
                  <p className="text-[0.78rem] text-muted-foreground">
                    Used only to send you a birthday greeting.
                  </p>
                </div>
                <div className="space-y-2 sm:col-span-2">
                  <Label htmlFor="residentialAddress">Residential address</Label>
                  <Textarea
                    id="residentialAddress"
                    name="residentialAddress"
                    autoComplete="street-address"
                    placeholder="Street, city and state"
                    rows={2}
                  />
                </div>
              </div>


              {error && (
                <p role="alert" className="border-l-2 border-destructive bg-destructive/8 px-4 py-3 text-[0.88rem] text-destructive">
                  {error}
                </p>
              )}

              <Button type="submit" size="lg" disabled={submitting}>
                {submitting ? 'Creating your account…' : 'Create my account'}
              </Button>

              <p className="text-[0.82rem] leading-relaxed text-muted-foreground">
                We&rsquo;ll send a link to confirm your email. Your account opens as pending, and
                moves to active once you&rsquo;ve verified your email and paid your dues — by card,
                bank transfer or USSD online, or by bank transfer with evidence you upload for review.
              </p>
            </form>
          </div>

          <aside className="space-y-6">
            <div className="border border-border bg-card p-6">
              <h2 className="text-[1.05rem]">Membership levels</h2>
              <p className="mt-2 text-[0.85rem] text-muted-foreground">
                Pick the level that fits once you&rsquo;re signed in — each has its own subscription
                and welfare levy.
              </p>
              {levels.length > 0 && (
                <dl className="mt-4 space-y-3 text-[0.9rem]">
                  {levels.map((level) => (
                    <div key={level.id} className="flex items-baseline justify-between gap-4 border-t border-border pt-3 first:border-0 first:pt-0">
                      <dt className="font-medium">{level.name}</dt>
                      <dd className="tnum font-medium">
                        {formatNaira(level.subscriptionAmount + level.welfareAmount)}
                      </dd>
                    </div>
                  ))}
                </dl>
              )}
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
