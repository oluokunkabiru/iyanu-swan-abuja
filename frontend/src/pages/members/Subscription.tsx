import { useState, type FormEvent } from 'react'
import { getMembershipLevels } from '@/api/content'
import { fetchMySubscriptions, paySubscriptionDues, submitBankTransfer } from '@/api/auth'
import { SectionHeading, StatusTag } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Skeleton } from '@/components/ui/skeleton'
import { useAuth } from '@/context/AuthContext'
import { useApiData } from '@/hooks/useApiData'
import { formatNaira, formatShortDate } from '@/lib/format'
import { cn } from '@/lib/utils'
import type { MembershipLevel, SubscriptionRecord } from '@/types'

function DuesPaymentCard({
  year,
  levels,
  existing,
  preferredLevelId,
  onSubmitted,
}: {
  year: number
  levels: MembershipLevel[]
  existing?: SubscriptionRecord
  preferredLevelId?: string
  onSubmitted: () => void
}) {
  const [selectedLevelId, setSelectedLevelId] = useState(
    existing?.membershipLevelId ?? preferredLevelId ?? levels[0]?.id ?? '',
  )
  const [method, setMethod] = useState<'online' | 'bank_transfer'>('online')
  const [payingOnline, setPayingOnline] = useState(false)
  const [reference, setReference] = useState('')
  const [evidence, setEvidence] = useState<File | null>(null)
  const [submittingTransfer, setSubmittingTransfer] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const selectedLevel = levels.find((l) => l.id === selectedLevelId)

  if (existing?.status === 'Pending review') {
    return (
      <div className="border border-border bg-card p-6">
        <div className="flex flex-wrap items-start justify-between gap-4">
          <div>
            <h3 className="font-heading text-xl">{year} dues</h3>
            <p className="mt-2 text-[0.88rem] text-muted-foreground">
              {existing.membershipLevelName ?? 'Membership'} ·{' '}
              {formatNaira(existing.subscription + existing.welfare)}
            </p>
          </div>
          <StatusTag tone="warning">Pending review</StatusTag>
        </div>
        <p className="mt-4 text-[0.88rem] leading-relaxed text-muted-foreground">
          We received your bank transfer evidence
          {existing.bankTransferReference ? ` (reference ${existing.bankTransferReference})` : ''} and it&rsquo;s
          awaiting admin review. This usually takes a few working days.
        </p>
      </div>
    )
  }

  async function handlePayOnline() {
    if (!selectedLevelId) return
    setError(null)
    setPayingOnline(true)
    try {
      const { authorizationUrl } = await paySubscriptionDues(year, selectedLevelId)
      window.location.href = authorizationUrl
    } catch {
      setError('We could not start this payment. Please try again shortly.')
      setPayingOnline(false)
    }
  }

  async function handleSubmitTransfer(e: FormEvent<HTMLFormElement>) {
    e.preventDefault()
    if (!selectedLevelId || !evidence || !reference.trim()) return
    setError(null)
    setSubmittingTransfer(true)
    try {
      await submitBankTransfer(year, { membershipLevelId: selectedLevelId, reference: reference.trim(), evidence })
      onSubmitted()
    } catch {
      setError('We could not submit your evidence. Please try again shortly.')
      setSubmittingTransfer(false)
    }
  }

  return (
    <div className="border border-gold-500/50 bg-accent p-6">
      <h3 className="font-heading text-xl">{year} dues</h3>

      {existing?.reviewNote && (
        <p className="mt-3 border-l-2 border-destructive bg-destructive/8 px-4 py-3 text-[0.85rem] text-destructive">
          Your last submission was rejected: {existing.reviewNote} Please review and resubmit.
        </p>
      )}

      {levels.length === 0 ? (
        <p className="mt-3 text-[0.88rem] text-muted-foreground">
          No membership levels are available to choose from yet. Contact the chapter secretary.
        </p>
      ) : (
        <>
          <fieldset className="mt-4">
            <legend className="text-[0.85rem] font-medium">Choose your membership level</legend>
            <div className="mt-3 grid gap-3 sm:grid-cols-2">
              {levels.map((level) => (
                <label
                  key={level.id}
                  className={cn(
                    'cursor-pointer border p-4 transition-colors',
                    selectedLevelId === level.id
                      ? 'border-plum-700 bg-card dark:border-primary'
                      : 'border-border bg-card/60 hover:bg-card',
                  )}
                >
                  <input
                    type="radio"
                    name={`level-${year}`}
                    value={level.id}
                    checked={selectedLevelId === level.id}
                    onChange={() => setSelectedLevelId(level.id)}
                    className="sr-only"
                  />
                  <span className="block text-[0.93rem] font-medium">{level.name}</span>
                  {level.description && (
                    <span className="mt-1 block text-[0.78rem] text-muted-foreground">{level.description}</span>
                  )}
                  <span className="tnum mt-2 block font-heading text-lg text-plum-700 dark:text-primary">
                    {formatNaira(level.subscriptionAmount + level.welfareAmount)}
                  </span>
                </label>
              ))}
            </div>
          </fieldset>

          {error && (
            <p role="alert" className="mt-4 border-l-2 border-destructive bg-destructive/8 px-4 py-3 text-[0.85rem] text-destructive">
              {error}
            </p>
          )}

          <div className="mt-5 flex flex-wrap gap-3">
            <Button disabled={!selectedLevelId || payingOnline} onClick={handlePayOnline}>
              {payingOnline
                ? 'Taking you to payment…'
                : `Pay ${selectedLevel ? formatNaira(selectedLevel.subscriptionAmount + selectedLevel.welfareAmount) : ''} online`}
            </Button>
            <Button
              type="button"
              variant="outline"
              onClick={() => setMethod((m) => (m === 'bank_transfer' ? 'online' : 'bank_transfer'))}
            >
              {method === 'bank_transfer' ? 'Cancel bank transfer' : 'Pay by bank transfer instead'}
            </Button>
          </div>

          {method === 'bank_transfer' && (
            <form onSubmit={handleSubmitTransfer} className="mt-5 space-y-4 border-t border-border pt-5">
              <div className="space-y-2">
                <Label htmlFor={`ref-${year}`}>Your transfer reference</Label>
                <Input
                  id={`ref-${year}`}
                  value={reference}
                  onChange={(e) => setReference(e.target.value)}
                  placeholder="e.g. bank teller number or narration"
                  required
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor={`evidence-${year}`}>Evidence (receipt or screenshot)</Label>
                <Input
                  id={`evidence-${year}`}
                  type="file"
                  accept="image/jpeg,image/png,image/webp,application/pdf"
                  onChange={(e) => setEvidence(e.target.files?.[0] ?? null)}
                  required
                />
              </div>
              <Button type="submit" disabled={submittingTransfer || !selectedLevelId}>
                {submittingTransfer ? 'Submitting…' : 'Submit for review'}
              </Button>
            </form>
          )}
        </>
      )}
    </div>
  )
}

export default function MembersSubscription() {
  const { user } = useAuth()
  const [refreshKey, setRefreshKey] = useState(0)
  const { data: subscriptions, isLoading } = useApiData(
    fetchMySubscriptions,
    [] as SubscriptionRecord[],
    [refreshKey],
  )
  const { data: levels, isLoading: loadingLevels } = useApiData(getMembershipLevels, [] as MembershipLevel[])

  // The level chosen at registration (stored as the member's "ICAN
  // level") doubles as their default dues level until they pick a
  // different one — matches an existing subscription's own level first.
  const preferredLevelId = levels.find((l) => l.name === user?.credential)?.id
  const cardsReady = !isLoading && !loadingLevels

  const currentYear = new Date().getFullYear()
  const currentYearRecord = subscriptions.find((s) => s.year === currentYear)
  const paid = subscriptions.filter((s) => s.status === 'Paid')

  const yearsNeedingAction: { year: number; existing?: SubscriptionRecord }[] = []
  if (!currentYearRecord || currentYearRecord.status !== 'Paid') {
    yearsNeedingAction.push({ year: currentYear, existing: currentYearRecord })
  }
  subscriptions
    .filter((s) => s.year !== currentYear && s.status !== 'Paid')
    .forEach((s) => yearsNeedingAction.push({ year: s.year, existing: s }))

  return (
    <div className="space-y-12">
      <div>
        <SectionHeading
          title="Subscription and welfare levy"
          lede="Dues run on a calendar year. Paying keeps you on the active roll, which is what applies member rates and makes you eligible for office."
          className="mb-6"
        />

        {!cardsReady ? (
          <Skeleton className="h-32" />
        ) : yearsNeedingAction.length > 0 ? (
          <div className="space-y-4">
            {yearsNeedingAction.map(({ year, existing }) => (
              <DuesPaymentCard
                key={year}
                year={year}
                levels={levels}
                existing={existing}
                preferredLevelId={preferredLevelId}
                onSubmitted={() => setRefreshKey((k) => k + 1)}
              />
            ))}
          </div>
        ) : (
          <div className="border-l-2 border-success bg-success/8 px-5 py-4">
            <h3 className="text-[1.05rem]">Nothing outstanding</h3>
            <p className="mt-1.5 text-[0.9rem] text-muted-foreground">
              Your dues are current and you are on the active roll.
            </p>
          </div>
        )}
      </div>

      <div>
        <SectionHeading title="Payment history" className="mb-6" />
        {isLoading ? (
          <div className="space-y-3">
            {Array.from({ length: 4 }).map((_, i) => (
              <Skeleton key={i} className="h-10" />
            ))}
          </div>
        ) : (
        <div className="overflow-x-auto">
          <table className="w-full min-w-[40rem] text-left">
            <caption className="sr-only">Subscription payment history</caption>
            <thead>
              <tr className="border-b border-border text-[0.75rem] font-semibold text-muted-foreground">
                <th scope="col" className="py-2.5 pr-4">Year</th>
                <th scope="col" className="py-2.5 pr-4">Level</th>
                <th scope="col" className="py-2.5 pr-4">Subscription</th>
                <th scope="col" className="py-2.5 pr-4">Welfare</th>
                <th scope="col" className="py-2.5 pr-4">Total</th>
                <th scope="col" className="py-2.5 pr-4">Paid on</th>
                <th scope="col" className="py-2.5">Reference</th>
              </tr>
            </thead>
            <tbody>
              {paid.map((s) => (
                <tr key={s.id} className="border-b border-border last:border-0">
                  <td className="tnum py-3.5 pr-4 align-top font-medium">{s.year}</td>
                  <td className="py-3.5 pr-4 align-top text-[0.88rem] text-muted-foreground">
                    {s.membershipLevelName ?? '—'}
                  </td>
                  <td className="tnum py-3.5 pr-4 align-top text-[0.88rem]">
                    {formatNaira(s.subscription)}
                  </td>
                  <td className="tnum py-3.5 pr-4 align-top text-[0.88rem]">
                    {formatNaira(s.welfare)}
                  </td>
                  <td className="tnum py-3.5 pr-4 align-top text-[0.88rem] font-medium">
                    {formatNaira(s.subscription + s.welfare)}
                  </td>
                  <td className="tnum py-3.5 pr-4 align-top text-[0.88rem] text-muted-foreground">
                    {s.paidOn ? formatShortDate(s.paidOn) : '—'}
                  </td>
                  <td className="py-3.5 align-top">
                    <span className="tnum block text-[0.82rem] text-muted-foreground">
                      {s.reference ?? s.bankTransferReference}
                    </span>
                    <span className="mt-1 inline-block">
                      <StatusTag tone="positive">Paid</StatusTag>
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        )}
      </div>

      <div className="border border-border bg-card p-5">
        <h2 className="text-[1.05rem]">Where the welfare levy goes</h2>
        <p className="mt-2 max-w-[65ch] text-[0.9rem] leading-relaxed text-muted-foreground">
          The levy funds the chapter response when a member faces bereavement or illness, and pays
          for the community outreach the chapter commits to each year. Spending is published in the
          annual accounts.
        </p>
      </div>
    </div>
  )
}
