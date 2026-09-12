import { CheckCircle2, Clock3, XCircle } from 'lucide-react'
import { useEffect, useState } from 'react'
import { useParams } from 'react-router-dom'
import { verifyTicket } from '@/api/content'
import { Section } from '@/components/common/Primitives'
import { formatDate } from '@/lib/format'
import type { TicketVerificationResult } from '@/types'

type Status = 'checking' | 'done' | 'error'

export default function TicketVerify() {
  const { reference } = useParams<{ reference: string }>()
  const [status, setStatus] = useState<Status>('checking')
  const [result, setResult] = useState<TicketVerificationResult | null>(null)

  useEffect(() => {
    if (!reference) {
      setStatus('error')
      return
    }

    let active = true
    verifyTicket(reference)
      .then((data) => {
        if (!active) return
        setResult(data)
        setStatus('done')
      })
      .catch(() => {
        if (active) setStatus('error')
      })

    return () => {
      active = false
    }
  }, [reference])

  const outcome = (() => {
    if (status === 'checking') {
      return { tone: 'neutral' as const, Icon: Clock3, heading: 'Checking ticket…' }
    }
    if (status === 'error' || !result) {
      return { tone: 'invalid' as const, Icon: XCircle, heading: 'Could not check this ticket' }
    }
    if (!result.valid) {
      return {
        tone: 'invalid' as const,
        Icon: XCircle,
        heading: result.reason === 'not_paid' ? 'NOT VALID — payment not completed' : 'Ticket not found',
      }
    }
    if (result.alreadyCheckedIn) {
      return { tone: 'warning' as const, Icon: Clock3, heading: 'Already checked in' }
    }
    return { tone: 'valid' as const, Icon: CheckCircle2, heading: 'VALID — checked in' }
  })()

  // This page exists for a door-staff glance check, where an unambiguous
  // pass/fail/warning signal is the whole point — not a brand surface. The
  // rest of the app deliberately has only navy/gold/white (see index.css),
  // which can't carry that distinction, so this page alone uses ordinary
  // green/amber/red instead of the brand's semantic tokens.
  const toneClasses = {
    neutral: 'border-border bg-card text-foreground',
    valid: 'border-green-600 bg-green-50 text-green-700 dark:border-green-500 dark:bg-green-950 dark:text-green-400',
    warning:
      'border-amber-500 bg-amber-50 text-amber-700 dark:border-amber-400 dark:bg-amber-950 dark:text-amber-400',
    invalid: 'border-red-600 bg-red-50 text-red-700 dark:border-red-500 dark:bg-red-950 dark:text-red-400',
  } as const

  return (
    <Section>
      <div className="mx-auto max-w-md">
        <div className={`border-2 px-6 py-10 text-center ${toneClasses[outcome.tone]}`}>
          <outcome.Icon className="mx-auto h-14 w-14" aria-hidden="true" />
          <h1 className="mt-4 text-xl font-bold uppercase tracking-tight">{outcome.heading}</h1>

          {status === 'done' && result?.name && (
            <dl className="mt-6 space-y-2 text-left text-[0.92rem] text-foreground">
              <div className="flex justify-between gap-3">
                <dt className="text-muted-foreground">Name</dt>
                <dd className="font-medium">{result.name}</dd>
              </div>
              {result.eventTitle && (
                <div className="flex justify-between gap-3">
                  <dt className="text-muted-foreground">Event</dt>
                  <dd className="font-medium">{result.eventTitle}</dd>
                </div>
              )}
              {result.ticketLabel && (
                <div className="flex justify-between gap-3">
                  <dt className="text-muted-foreground">Ticket</dt>
                  <dd className="font-medium">{result.ticketLabel}</dd>
                </div>
              )}
              {result.venue && (
                <div className="flex justify-between gap-3">
                  <dt className="text-muted-foreground">Venue</dt>
                  <dd className="font-medium">{result.venue}</dd>
                </div>
              )}
              {result.checkedInAt && (
                <div className="flex justify-between gap-3">
                  <dt className="text-muted-foreground">Checked in</dt>
                  <dd className="font-medium">{formatDate(result.checkedInAt)}</dd>
                </div>
              )}
            </dl>
          )}
        </div>
      </div>
    </Section>
  )
}
