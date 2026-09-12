import { useMemo, useState } from 'react'
import { Link } from 'react-router-dom'
import { getJobs } from '@/api/content'
import { PageHeader, Section, SectionHeading, StatusTag } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { useApiData } from '@/hooks/useApiData'
import { formatShortDate } from '@/lib/format'
import { cn } from '@/lib/utils'
import type { JobListing } from '@/types'

const levels: (JobListing['level'] | 'All')[] = ['All', 'Entry', 'Mid', 'Senior', 'Executive']

export default function Jobs() {
  const [level, setLevel] = useState<(typeof levels)[number]>('All')
  const { data: jobs, isLoading } = useApiData(getJobs, [] as JobListing[])
  const visible = useMemo(
    () => (level === 'All' ? jobs : jobs.filter((j) => j.level === level)),
    [jobs, level],
  )

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'Job centre' }]}
        title="Job centre"
        intro="Roles shared with the chapter by members and partner organisations. Listings are removed on their closing date."
      />

      <Section>
        <SectionHeading
          title="Open roles"
          lede={isLoading ? 'Loading…' : `${visible.length} listings.`}
          className="mb-6"
        />

        <div className="flex flex-wrap gap-2">
          {levels.map((l) => (
            <button
              key={l}
              type="button"
              onClick={() => setLevel(l)}
              aria-pressed={level === l}
              className={cn(
                'rounded-sm border px-3 py-1.5 text-[0.84rem] font-medium transition-colors',
                level === l
                  ? 'border-plum-700 bg-plum-700 text-white dark:border-primary dark:bg-primary dark:text-primary-foreground'
                  : 'border-border bg-card text-muted-foreground hover:text-foreground',
              )}
            >
              {l}
            </button>
          ))}
        </div>

        {isLoading && (
          <div className="mt-8 space-y-3">
            {Array.from({ length: 4 }).map((_, i) => (
              <Skeleton key={i} className="h-24" />
            ))}
          </div>
        )}
        {!isLoading && (
        <ul className="mt-8 divide-y divide-border border-y border-border">
          {visible.map((job) => (
            <li key={job.id} className="py-5">
              <div className="flex flex-wrap items-start justify-between gap-3">
                <div className="min-w-0">
                  <h2 className="text-[1.1rem] leading-snug">{job.title}</h2>
                  <p className="mt-1 text-[0.88rem] text-accent-foreground">
                    {job.organisation} · {job.location}
                  </p>
                </div>
                <div className="flex shrink-0 flex-wrap gap-2">
                  <StatusTag tone="neutral">{job.type}</StatusTag>
                  <StatusTag tone="gold">{job.level}</StatusTag>
                </div>
              </div>
              <div
                className="prose prose-sm mt-3 max-w-[70ch] text-[0.9rem] leading-relaxed text-muted-foreground prose-p:my-1"
                dangerouslySetInnerHTML={{ __html: job.summary }}
              />
              <p className="tnum mt-3 text-[0.8rem] text-muted-foreground">
                Posted {formatShortDate(job.postedAt)} · closes {formatShortDate(job.closesAt)}
              </p>
            </li>
          ))}
        </ul>
        )}
      </Section>

      <Section tone="tinted">
        <SectionHeading
          title="Hiring, and want a chapter member?"
          lede="Send the role to the Mentorship and Career committee. Listings are free for members and partner organisations."
          action={
            <Button asChild>
              <Link to="/contact">Submit a role</Link>
            </Button>
          }
        />
      </Section>
    </>
  )
}
