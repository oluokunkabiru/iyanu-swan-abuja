import { useState } from 'react'
import { DocumentRow } from '@/components/common/Cards'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { publications } from '@/data'
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
        <SectionHeading title="Library" lede={`${visible.length} documents.`} className="mb-6" />
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

        <ul className="mt-8 divide-y divide-border border-y border-border">
          {visible.map((p) => (
            <DocumentRow key={p.id} item={p} />
          ))}
        </ul>
      </Section>
    </>
  )
}
