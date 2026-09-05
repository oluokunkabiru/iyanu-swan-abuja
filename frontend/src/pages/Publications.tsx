import { useState } from 'react'
import { getPublications } from '@/api/content'
import { DocumentRow } from '@/components/common/Cards'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Skeleton } from '@/components/ui/skeleton'
import { useApiData } from '@/hooks/useApiData'
import { cn } from '@/lib/utils'
import type { Publication } from '@/types'

const categories: (Publication['category'] | 'All')[] = [
  'All',
  'Communiqué',
  'Newsletter',
  'Technical',
  'Report',
  'Address',
]

export default function Publications() {
  const [category, setCategory] = useState<(typeof categories)[number]>('All')
  const { data: publications, isLoading } = useApiData(getPublications, [] as Publication[])
  const visible =
    category === 'All' ? publications : publications.filter((p) => p.category === category)

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'Publications' }]}
        title="Publications library"
        intro="Communiqués, technical bulletins, newsletters and the annual report. Everything the chapter publishes is filed here."
      />

      <Section>
        <SectionHeading
          title="Library"
          lede={isLoading ? 'Loading…' : `${visible.length} documents.`}
          className="mb-6"
        />
        <div className="flex flex-wrap gap-2">
          {categories.map((c) => (
            <button
              key={c}
              type="button"
              onClick={() => setCategory(c)}
              aria-pressed={category === c}
              className={cn(
                'rounded-sm border px-3 py-1.5 text-[0.84rem] font-medium transition-colors',
                category === c
                  ? 'border-plum-700 bg-plum-700 text-white dark:border-primary dark:bg-primary dark:text-primary-foreground'
                  : 'border-border bg-card text-muted-foreground hover:text-foreground',
              )}
            >
              {c}
            </button>
          ))}
        </div>

        {isLoading ? (
          <div className="mt-8 space-y-3">
            {Array.from({ length: 5 }).map((_, i) => (
              <Skeleton key={i} className="h-14" />
            ))}
          </div>
        ) : (
          <ul className="mt-8 divide-y divide-border border-y border-border">
            {visible.map((p) => (
              <DocumentRow key={p.id} item={p} />
            ))}
          </ul>
        )}
      </Section>
    </>
  )
}
