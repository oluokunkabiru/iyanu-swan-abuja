import type { ReactNode } from 'react'
import { Link, useParams } from 'react-router-dom'
import { motion } from 'framer-motion'
import { getCommittee, getCommittees } from '@/api/content'
import { EmptyState, PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { useApiData } from '@/hooks/useApiData'
import { containerVariants, itemVariants } from '@/lib/animations'
import type { Committee } from '@/types'

export default function Committees() {
  const { data: committees, isLoading } = useApiData(getCommittees, [] as Committee[])

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'Committees' }]}
        title="Standing committees"
        intro="Ten committees carry the working load of the chapter, each under a written mandate. Any financial member may serve — places are confirmed by council at the start of each session."
      />

      <Section>
        <SectionHeading
          title="The committees"
          lede="Each mandate sets out the committee's purpose, objectives, membership, meetings and reporting line."
          className="mb-8"
        />
        {isLoading ? (
          <div className="grid gap-px bg-border md:grid-cols-2">
            {Array.from({ length: 4 }).map((_, i) => (
              <Skeleton key={i} className="h-40" />
            ))}
          </div>
        ) : (
          <motion.ul
            className="grid gap-px bg-border md:grid-cols-2"
            initial="initial"
            whileInView="animate"
            viewport={{ once: true, amount: 0.2 }}
            variants={containerVariants}
          >
            {committees.map((c) => (
              <motion.li key={c.id} className="bg-card p-6" variants={itemVariants}
                whileHover="hover"
              >
                <h2 className="text-[1.1rem]">
                  <Link to={`/committees/${c.slug}`} className="hover:text-plum-700 dark:hover:text-primary">
                    {c.name}
                  </Link>
                </h2>
                {c.subtitle && (
                  <p className="mt-1 text-[0.8rem] font-semibold text-accent-foreground">{c.subtitle}</p>
                )}
                <p className="mt-3 line-clamp-4 text-[0.9rem] leading-relaxed text-muted-foreground">{c.purpose}</p>
                <div className="mt-4 flex flex-wrap items-center justify-between gap-3 text-[0.8rem]">
                  <span className="text-muted-foreground">{c.objectives.length} objectives</span>
                  <Link to={`/committees/${c.slug}`} className="font-semibold hover:text-plum-700 dark:hover:text-primary">
                    Read the mandate →
                  </Link>
                </div>
              </motion.li>
            ))}
          </motion.ul>
        )}
      </Section>

      <Section tone="tinted">
        <div className="flex flex-wrap items-center justify-between gap-6">
          <div>
            <h2 className="text-xl">Interested in serving?</h2>
            <p className="mt-2 max-w-xl text-[0.93rem] text-muted-foreground">
              Committee service is open to any member whose dues are current. Write to the General
              Secretary with the committee you would like to join and a short note on what you
              would bring to it.
            </p>
          </div>
          <Button asChild>
            <Link to="/contact">Write to the Secretary</Link>
          </Button>
        </div>
      </Section>
    </>
  )
}

export function CommitteeDetail() {
  const { slug } = useParams<{ slug: string }>()
  const { data: committee, isLoading } = useApiData(
    () => (slug ? getCommittee(slug) : Promise.resolve(null)),
    null as Committee | null,
    [slug],
  )

  if (isLoading) {
    return (
      <Section>
        <Skeleton className="h-10 w-2/3" />
        <Skeleton className="mt-8 h-64" />
      </Section>
    )
  }

  if (!committee) {
    return (
      <Section>
        <EmptyState
          title="Committee not found"
          body="That committee is not on the current list. It may have been merged or renamed."
          action={
            <Button variant="outline" asChild>
              <Link to="/committees">Back to committees</Link>
            </Button>
          }
        />
      </Section>
    )
  }

  return (
    <>
      <PageHeader
        breadcrumb={[
          { label: 'Home', to: '/' },
          { label: 'Committees', to: '/committees' },
          { label: committee.name },
        ]}
        title={`Mandate of the ${committee.name}`}
        intro={committee.subtitle ?? undefined}
      />

      <Section>
        <div className="grid gap-12 lg:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]">
          <article className="space-y-10">
            <MandateClause title="Purpose">
              <p>{committee.purpose}</p>
            </MandateClause>

            <MandateClause title="Objectives">
              <ol className="divide-y divide-border border-y border-border">
                {committee.objectives.map((objective, i) => (
                  <li key={i} className="flex gap-4 py-3.5">
                    <span className="w-6 shrink-0 font-semibold tabular-nums text-accent-foreground">{i + 1}.</span>
                    <span>{objective}</span>
                  </li>
                ))}
              </ol>
            </MandateClause>

            <MandateClause title="Structure & membership">
              <p>{committee.structure}</p>
            </MandateClause>

            <MandateClause title="Meetings">
              <p>{committee.meetings}</p>
            </MandateClause>

            <MandateClause title="Reporting">
              <p>{committee.reporting}</p>
            </MandateClause>

            {committee.note && (
              <p className="border-l-2 border-gold-500 bg-muted/40 px-5 py-4 text-[0.95rem] font-semibold leading-relaxed">
                {committee.note}
              </p>
            )}
          </article>

          <aside className="h-fit border border-border bg-card p-6 lg:sticky lg:top-24">
            <h2 className="text-[1.05rem]">Committee details</h2>
            <dl className="mt-4 space-y-4 text-[0.88rem]">
              <div>
                <dt className="text-muted-foreground">Chair</dt>
                <dd className="mt-0.5 font-medium">{committee.chair ?? 'To be appointed by the executive'}</dd>
              </div>
              <div>
                <dt className="text-muted-foreground">Objectives</dt>
                <dd className="mt-0.5 font-medium">{committee.objectives.length}</dd>
              </div>
              <div>
                <dt className="text-muted-foreground">Open to</dt>
                <dd className="mt-0.5 font-medium">Any member with current dues</dd>
              </div>
            </dl>
            <Button asChild className="mt-6 w-full">
              <Link to="/contact">Ask to join</Link>
            </Button>
          </aside>
        </div>
      </Section>
    </>
  )
}

function MandateClause({ title, children }: { title: string; children: ReactNode }) {
  return (
    <section>
      <SectionHeading title={title} />
      <div className="mt-5 text-[0.95rem] leading-relaxed">{children}</div>
    </section>
  )
}
