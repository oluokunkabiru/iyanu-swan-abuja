import { useMemo } from 'react'
import { useSearchParams } from 'react-router-dom'
import { motion } from 'framer-motion'
import { getEvents } from '@/api/content'
import { EventCard } from '@/components/common/Cards'
import { EmptyState, PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Skeleton } from '@/components/ui/skeleton'
import { useApiData } from '@/hooks/useApiData'
import { containerVariants, itemVariants } from '@/lib/animations'
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
  const type = params.get('type') ?? 'all'

  function update(next: Record<string, string | null>) {
    const merged = new URLSearchParams(params)
    for (const [key, value] of Object.entries(next)) {
      if (value === null) merged.delete(key)
      else merged.set(key, value)
    }
    setParams(merged)
  }

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

  const types = useMemo(() => Array.from(new Set(events.map((e) => e.category))).sort(), [events])
  const visible = type === 'all' ? events : events.filter((e) => e.category === type)
  // With every type showing, the diary is arranged under a heading per event type.
  const groups = useMemo(
    () =>
      type === 'all'
        ? types.map((t) => ({ type: t, events: visible.filter((e) => e.category === t) }))
        : [{ type, events: visible }],
    [type, types, visible],
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
          lede={isLoading ? 'Loading…' : `${visible.length} ${visible.length === 1 ? 'event' : 'events'}.`}
          className="mb-6"
        />

        <div className="flex flex-wrap gap-2">
          {filters.map((f) => (
            <button
              key={f.key}
              type="button"
              onClick={() => update({ when: f.key === 'all' ? null : f.key })}
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

        {types.length > 1 && (
          <div className="mt-3 flex flex-wrap items-center gap-2">
            <span className="mr-1 text-[0.8rem] font-semibold text-muted-foreground">Event type</span>
            {['all', ...types].map((t) => (
              <button
                key={t}
                type="button"
                onClick={() => update({ type: t === 'all' ? null : t })}
                aria-pressed={type === t}
                className={cn(
                  'rounded-sm border px-3 py-1.5 text-[0.84rem] font-medium transition-colors',
                  type === t
                    ? 'border-plum-700 bg-plum-700 text-white dark:border-primary dark:bg-primary dark:text-primary-foreground'
                    : 'border-border bg-card text-muted-foreground hover:text-foreground',
                )}
              >
                {t === 'all' ? 'All types' : t}
              </button>
            ))}
          </div>
        )}

        {isLoading ? (
          <div className="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {Array.from({ length: 6 }).map((_, i) => (
              <Skeleton key={i} className="h-64" />
            ))}
          </div>
        ) : visible.length === 0 ? (
          <div className="mt-8">
            <EmptyState
              title="Nothing in this view yet"
              body="Switch to another filter, or check the training calendar for accredited sessions run with the faculties."
            />
          </div>
        ) : (
          <div className="mt-8 space-y-12">
            {groups.map((group) => (
              <section key={group.type} aria-labelledby={`events-${group.type}`}>
                {type === 'all' && (
                  <h2 id={`events-${group.type}`} className="mb-5 border-b border-border pb-2 text-[1.15rem]">
                    {group.type}
                    <span className="tnum ml-2 text-[0.85rem] font-normal text-muted-foreground">{group.events.length}</span>
                  </h2>
                )}
                <motion.div
                  className="grid gap-6 md:grid-cols-2 lg:grid-cols-3"
                  initial="initial"
                  whileInView="animate"
                  viewport={{ once: true, amount: 0.1 }}
                  variants={containerVariants}
                >
                  {group.events.map((e) => (
                    <motion.div key={e.id} variants={itemVariants}>
                      <EventCard event={e} />
                    </motion.div>
                  ))}
                </motion.div>
              </section>
            ))}
          </div>
        )}
      </Section>
    </>
  )
}
