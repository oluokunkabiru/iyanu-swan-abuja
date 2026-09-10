import { Link } from 'react-router-dom'
import { Award, CalendarDays, GraduationCap, Ticket, Wallet, ArrowRight } from 'lucide-react'
import { fetchMyCpdRecords, fetchMyRegistrations, fetchMySubscriptions } from '@/api/auth'
import { getEvents } from '@/api/content'
import { EmptyState, SectionHeading, StatusTag } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { useAuth } from '@/context/AuthContext'
import { useApiData } from '@/hooks/useApiData'
import { formatDate, formatNaira, formatShortDate } from '@/lib/format'
import { cn } from '@/lib/utils'
import type { ChapterEvent, CpdRecord, SubscriptionRecord, TicketRecord } from '@/types'

export default function MembersOverview() {
  const { user } = useAuth()
  const { data: cpdRecords, isLoading: loadingCpd } = useApiData(fetchMyCpdRecords, [] as CpdRecord[])
  const { data: subscriptions, isLoading: loadingSubscriptions } = useApiData(
    fetchMySubscriptions,
    [] as SubscriptionRecord[],
  )
  const { data: tickets, isLoading: loadingTickets } = useApiData(fetchMyRegistrations, [] as TicketRecord[])
  const { data: upcomingEvents } = useApiData(() => getEvents('upcoming'), [] as ChapterEvent[])

  if (!user) return null

  const isLoading = loadingCpd || loadingSubscriptions || loadingTickets
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
        {isLoading ? (
          <div className="grid gap-5 sm:grid-cols-3">
            {Array.from({ length: 3 }).map((_, i) => (
              <Skeleton key={i} className="h-44 rounded-xl" />
            ))}
          </div>
        ) : (
          <div className="grid gap-5 sm:grid-cols-3">
            <div className="rounded-xl border border-border bg-card p-5 shadow-sm transition-shadow hover:shadow-md">
              <div className="flex items-center justify-between">
                <span className="flex h-10 w-10 items-center justify-center rounded-full bg-accent text-accent-foreground">
                  <GraduationCap className="h-5 w-5" />
                </span>
                <span className="tnum text-[0.76rem] font-medium text-muted-foreground">
                  {cycleStart}–{new Date().getFullYear()}
                </span>
              </div>
              <p className="mt-4 text-[0.82rem] text-muted-foreground">CPD this cycle</p>
              <p className="tnum mt-1 font-heading text-3xl text-plum-700 dark:text-primary">
                {cycleHours}
                <span className="text-lg text-muted-foreground">/{user.cpdTarget}</span>
              </p>
              <div
                role="progressbar"
                aria-valuenow={progress}
                aria-valuemin={0}
                aria-valuemax={100}
                aria-label="CPD progress this cycle"
                className="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-border"
              >
                <div
                  className="h-full rounded-full bg-gold-500 transition-[width]"
                  style={{ width: `${progress}%` }}
                />
              </div>
              <p className="mt-2 text-[0.78rem] text-muted-foreground">{progress}% of target</p>
            </div>

            <div className="rounded-xl border border-border bg-card p-5 shadow-sm transition-shadow hover:shadow-md">
              <div className="flex items-center justify-between">
                <span
                  className={cn(
                    'flex h-10 w-10 items-center justify-center rounded-full',
                    outstanding ? 'bg-destructive/10 text-destructive' : 'bg-success/12 text-success',
                  )}
                >
                  <Wallet className="h-5 w-5" />
                </span>
                <StatusTag tone={outstanding ? 'warning' : 'positive'}>
                  {outstanding ? 'Outstanding' : 'Active roll'}
                </StatusTag>
              </div>
              <p className="mt-4 text-[0.82rem] text-muted-foreground">Dues</p>
              <p className="mt-1 font-heading text-2xl">
                {outstanding ? `${outstanding.year} outstanding` : 'Up to date'}
              </p>
              <p className="tnum mt-2 text-[0.85rem] text-muted-foreground">
                {outstanding
                  ? `${formatNaira(outstanding.subscription + outstanding.welfare)} due`
                  : 'No balance on your record'}
              </p>
            </div>

            <div className="rounded-xl border border-border bg-card p-5 shadow-sm transition-shadow hover:shadow-md">
              <div className="flex items-center justify-between">
                <span className="flex h-10 w-10 items-center justify-center rounded-full bg-secondary text-secondary-foreground">
                  <Ticket className="h-5 w-5" />
                </span>
              </div>
              <p className="mt-4 text-[0.82rem] text-muted-foreground">Tickets held</p>
              <p className="tnum mt-1 font-heading text-3xl text-plum-700 dark:text-primary">
                {tickets.length}
              </p>
              <p className="mt-2 text-[0.78rem] text-muted-foreground">
                {tickets.filter((t) => t.status === 'Pending').length} awaiting confirmation
              </p>
            </div>
          </div>
        )}
      </div>

      {outstanding && (
        <div className="flex flex-col gap-4 rounded-xl border border-gold-500/40 bg-accent/60 p-6 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 className="flex items-center gap-2 text-[1.05rem] font-semibold">
              <Wallet className="h-4 w-4 text-accent-foreground" aria-hidden="true" />
              Your {outstanding.year} dues are open
            </h2>
            <p className="mt-1.5 max-w-[60ch] text-[0.9rem] leading-relaxed text-muted-foreground">
              {formatNaira(outstanding.subscription)} subscription and{' '}
              {formatNaira(outstanding.welfare)} welfare. Paying keeps your member rate on events and
              your eligibility for committee service.
            </p>
          </div>
          <Button asChild className="shrink-0">
            <Link to="/members/subscription">
              Pay {outstanding.year} dues
              <ArrowRight className="h-4 w-4" aria-hidden="true" />
            </Link>
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
        {loadingCpd && (
          <div className="space-y-3">
            {Array.from({ length: 4 }).map((_, i) => (
              <Skeleton key={i} className="h-16 rounded-xl" />
            ))}
          </div>
        )}
        {!loadingCpd && cpdRecords.length === 0 && (
          <EmptyState
            title="No CPD logged yet"
            body="Activities you complete will show up here once the chapter secretary records them."
          />
        )}
        {!loadingCpd && cpdRecords.length > 0 && (
          <div className="overflow-hidden rounded-xl border border-border">
            <ul className="divide-y divide-border">
              {cpdRecords.slice(0, 4).map((r) => (
                <li
                  key={r.id}
                  className="flex flex-wrap items-center justify-between gap-3 bg-card px-5 py-4 transition-colors hover:bg-secondary/60"
                >
                  <div className="flex min-w-0 items-center gap-3">
                    <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-secondary text-secondary-foreground">
                      <Award className="h-4 w-4" />
                    </span>
                    <div className="min-w-0">
                      <p className="truncate text-[0.93rem] leading-snug">{r.activity}</p>
                      <p className="tnum mt-0.5 text-[0.8rem] text-muted-foreground">
                        {formatShortDate(r.date)} · {r.type}
                      </p>
                    </div>
                  </div>
                  <span className="tnum shrink-0 font-heading text-lg text-accent-foreground">
                    {r.hours} hrs
                  </span>
                </li>
              ))}
            </ul>
          </div>
        )}
      </div>

      {(nextTicket || nextEvent) && (
        <div className="grid gap-5 md:grid-cols-2">
          {nextTicket && (
            <div className="rounded-xl border border-border bg-card p-5 shadow-sm transition-shadow hover:shadow-md">
              <div className="flex items-center gap-2 text-[1.05rem] font-semibold">
                <Ticket className="h-4 w-4 text-plum-700 dark:text-primary" aria-hidden="true" />
                Your next ticket
              </div>
              <p className="mt-3 text-[0.92rem] leading-snug">{nextTicket.eventTitle}</p>
              <div className="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-[0.82rem] text-muted-foreground">
                <span>{nextTicket.tier}</span>
                <span aria-hidden="true">·</span>
                <span className="tnum">{nextTicket.reference}</span>
              </div>
              <Button variant="outline" asChild className="mt-4 w-full">
                <Link to="/members/tickets">All tickets</Link>
              </Button>
            </div>
          )}

          {nextEvent && (
            <div className="rounded-xl border border-border bg-card p-5 shadow-sm transition-shadow hover:shadow-md">
              <div className="flex items-center gap-2 text-[1.05rem] font-semibold">
                <CalendarDays className="h-4 w-4 text-plum-700 dark:text-primary" aria-hidden="true" />
                Next in the diary
              </div>
              <p className="mt-3 text-[0.92rem] leading-snug">{nextEvent.title}</p>
              <p className="tnum mt-2 text-[0.82rem] text-muted-foreground">
                {formatDate(nextEvent.startsAt)}
              </p>
              <Button variant="outline" asChild className="mt-4 w-full">
                <Link to={`/events/${nextEvent.slug}`}>See the event</Link>
              </Button>
            </div>
          )}
        </div>
      )}
    </div>
  )
}
