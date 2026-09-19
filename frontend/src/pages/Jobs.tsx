import { useMemo, useState } from 'react'
import { Link } from 'react-router-dom'
import { ArrowUpRight } from 'lucide-react'
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
  const { data: closedJobs, isLoading: loadingClosedJobs } = useApiData(
    () => getJobs('closed'),
    [] as JobListing[],
  )
  const visible = useMemo(
    () => (level === 'All' ? jobs : jobs.filter((j) => j.level === level)),
    [jobs, level],
  )
  const closedVisible = useMemo(
    () => (level === 'All' ? closedJobs : closedJobs.filter((j) => j.level === level)),
    [closedJobs, level],
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
                  <h2 className="text-[1.1rem] leading-snug">
                    {job.applicationUrl ? (
                      <a href={job.applicationUrl} target="_blank" rel="noreferrer" className="hover:text-plum-700 dark:hover:text-primary">
                        {job.title}
                      </a>
                    ) : job.title}
                  </h2>
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
              {job.applicationUrl && (
                <a
                  href={job.applicationUrl}
                  target="_blank"
                  rel="noreferrer"
                  className="mt-4 inline-flex items-center gap-1 border-b border-plum-700 pb-0.5 text-[0.88rem] font-semibold text-plum-700 hover:text-foreground dark:border-primary dark:text-primary dark:hover:text-foreground"
                >
                  View application <ArrowUpRight aria-hidden="true" className="h-4 w-4" />
                </a>
              )}
            </li>
          ))}
        </ul>
        )}
      </Section>

      {!loadingClosedJobs && closedVisible.length > 0 && (
        <Section tone="tinted">
          <SectionHeading
            title="Closed roles"
            lede={`${closedVisible.length} past listing${closedVisible.length === 1 ? '' : 's'}.`}
            className="mb-6"
          />
          <ul className="divide-y divide-border border-y border-border">
            {closedVisible.map((job) => (
              <li key={job.id} className="py-5">
                <div className="flex flex-wrap items-start justify-between gap-3">
                  <div className="min-w-0">
                    <h2 className="text-[1.1rem] leading-snug">{job.title}</h2>
                    <p className="mt-1 text-[0.88rem] text-accent-foreground">
                      {job.organisation} · {job.location}
                    </p>
                  </div>
                  <StatusTag tone="neutral">Closed</StatusTag>
                </div>
                <p className="tnum mt-3 text-[0.8rem] text-muted-foreground">
                  Closed {formatShortDate(job.closesAt)}
                </p>
              </li>
            ))}
          </ul>
        </Section>
      )}

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
