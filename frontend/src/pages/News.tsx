import { useState } from 'react'
import { getNews } from '@/api/content'
import { NewsCard } from '@/components/common/Cards'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Skeleton } from '@/components/ui/skeleton'
import { useApiData } from '@/hooks/useApiData'
import { cn } from '@/lib/utils'
import type { NewsPost } from '@/types'

const categories: (NewsPost['category'] | 'All')[] = ['All', 'Chapter', 'ICAN', 'Profession', 'Advocacy']

export default function News() {
  const [category, setCategory] = useState<(typeof categories)[number]>('All')
  const { data: news, isLoading } = useApiData(getNews, [] as NewsPost[])
  const visible = category === 'All' ? news : news.filter((n) => n.category === category)
  const [lead, ...rest] = visible

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'News' }]}
        title="Chapter news"
        intro="What the chapter has done, published and argued for."
      />

      <Section>
        <SectionHeading title="Latest" className="mb-6" />
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
          <div className="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {Array.from({ length: 6 }).map((_, i) => (
              <Skeleton key={i} className="h-64" />
            ))}
          </div>
        ) : (
          <>
            {lead && (
              <div className="mt-8">
                <NewsCard post={lead} featured />
              </div>
            )}

            {rest.length > 0 && (
              <div className="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                {rest.map((post) => (
                  <NewsCard key={post.id} post={post} />
                ))}
              </div>
            )}

            {visible.length === 0 && (
              <p className="mt-8 border border-dashed border-rule px-6 py-10 text-center text-[0.9rem] text-muted-foreground">
                Nothing filed under that category yet.
              </p>
            )}
          </>
        )}
      </Section>
    </>
  )
}
