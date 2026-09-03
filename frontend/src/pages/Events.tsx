import { useSearchParams } from 'react-router-dom'
import { EventCard } from '@/components/common/Cards'
import { EmptyState, PageHeader, Section, SectionHeading, StatusTag } from '@/components/common/Primitives'
import { events, standardTiers } from '@/data'
import { formatNaira } from '@/lib/format'
import { cn } from '@/lib/utils'

const filters = [
  { key: 'all', label: 'All events' },
  { key: 'upcoming', label: 'Upcoming' },
  { key: 'past', label: 'Past' },
] as const

export default function Events() {
  const [params, setParams] = useSearchParams()
  const when = params.get('when') ?? 'all'

  const visible = events.filter((e) => (when === 'all' ? true : e.status === when))

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
          lede={`${visible.length} ${visible.length === 1 ? 'event' : 'events'}.`}
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

        {visible.length === 0 ? (
          <div className="mt-8">
            <EmptyState
              title="Nothing in this view yet"
              body="Switch to another filter, or check the training calendar for accredited sessions run with the faculties."
            />
          </div>
        ) : (
          <div className="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {visible.map((e) => (
              <EventCard key={e.id} event={e} />
            ))}
          </div>
        )}
      </Section>

      <Section tone="tinted">
        <SectionHeading
          id="pricing"
          title="Event pricing"
          lede="The chapter runs four tiers on paid technical events. Members save roughly forty per cent on the in-person rate."
          className="mb-8"
        />
        <div className="grid gap-px bg-border sm:grid-cols-2 lg:grid-cols-4">
          {standardTiers.map((tier) => (
            <div key={tier.id} className="flex flex-col bg-card p-6">
              <StatusTag tone={tier.audience === 'member' ? 'gold' : 'neutral'}>
                {tier.audience === 'member' ? 'Member' : 'Non-member'}
              </StatusTag>
              <h3 className="mt-3 text-[1.02rem] capitalize">{tier.mode} attendance</h3>
              <p className="tnum mt-3 font-heading text-3xl text-plum-700 dark:text-primary">
                {formatNaira(tier.price)}
              </p>
              <p className="mt-1 text-[0.78rem] text-muted-foreground">per delegate</p>
              <ul className="mt-5 space-y-2 text-[0.84rem] text-muted-foreground">
                {tier.includes.map((inc) => (
                  <li key={inc} className="border-l border-gold-500/60 pl-3 leading-snug">
                    {inc}
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>
      </Section>
    </>
  )
}
