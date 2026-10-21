import { useState, type FormEvent } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { isAxiosError } from 'axios'
import { Check } from 'lucide-react'
import { lookupMembershipNumber, type MembershipLookup } from '@/api/auth'
import { getMembershipLevels } from '@/api/content'
import { PasswordRequirements } from '@/components/common/PasswordRequirements'
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
import { defaultPasswordPolicy, passwordMeetsPolicy, passwordRequirementText } from '@/lib/password'
import type { MembershipLevel } from '@/types'

export default function MembershipRegister() {
  const { signUp } = useAuth()
  const { settings } = useSettings()
  const navigate = useNavigate()
  const [error, setError] = useState<string | null>(null)
  const [submitting, setSubmitting] = useState(false)
  const [credential, setCredential] = useState('')
  const [password, setPassword] = useState('')
  const [passwordConfirmation, setPasswordConfirmation] = useState('')
  const [numberInput, setNumberInput] = useState('')
  const [lookup, setLookup] = useState<MembershipLookup | null>(null)
  const [checking, setChecking] = useState(false)
  const [lookupError, setLookupError] = useState<string | null>(null)

  const { data: levels } = useApiData(getMembershipLevels, [] as MembershipLevel[])
  const registrationSteps = settings?.registrationSteps ?? []
  const passwordPolicy = settings?.passwordPolicy ?? defaultPasswordPolicy

  async function handleLookup(e: FormEvent<HTMLFormElement>) {
    e.preventDefault()
    setLookupError(null)
    setLookup(null)
    if (!/\d/.test(numberInput)) {
      setLookupError('Enter your ICAN membership number, e.g. MB040071 or just the digits.')
      return
    }
    setChecking(true)
    try {
      const result = await lookupMembershipNumber(numberInput)
      setLookup(result)
      setNumberInput(result.membershipNumber)
    } catch (err) {
      const message = isAxiosError(err) ? err.response?.data?.errors?.membership_number?.[0] : undefined
      setLookupError(message ?? 'We could not check that number just now. Try again in a moment.')
    } finally {
      setChecking(false)
    }
  }

  async function handleSubmit(e: FormEvent<HTMLFormElement>) {
    e.preventDefault()
    setError(null)
    const form = new FormData(e.currentTarget)
    const lastName = String(form.get('lastName') ?? '').trim()
    const firstName = String(form.get('firstName') ?? '').trim()
    const middleName = String(form.get('middleName') ?? '').trim()
    const email = String(form.get('email') ?? '').trim()
    const membershipNumber = lookup?.membershipNumber ?? ''
    const phone = String(form.get('phone') ?? '').trim()
    const residentialAddress = String(form.get('residentialAddress') ?? '').trim()
    const placeOfWork = String(form.get('placeOfWork') ?? '').trim()
    const professionalInterest = String(form.get('professionalInterest') ?? '').trim()
    const dateOfBirth = String(form.get('dateOfBirth') ?? '').trim()

    if (
      !lastName ||
      !firstName ||
      !email ||
      !passwordMeetsPolicy(password, passwordPolicy) ||
      !passwordConfirmation ||
      !membershipNumber ||
      !credential ||
      !phone ||
      !residentialAddress ||
      !placeOfWork ||
      !dateOfBirth
    ) {
      setError(`Fill in every field — ${passwordRequirementText(passwordPolicy)} Your ICAN level is also required.`)
      return
    }

    if (password !== passwordConfirmation) {
      setError('Your password and confirmation do not match.')
      return
    }

    setSubmitting(true)
    try {
      await signUp({
        lastName,
        firstName,
        middleName: middleName || undefined,
        email,
        password,
        passwordConfirmation,
        membershipNumber,
        credential,
        phone,
        residentialAddress,
        placeOfWork,
        professionalInterest: professionalInterest || undefined,
        dateOfBirth,
      })
      navigate('/members')
    } catch (err) {
      const message = isAxiosError(err) ? err.response?.data?.errors?.membership_number?.[0] : undefined
      setError(message ?? 'We could not complete that registration. Check your details and try again.')
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
        intro="Registration itself is free. Start with your ICAN membership number — we check it first so nobody is registered twice — then use your name exactly as it appears on your ICAN record. You'll choose a membership level and pay your dues from your dashboard afterwards."
      />

      <Section>
        <div className="grid gap-12 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)]">
          <div>
            <SectionHeading
              title="Start with your ICAN number"
              lede="We check it against the chapter's records first, so nobody ends up with two accounts."
              className="mb-6"
            />
            <form onSubmit={handleLookup} className="space-y-3">
              <Label htmlFor="membershipNumber">ICAN membership number</Label>
              <div className="flex flex-wrap gap-3">
                <Input
                  id="membershipNumber"
                  name="membershipNumber"
                  autoComplete="off"
                  placeholder="MB040071 or just 40071"
                  className="max-w-xs uppercase"
                  value={numberInput}
                  onChange={(event) => {
                    setNumberInput(event.target.value)
                    setLookup(null)
                    setLookupError(null)
                  }}
                  aria-describedby="membership-number-help"
                />
                <Button type="submit" variant="outline" disabled={checking || !numberInput.trim()}>
                  {checking ? 'Checking…' : 'Check number'}
                </Button>
              </div>
              <p id="membership-number-help" className="text-[0.8rem] text-muted-foreground">
                Type it with or without the letters — numbers shorter than six digits are padded with
                zeros, so 40071 becomes MB040071.
              </p>
              {lookupError && (
                <p role="alert" className="text-[0.85rem] text-destructive">
                  {lookupError}
                </p>
              )}
              {lookup?.exists && (
                <div role="status" className="border-l-2 border-gold-500 bg-muted/40 px-4 py-4 text-[0.9rem]">
                  <p className="font-semibold">{lookup.membershipNumber} is already registered</p>
                  <p className="mt-1 text-muted-foreground">
                    The account belongs to <span className="font-medium text-foreground">{lookup.name}</span>,
                    with the email {lookup.maskedEmail}. If that&rsquo;s you, sign in — or reset your
                    password if you can&rsquo;t remember it.
                  </p>
                  <div className="mt-3 flex flex-wrap gap-3">
                    <Button asChild size="sm">
                      <Link to="/login">Sign in</Link>
                    </Button>
                    <Button asChild size="sm" variant="outline">
                      <Link to="/forgot-password">Reset password</Link>
                    </Button>
                  </div>
                  <p className="mt-3 text-[0.8rem] text-muted-foreground">
                    Not you? <Link to="/contact" className="underline underline-offset-4">Contact the chapter</Link>{' '}
                    and we&rsquo;ll sort out the record.
                  </p>
                </div>
              )}
              {lookup && !lookup.exists && (
                <p role="status" className="text-[0.85rem] font-medium text-primary">
                  {lookup.membershipNumber} isn&rsquo;t registered yet — fill in your details below.
                </p>
              )}
            </form>

            <SectionHeading title="Your details" className="mb-6 mt-12" />
            <form onSubmit={handleSubmit} className="space-y-5">
              <fieldset
                disabled={!lookup || lookup.exists}
                className="space-y-5 transition-opacity disabled:opacity-50"
              >
              <div className="grid gap-5 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="lastName">Last name</Label>
                  <Input key={`lastName-${lookup?.membershipNumber ?? ''}`} defaultValue={lookup?.exists ? lookup.lastName ?? '' : undefined} id="lastName" name="lastName" autoComplete="family-name" placeholder="Surname as it appears on your ICAN record" />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="firstName">First name</Label>
                  <Input key={`firstName-${lookup?.membershipNumber ?? ''}`} defaultValue={lookup?.exists ? lookup.firstName ?? '' : undefined} id="firstName" name="firstName" autoComplete="given-name" placeholder="First name as it appears on your ICAN record" />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="middleName">Middle name (optional)</Label>
                  <Input key={`middleName-${lookup?.membershipNumber ?? ''}`} defaultValue={lookup?.exists ? lookup.middleName ?? '' : undefined} id="middleName" name="middleName" autoComplete="additional-name" placeholder="Middle name, if applicable" />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="credential">ICAN level</Label>
                  <Select value={credential || undefined} onValueChange={setCredential}>
                    <SelectTrigger id="credential" className="w-full">
                      <SelectValue placeholder="Select your level" />
                    </SelectTrigger>
                    <SelectContent>
                      {levels.map((level) => (
                        <SelectItem key={level.id} value={level.name}>
                          {level.name}
                        </SelectItem>
                      ))}
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
                  <Label htmlFor="placeOfWork">Place of work</Label>
                  <Input id="placeOfWork" name="placeOfWork" autoComplete="organization" placeholder="Employer or firm name" />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="professionalInterest">Area of professional interest (optional)</Label>
                  <Input id="professionalInterest" name="professionalInterest" placeholder="For example: Audit, Tax or Public Sector" />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="dateOfBirth">Date of birth</Label>
                  <Input id="dateOfBirth" name="dateOfBirth" type="date" autoComplete="bday" required />
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
                <div className="space-y-2">
                  <Label htmlFor="password">Choose a password</Label>
                  <Input id="password" name="password" type="password" minLength={passwordPolicy.minLength} autoComplete="new-password" placeholder={`At least ${passwordPolicy.minLength} characters`} value={password} onChange={(event) => setPassword(event.target.value)} aria-describedby="password-requirements" />
                  <div id="password-requirements">
                    <PasswordRequirements password={password} policy={passwordPolicy} />
                  </div>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="passwordConfirmation">Confirm your password</Label>
                  <Input id="passwordConfirmation" name="passwordConfirmation" type="password" minLength={passwordPolicy.minLength} autoComplete="new-password" placeholder="Type your password again" value={passwordConfirmation} onChange={(event) => setPasswordConfirmation(event.target.value)} aria-describedby={passwordConfirmation && password !== passwordConfirmation ? 'password-confirmation-error' : undefined} />
                  {passwordConfirmation && password !== passwordConfirmation && (
                    <p id="password-confirmation-error" className="text-[0.78rem] text-destructive">
                      Your password and confirmation do not match.
                    </p>
                  )}
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
              </fieldset>

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
                Each level has its own subscription and welfare levy — you can review or change
                yours later from your dashboard.
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
