import { Link } from 'react-router-dom'
import { SectionHeading, StatusTag } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { useAuth } from '@/context/AuthContext'
import { cpdRecords, subscriptions, tickets, upcomingEvents } from '@/data'
import { formatDate, formatNaira, formatShortDate } from '@/lib/format'

export default function MembersOverview() {
  const { user } = useAuth()
  if (!user) return null

  const cycleStart = new Date().getFullYear() - 2
  const cycleHours = cpdRecords
    .filter((r) => new Date(r.date).getFullYear() >= cycleStart)
    .reduce((sum, r) => sum + r.hours, 0)
  const progress = Math.min(100, Math.round((cycleHours / user.cpdTarget) * 100))

  const outstanding = subscriptions.find((s) => s.status === 'Outstanding')
  const nextTicket = tickets.find((t) => t.status === 'Confirmed')
  const nextEvent = upcomingEvents[0]

  return (
    <div className="space-y-12">
      <div>
        <SectionHeading title="Where you stand" className="mb-6" />
        <div className="grid gap-px bg-border sm:grid-cols-3">
          <div className="bg-card p-5">
            <p className="text-[0.82rem] text-muted-foreground">CPD this cycle</p>
            <p className="tnum mt-2 font-heading text-3xl text-plum-700 dark:text-primary">
              {cycleHours}
              <span className="text-lg text-muted-foreground">/{user.cpdTarget}</span>
            </p>
            <div
              role="progressbar"
              aria-valuenow={progress}
              aria-valuemin={0}
              aria-valuemax={100}
              aria-label="CPD progress this cycle"
              className="mt-3 h-1.5 w-full bg-border"
            >
              <div className="h-full bg-gold-500" style={{ width: `${progress}%` }} />
            </div>
            <p className="mt-2 text-[0.78rem] text-muted-foreground">
              {cycleStart} to {new Date().getFullYear()}
            </p>
          </div>

          <div className="bg-card p-5">
            <p className="text-[0.82rem] text-muted-foreground">Dues</p>
            <p className="mt-2 font-heading text-2xl">
              {outstanding ? `${outstanding.year} outstanding` : 'Up to date'}
            </p>
            <div className="mt-3">
              <StatusTag tone={outstanding ? 'warning' : 'positive'}>
                {outstanding
                  ? `${formatNaira(outstanding.subscription + outstanding.welfare)} due`
                  : 'Active roll'}
              </StatusTag>
            </div>
          </div>

          <div className="bg-card p-5">
            <p className="text-[0.82rem] text-muted-foreground">Tickets held</p>
            <p className="tnum mt-2 font-heading text-3xl text-plum-700 dark:text-primary">
              {tickets.length}
            </p>
            <p className="mt-2 text-[0.78rem] text-muted-foreground">
              {tickets.filter((t) => t.status === 'Pending').length} awaiting confirmation
            </p>
          </div>
        </div>
      </div>

      {outstanding && (
        <div className="border-l-2 border-gold-500 bg-accent px-5 py-4">
          <h2 className="text-[1.05rem]">Your {outstanding.year} dues are open</h2>
          <p className="mt-1.5 max-w-[60ch] text-[0.9rem] leading-relaxed text-muted-foreground">
            {formatNaira(outstanding.subscription)} subscription and{' '}
            {formatNaira(outstanding.welfare)} welfare. Paying keeps your member rate on events and
            your eligibility for committee service.
          </p>
          <Button asChild className="mt-4">
            <Link to="/members/subscription">Pay {outstanding.year} dues</Link>
          </Button>
        </div>
      )}

      <div>
        <SectionHeading
          title="Recent CPD"
          action={
            <Button variant="outline" size="sm" asChild>
              <Link to="/members/cpd">Full record</Link>
            </Button>
          }
          className="mb-6"
        />
        <ul className="divide-y divide-border border-y border-border">
          {cpdRecords.slice(0, 4).map((r) => (
            <li key={r.id} className="flex flex-wrap items-baseline justify-between gap-3 py-3.5">
              <div className="min-w-0">
                <p className="text-[0.93rem] leading-snug">{r.activity}</p>
                <p className="tnum mt-1 text-[0.8rem] text-muted-foreground">
                  {formatShortDate(r.date)} · {r.type}
                </p>
              </div>
              <span className="tnum shrink-0 font-heading text-lg text-accent-foreground">{r.hours} hrs</span>
            </li>
          ))}
        </ul>
      </div>

      <div className="grid gap-8 md:grid-cols-2">
        {nextTicket && (
          <div className="border border-border bg-card p-5">
            <h2 className="text-[1.05rem]">Your next ticket</h2>
            <p className="mt-2 text-[0.92rem] leading-snug">{nextTicket.eventTitle}</p>
            <p className="mt-2 text-[0.82rem] text-muted-foreground">{nextTicket.tier}</p>
            <p className="tnum mt-1 text-[0.82rem] text-muted-foreground">{nextTicket.reference}</p>
            <Button variant="outline" asChild className="mt-4 w-full">
              <Link to="/members/tickets">All tickets</Link>
            </Button>
          </div>
        )}

        {nextEvent && (
          <div className="border border-border bg-card p-5">
            <h2 className="text-[1.05rem]">Next in the diary</h2>
            <p className="mt-2 text-[0.92rem] leading-snug">{nextEvent.title}</p>
            <p className="tnum mt-2 text-[0.82rem] text-muted-foreground">
              {formatDate(nextEvent.startsAt)}
            </p>
            <Button variant="outline" asChild className="mt-4 w-full">
              <Link to={`/events/${nextEvent.slug}`}>See the event</Link>
            </Button>
          </div>
        )}
      </div>
    </div>
  )
}
