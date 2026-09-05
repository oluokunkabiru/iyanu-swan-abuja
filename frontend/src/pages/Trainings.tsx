import { useMemo, useState } from 'react'
import { getTrainings } from '@/api/content'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { TrainingRow } from '@/components/common/Cards'
import { Skeleton } from '@/components/ui/skeleton'
import { useApiData } from '@/hooks/useApiData'
import { cn } from '@/lib/utils'
import type { Training } from '@/types'

const modes: (Training['deliveryMode'] | 'All')[] = ['All', 'Physical', 'Virtual', 'Hybrid']
const providers: (Training['provider'] | 'All')[] = ['All', 'SWAN Abuja', 'ICAN MPD', 'Faculty']

export default function Trainings() {
  const { data: trainings, isLoading } = useApiData(getTrainings, [] as Training[])
  const [mode, setMode] = useState<(typeof modes)[number]>('All')
  const [provider, setProvider] = useState<(typeof providers)[number]>('All')

  const visible = useMemo(
    () =>
      trainings.filter(
        (t) =>
          (mode === 'All' || t.deliveryMode === mode) &&
          (provider === 'All' || t.provider === provider),
      ),
    [trainings, mode, provider],
  )

  const totalHours = visible.reduce((sum, t) => sum + t.cpdHours, 0)

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'CPD', to: '/cpd' }, { label: 'Training calendar' }]}
        title="Training calendar"
        intro="Everything on this calendar is accredited. Member rates apply automatically when you are signed in."
      />

      <Section>
        <SectionHeading
          title="Sessions open for registration"
          lede={isLoading ? 'Loading…' : `${visible.length} sessions, ${totalHours} CPD hours in total.`}
          className="mb-6"
        />

        <div className="flex flex-wrap gap-6">
          <fieldset>
            <legend className="mb-2 text-[0.8rem] font-semibold text-muted-foreground">Delivery</legend>
            <div className="flex flex-wrap gap-2">
              {modes.map((m) => (
                <button
                  key={m}
                  type="button"
                  onClick={() => setMode(m)}
                  aria-pressed={mode === m}
                  className={cn(
                    'rounded-sm border px-3 py-1.5 text-[0.84rem] font-medium transition-colors',
                    mode === m
                      ? 'border-plum-700 bg-plum-700 text-white dark:border-primary dark:bg-primary dark:text-primary-foreground'
                      : 'border-border bg-card text-muted-foreground hover:text-foreground',
                  )}
                >
                  {m}
                </button>
              ))}
            </div>
          </fieldset>

          <fieldset>
            <legend className="mb-2 text-[0.8rem] font-semibold text-muted-foreground">Provider</legend>
            <div className="flex flex-wrap gap-2">
              {providers.map((p) => (
                <button
                  key={p}
                  type="button"
                  onClick={() => setProvider(p)}
                  aria-pressed={provider === p}
                  className={cn(
                    'rounded-sm border px-3 py-1.5 text-[0.84rem] font-medium transition-colors',
                    provider === p
                      ? 'border-plum-700 bg-plum-700 text-white dark:border-primary dark:bg-primary dark:text-primary-foreground'
                      : 'border-border bg-card text-muted-foreground hover:text-foreground',
                  )}
                >
                  {p}
                </button>
              ))}
            </div>
          </fieldset>
        </div>

        {isLoading ? (
          <div className="mt-8 space-y-3">
            {Array.from({ length: 5 }).map((_, i) => (
              <Skeleton key={i} className="h-12" />
            ))}
          </div>
        ) : (
          <div className="mt-8 overflow-x-auto">
            <table className="w-full min-w-[44rem] text-left">
              <caption className="sr-only">Training sessions</caption>
              <thead>
                <tr className="border-b border-border text-[0.75rem] font-semibold text-muted-foreground">
                  <th scope="col" className="py-2.5 pr-4">Session</th>
                  <th scope="col" className="py-2.5 pr-4">Date</th>
                  <th scope="col" className="py-2.5 pr-4">CPD</th>
                  <th scope="col" className="py-2.5 pr-4">Member / standard fee</th>
                  <th scope="col" className="py-2.5">Availability</th>
                </tr>
              </thead>
              <tbody>
                {visible.map((t) => (
                  <TrainingRow key={t.id} training={t} />
                ))}
              </tbody>
            </table>
          </div>
        )}

        {!isLoading && visible.length === 0 && (
          <p className="mt-6 border border-dashed border-rule px-6 py-10 text-center text-[0.9rem] text-muted-foreground">
            No sessions match those filters. Widen the delivery mode or provider to see more.
          </p>
        )}
      </Section>
    </>
  )
}
