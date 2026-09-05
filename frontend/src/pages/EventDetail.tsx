import { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import { CalendarDays, Clock, MapPin } from 'lucide-react'
import { getEvent, getEvents, registerForEvent } from '@/api/content'
import { EmptyState, PageHeader, Section, SectionHeading, StatusTag } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { useAuth } from '@/context/AuthContext'
import { useApiData } from '@/hooks/useApiData'
import { formatDate, formatNaira, formatTimeRange } from '@/lib/format'
import { cn } from '@/lib/utils'
import type { ChapterEvent } from '@/types'

export default function EventDetail() {
  const { slug } = useParams<{ slug: string }>()
  const { user } = useAuth()

  const { data: event, isLoading } = useApiData(
    () => (slug ? getEvent(slug) : Promise.resolve(null)),
    null as ChapterEvent | null,
    [slug],
  )
  const { data: upcoming } = useApiData(() => getEvents('upcoming'), [] as ChapterEvent[])

  const [selectedTier, setSelectedTier] = useState<string | null>(null)
  const [registering, setRegistering] = useState(false)
  const [registered, setRegistered] = useState(false)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    setSelectedTier(null)
    setRegistered(false)
    setError(null)
  }, [slug])

  if (isLoading) {
    return (
      <Section>
        <Skeleton className="h-10 w-2/3" />
        <div className="mt-8 grid gap-12 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]">
          <Skeleton className="h-96" />
          <Skeleton className="h-64" />
        </div>
      </Section>
    )
  }

  if (!event) {
    return (
      <Section>
        <EmptyState
          title="Event not found"
          body="That event is not in the diary. It may have been rescheduled or removed."
          action={
            <Button variant="outline" asChild>
              <Link to="/events">Back to events</Link>
            </Button>
          }
        />
      </Section>
    )
  }

  const isMember = user?.membershipStatus === 'active'
  const relevantTiers = event.ticketTiers.filter((t) =>
    isMember ? t.audience === 'member' : t.audience === 'non-member',
  )
  const shownTiers = relevantTiers.length > 0 ? relevantTiers : event.ticketTiers
  const others = upcoming.filter((e) => e.id !== event.id).slice(0, 3)

  async function handleRegister() {
    if (!event || !selectedTier || !user) return
    setRegistering(true)
    setError(null)
    try {
      await registerForEvent(event.slug, {
        event_ticket_type_id: Number(selectedTier),
        name: user.name,
        email: user.email,
      })
      setRegistered(true)
    } catch {
      setError('Something went wrong submitting your registration. Please try again.')
    } finally {
      setRegistering(false)
    }
  }

  return (
    <>
      <PageHeader
        breadcrumb={[
          { label: 'Home', to: '/' },
          { label: 'Events', to: '/events' },
          { label: event.category },
        ]}
        title={event.title}
        intro={event.summary}
      />

      <Section>
        <div className="grid gap-12 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]">
          <article>
            {event.coverUrl && (
              <img
                src={event.coverUrl}
                alt=""
                className="mb-8 aspect-[16/9] w-full border border-border object-cover"
              />
            )}

            <div className="max-w-[70ch] space-y-4 text-[1rem] leading-relaxed">
              {event.body.map((para) => (
                <p key={para.slice(0, 40)}>{para}</p>
              ))}
            </div>

            {event.speakers.length > 0 && (
              <div className="mt-10">
                <SectionHeading title="Speakers and chairs" className="mb-6" />
                <ul className="divide-y divide-border border-y border-border">
                  {event.speakers.map((s) => (
                    <li key={s.name} className="flex flex-wrap items-baseline justify-between gap-2 py-3.5">
                      <span className="font-medium">{s.name}</span>
                      <span className="text-[0.85rem] text-muted-foreground">{s.role}</span>
                    </li>
                  ))}
                </ul>
              </div>
            )}
          </article>

          <aside className="space-y-6">
            <div className="border border-border bg-card p-6">
              <h2 className="text-[1.05rem]">Details</h2>
              <dl className="mt-4 space-y-3.5 text-[0.9rem]">
                <div className="flex gap-3">
                  <dt className="sr-only">Date</dt>
                  <CalendarDays aria-hidden="true" className="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                  <dd>{formatDate(event.startsAt)}</dd>
                </div>
                <div className="flex gap-3">
                  <dt className="sr-only">Time</dt>
                  <Clock aria-hidden="true" className="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                  <dd className="tnum">{formatTimeRange(event.startsAt, event.endsAt)}</dd>
                </div>
                <div className="flex gap-3">
                  <dt className="sr-only">Venue</dt>
                  <MapPin aria-hidden="true" className="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                  <dd>{event.venue}</dd>
                </div>
              </dl>
              <div className="mt-4 flex flex-wrap gap-2">
                <StatusTag tone="neutral">{event.category}</StatusTag>
                {event.cpdHours > 0 && <StatusTag tone="gold">{event.cpdHours} CPD hours</StatusTag>}
                {event.status === 'past' && <StatusTag tone="neutral">Concluded</StatusTag>}
              </div>
            </div>

            {event.ticketTiers.length > 0 && event.status === 'upcoming' && (
              <div className="border border-border bg-card p-6">
                <h2 className="text-[1.05rem]">Register</h2>

                {registered ? (
                  <p className="mt-2 text-[0.88rem] leading-relaxed text-muted-foreground">
                    You&rsquo;re registered. A confirmation has been sent to {user?.email}.
                  </p>
                ) : (
                  <>
                    <p className="mt-2 text-[0.85rem] leading-relaxed text-muted-foreground">
                      {isMember
                        ? 'Your member rate is applied below.'
                        : 'Sign in with an active membership to see member rates.'}
                    </p>

                    <ul className="mt-4 space-y-2">
                      {shownTiers.map((tier) => (
                        <li key={tier.id}>
                          <button
                            type="button"
                            onClick={() => setSelectedTier(tier.id)}
                            aria-pressed={selectedTier === tier.id}
                            className={cn(
                              'flex w-full items-baseline justify-between gap-3 rounded-sm border px-4 py-3 text-left transition-colors',
                              selectedTier === tier.id
                                ? 'border-plum-700 bg-secondary dark:border-primary'
                                : 'border-border hover:border-plum-500',
                            )}
                          >
                            <span className="text-[0.86rem] leading-snug">{tier.label}</span>
                            <span className="tnum shrink-0 font-heading text-lg text-plum-700 dark:text-primary">
                              {formatNaira(tier.price)}
                            </span>
                          </button>
                        </li>
                      ))}
                    </ul>

                    {error && <p className="mt-3 text-[0.82rem] text-destructive">{error}</p>}

                    <Button
                      className="mt-5 w-full"
                      disabled={!selectedTier || !user || registering}
                      onClick={handleRegister}
                    >
                      {registering ? 'Submitting…' : selectedTier ? 'Continue to payment' : 'Choose a ticket'}
                    </Button>

                    {!user && (
                      <p className="mt-3 text-center text-[0.82rem] text-muted-foreground">
                        <Link to="/login" className="font-semibold text-plum-700 underline-offset-4 hover:underline dark:text-primary">
                          Sign in
                        </Link>{' '}
                        for the member rate
                      </p>
                    )}
                  </>
                )}
              </div>
            )}

            {event.status === 'past' && (
              <div className="border border-border bg-card p-6">
                <h2 className="text-[1.05rem]">After the event</h2>
                <p className="mt-2 text-[0.88rem] leading-relaxed text-muted-foreground">
                  Photographs are in the gallery, and any communiqué issued is filed in the
                  publications library.
                </p>
                <div className="mt-4 flex flex-col gap-2">
                  <Button variant="outline" asChild>
                    <Link to="/gallery">See the gallery</Link>
                  </Button>
                  <Button variant="outline" asChild>
                    <Link to="/publications">Publications library</Link>
                  </Button>
                </div>
              </div>
            )}
          </aside>
        </div>
      </Section>

      {others.length > 0 && (
        <Section tone="tinted">
          <SectionHeading title="Also in the diary" className="mb-8" />
          <ul className="grid gap-px bg-border md:grid-cols-3">
            {others.map((e) => (
              <li key={e.id} className="bg-card p-5">
                <p className="tnum text-[0.8rem] text-muted-foreground">{formatDate(e.startsAt)}</p>
                <h3 className="mt-2 text-[1rem] leading-snug">
                  <Link to={`/events/${e.slug}`} className="hover:text-plum-700 dark:hover:text-primary">
                    {e.title}
                  </Link>
                </h3>
              </li>
            ))}
          </ul>
        </Section>
      )}
    </>
  )
}
