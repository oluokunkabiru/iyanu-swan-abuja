import { useState, type FormEvent } from 'react'
import { isAxiosError } from 'axios'
import { CheckCircle2, CreditCard, RefreshCw, Wallet } from 'lucide-react'
import { fetchDuesQuote, paySubscriptionDues, submitBankTransfer } from '@/api/auth'
import { StatusTag } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Dialog, DialogContent, DialogDescription, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Skeleton } from '@/components/ui/skeleton'
import { useAuth } from '@/context/AuthContext'
import { useDues } from '@/context/DuesContext'
import { useApiData } from '@/hooks/useApiData'
import { formatDuesAmount } from '@/lib/format'
import type { MembershipDuesQuote } from '@/types'

function paymentError(error: unknown): string {
  if (isAxiosError(error) && typeof error.response?.data?.message === 'string') return error.response.data.message
  return 'We could not complete this request. Please try again.'
}

export function DuesPaymentCard({ year, onSubmitted }: { year: number; onSubmitted?: () => void }) {
  const { user, refreshUser } = useAuth()
  const dues = useDues()
  const [selectedLevelId, setSelectedLevelId] = useState('')
  const [revision, setRevision] = useState(0)
  const [method, setMethod] = useState<'online' | 'bank_transfer'>('online')
  const [reference, setReference] = useState('')
  const [evidence, setEvidence] = useState<File | null>(null)
  const [busy, setBusy] = useState(false)
  const [error, setError] = useState('')
  const { data: quote, isLoading } = useApiData(
    () => fetchDuesQuote(year, selectedLevelId || undefined),
    null as MembershipDuesQuote | null,
    [year, selectedLevelId, revision, user?.dateOfBirth, user?.credential, dues.quote?.status],
  )

  function completed() {
    dues.refresh()
    setRevision(value => value + 1)
    onSubmitted?.()
  }

  async function payOnline() {
    if (!quote?.membershipLevelId || isLoading) return
    setBusy(true); setError('')
    try {
      const response = await paySubscriptionDues(year, quote.membershipLevelId)
      if (response.authorizationUrl) window.location.assign(response.authorizationUrl)
      else { await refreshUser(); completed() }
    } catch (error) { setError(paymentError(error)) }
    finally { setBusy(false) }
  }

  async function transfer(event: FormEvent) {
    event.preventDefault()
    if (!quote?.membershipLevelId || !evidence || !reference.trim() || isLoading) return
    if (evidence.size > 5 * 1024 * 1024) { setError('Choose a receipt up to 5 MB.'); return }
    setBusy(true); setError('')
    try {
      await submitBankTransfer(year, { membershipLevelId: quote.membershipLevelId, reference: reference.trim(), evidence })
      completed()
    } catch (error) { setError(paymentError(error)) }
    finally { setBusy(false) }
  }

  if (isLoading) return <Skeleton className="h-44 rounded-xl" />
  if (!quote) return <div role="alert" className="rounded-xl border border-border bg-card p-5 text-sm">Unable to load your dues. <Button variant="link" onClick={() => setRevision(value => value + 1)}>Try again</Button></div>
  if (quote.status === 'paid') return <div className="rounded-xl border border-border bg-card p-5"><StatusTag tone="positive">Paid</StatusTag><p className="mt-3 text-lg font-semibold">{year} balance: {formatDuesAmount(0)}</p></div>
  if (quote.status === 'pending_review') return <div className="rounded-xl border border-border bg-card p-5"><StatusTag tone="warning">Pending review</StatusTag><p className="mt-3 text-sm">Your {year} transfer of {formatDuesAmount(quote.total ?? 0)} is awaiting confirmation. You do not need to pay again.</p></div>

  return (
    <div className="rounded-xl border border-border bg-card p-5 sm:p-6">
      <h3 className="text-lg">{year} membership dues</h3>
      {quote.reviewNote && <p className="mt-3 rounded-lg bg-destructive/5 p-3 text-sm text-destructive">Your previous evidence was not approved: {quote.reviewNote}</p>}
      {(!dues.quote?.membershipLevelId || selectedLevelId) && (
        <div className="mt-4 space-y-2">
          <Label htmlFor={`dues-level-${year}`}>Membership level</Label>
          <select id={`dues-level-${year}`} value={quote.membershipLevelId ?? ''} onChange={event => { setSelectedLevelId(event.target.value); setEvidence(null); setReference(''); setError('') }} disabled={busy} className="h-10 w-full rounded-md border border-input bg-background px-3 text-sm">
            <option value="">Choose your membership level</option>
            {quote.options.map(level => <option key={level.id} value={level.id}>{level.name} — {formatDuesAmount(level.total)}</option>)}
          </select>
        </div>
      )}
      {quote.membershipLevelId ? (
        <>
          <p className="mt-4 text-sm font-semibold">{quote.membershipLevelName}</p>
          <dl className="mt-4 divide-y divide-border text-sm">
            <div className="flex justify-between gap-4 py-3"><dt className="text-muted-foreground">Subscription</dt><dd className="tnum font-medium">{formatDuesAmount(quote.subscription ?? 0)}</dd></div>
            <div className="flex justify-between gap-4 py-3"><dt className="text-muted-foreground">Welfare levy</dt><dd className="tnum font-medium">{formatDuesAmount(quote.welfare ?? 0)}</dd></div>
            <div className="flex justify-between gap-4 py-3"><dt className="font-semibold">Amount to pay</dt><dd className="tnum text-lg font-semibold">{formatDuesAmount(quote.balanceDue ?? 0)}</dd></div>
          </dl>
          {quote.subscriptionWaived && <p className="mt-2 rounded-lg bg-secondary px-3 py-2 text-xs leading-relaxed text-muted-foreground">Your subscription is waived because you are aged 65 or over. Only the welfare levy applies.</p>}
          {error && <p role="alert" className="mt-4 text-sm text-destructive">{error}</p>}
          <div className="mt-5 flex flex-wrap gap-3">
            <Button type="button" onClick={payOnline} disabled={busy}><CreditCard className="h-4 w-4" aria-hidden="true" />{busy ? 'Processing…' : quote.balanceDue === 0 ? 'Confirm zero balance' : `Pay ${formatDuesAmount(quote.balanceDue ?? 0)} online`}</Button>
            {(quote.balanceDue ?? 0) > 0 && <Button type="button" variant="outline" disabled={busy} onClick={() => setMethod(value => value === 'online' ? 'bank_transfer' : 'online')}>{method === 'bank_transfer' ? 'Cancel bank transfer' : 'Pay by bank transfer'}</Button>}
          </div>
          {method === 'bank_transfer' && (
            <form onSubmit={transfer} className="mt-5 space-y-4 border-t border-border pt-5">
              <p className="text-xs leading-relaxed text-muted-foreground">Transfer the amount shown above using the chapter’s bank details, then submit your reference and receipt for review.</p>
              <div className="space-y-2"><Label htmlFor={`dues-reference-${year}`}>Transfer reference</Label><Input id={`dues-reference-${year}`} value={reference} onChange={event => setReference(event.target.value)} maxLength={255} required disabled={busy} /></div>
              <div className="space-y-2"><Label htmlFor={`dues-evidence-${year}`}>Receipt or screenshot (up to 5 MB)</Label><Input id={`dues-evidence-${year}`} type="file" accept="image/jpeg,image/png,application/pdf" onChange={event => setEvidence(event.target.files?.[0] ?? null)} required disabled={busy} /></div>
              <Button type="submit" disabled={busy || !evidence || !reference.trim()}>{busy ? 'Submitting…' : 'Submit for review'}</Button>
            </form>
          )}
        </>
      ) : <p className="mt-4 text-sm text-muted-foreground">{quote.options.length ? 'Select your membership level to see the amount due.' : 'No membership fees have been configured. Please contact the chapter secretariat.'}</p>}
    </div>
  )
}

export function MembershipDuesSummary() {
  const { quote, isLoading, refresh } = useDues()
  const [open, setOpen] = useState(false)
  if (isLoading && !quote) return <Skeleton className="mb-6 h-32 rounded-xl" />
  if (!quote) return <div role="alert" className="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-border bg-card p-5 text-sm">Unable to load your dues balance.<Button variant="outline" size="sm" onClick={refresh}>Retry</Button></div>
  const paid = quote.status === 'paid'
  const pending = quote.status === 'pending_review'
  return (
    <section aria-label="Membership dues summary" className="mb-6 rounded-xl border border-border bg-card p-5 shadow-sm sm:p-6">
      <div className="flex flex-wrap items-center justify-between gap-5">
        <div className="flex items-center gap-4">
          <span className="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-secondary text-primary">{paid ? <CheckCircle2 className="h-6 w-6" aria-hidden="true" /> : <Wallet className="h-6 w-6" aria-hidden="true" />}</span>
          <div><p className="text-xs text-muted-foreground">{quote.year} membership dues · balance to pay</p><p className="tnum mt-1 text-2xl font-semibold sm:text-3xl">{quote.balanceDue === null ? 'Select membership level' : formatDuesAmount(quote.balanceDue)}</p></div>
        </div>
        <div className="flex items-center gap-3">
          <StatusTag tone={paid ? 'positive' : 'warning'}>{paid ? 'Paid' : pending ? 'Pending review' : 'Outstanding'}</StatusTag>
          <Button size="icon" variant="ghost" aria-label="Refresh dues balance" onClick={refresh} disabled={isLoading}><RefreshCw className="h-4 w-4" /></Button>
          {!paid && !pending && <Dialog open={open} onOpenChange={setOpen}><DialogTrigger asChild><Button disabled={isLoading}>Pay membership dues</Button></DialogTrigger><DialogContent className="max-h-[90dvh] overflow-y-auto sm:max-w-xl"><DialogTitle>Pay your membership dues</DialogTitle><DialogDescription>Review your subscription and welfare levy before continuing.</DialogDescription><DuesPaymentCard year={quote.year} onSubmitted={() => setOpen(false)} /></DialogContent></Dialog>}
        </div>
      </div>
      <p className="mt-4 text-xs leading-relaxed text-muted-foreground">{paid ? 'Your dues for this year are fully paid. Nothing more is due.' : pending ? 'Your transfer is awaiting confirmation. Please do not submit another payment.' : quote.subscriptionWaived ? `Subscription ${formatDuesAmount(0)} (65+ exemption) · Welfare ${formatDuesAmount(quote.welfare ?? 0)}.` : quote.subscription !== null ? `Subscription ${formatDuesAmount(quote.subscription)} · Welfare ${formatDuesAmount(quote.welfare ?? 0)}.` : 'Choose your membership level to calculate your dues.'}</p>
    </section>
  )
}
