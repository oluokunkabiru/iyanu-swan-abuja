import { useState } from 'react'
import { getResources } from '@/api/content'
import { DocumentRow } from '@/components/common/Cards'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Skeleton } from '@/components/ui/skeleton'
import { useApiData } from '@/hooks/useApiData'
import { cn } from '@/lib/utils'
import type { ResourceItem } from '@/types'

const categories: (ResourceItem['category'] | 'All')[] = [
  'All',
  'Form',
  'Guide',
  'Policy',
  'Template',
  'Syllabus',
]

export default function Resources() {
  const [category, setCategory] = useState<(typeof categories)[number]>('All')
  const { data: resources, isLoading } = useApiData(getResources, [] as ResourceItem[])
  const visible = category === 'All' ? resources : resources.filter((r) => r.category === category)

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'Forms and downloads' }]}
        title="Forms and downloads"
        intro="Registration forms, practice procedures, chapter policies and the templates members ask for most."
      />

      <Section>
        <SectionHeading
          title="Downloads"
          lede={isLoading ? 'Loading…' : `${visible.length} files.`}
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
            {visible.map((r) => (
              <DocumentRow key={r.id} item={r} />
            ))}
          </ul>
        )}
      </Section>
    </>
  )
}
