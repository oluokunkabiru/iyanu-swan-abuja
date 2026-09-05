import { useSearchParams } from 'react-router-dom'
import { getEvents } from '@/api/content'
import { EventCard } from '@/components/common/Cards'
import { EmptyState, PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Skeleton } from '@/components/ui/skeleton'
import { useApiData } from '@/hooks/useApiData'
import type { ChapterEvent } from '@/types'
import { cn } from '@/lib/utils'

const filters = [
  { key: 'all', label: 'All events' },
  { key: 'upcoming', label: 'Upcoming' },
  { key: 'past', label: 'Past' },
] as const

export default function Events() {
  const [params, setParams] = useSearchParams()
  const when = params.get('when') ?? 'all'

  const { data: events, isLoading } = useApiData(
    () =>
      when === 'past'
        ? getEvents('past')
        : when === 'upcoming'
          ? getEvents('upcoming')
          : Promise.all([getEvents('upcoming'), getEvents('past')]).then(([a, b]) => [...a, ...b]),
    [] as ChapterEvent[],
    [when],
  )

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'Events' }]}
        title="Events"
        intro="Technical seminars, community outreach and the chapter meeting calendar. Sign in before you check out and the member rate applies automatically."
      />

      <Section>
        <SectionHeading
          title={when === 'past' ? 'Past events' : when === 'upcoming' ? 'Open for registration' : 'The events diary'}
          lede={isLoading ? 'Loading…' : `${events.length} ${events.length === 1 ? 'event' : 'events'}.`}
          className="mb-6"
        />

        <div className="flex flex-wrap gap-2">
          {filters.map((f) => (
            <button
              key={f.key}
              type="button"
              onClick={() => setParams(f.key === 'all' ? {} : { when: f.key })}
              aria-pressed={when === f.key}
              className={cn(
                'rounded-sm border px-3 py-1.5 text-[0.84rem] font-medium transition-colors',
                when === f.key
                  ? 'border-plum-700 bg-plum-700 text-white dark:border-primary dark:bg-primary dark:text-primary-foreground'
                  : 'border-border bg-card text-muted-foreground hover:text-foreground',
              )}
            >
              {f.label}
            </button>
          ))}
        </div>

        {isLoading ? (
          <div className="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {Array.from({ length: 6 }).map((_, i) => (
              <Skeleton key={i} className="h-64" />
            ))}
          </div>
        ) : events.length === 0 ? (
          <div className="mt-8">
            <EmptyState
              title="Nothing in this view yet"
              body="Switch to another filter, or check the training calendar for accredited sessions run with the faculties."
            />
          </div>
        ) : (
          <div className="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {events.map((e) => (
              <EventCard key={e.id} event={e} />
            ))}
          </div>
        )}
      </Section>
    </>
  )
}
