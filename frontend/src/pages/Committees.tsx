import { Link, useParams } from 'react-router-dom'
import { EmptyState, PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { committees, findCommittee } from '@/data'

export default function Committees() {
  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'Committees' }]}
        title="Standing committees"
        intro="Eight committees carry the working load of the chapter. Any financial member may serve — places are confirmed by council at the start of each session."
      />

      <Section>
        <SectionHeading
          title="The committees"
          lede="Each committee sets its own work plan within the remit council gives it."
          className="mb-8"
        />
        <ul className="grid gap-px bg-border md:grid-cols-2">
          {committees.map((c) => (
            <li key={c.id} className="bg-card p-6">
              <h2 className="text-[1.1rem]">
                <Link to={`/committees/${c.slug}`} className="hover:text-plum-700 dark:hover:text-primary">
                  {c.name}
                </Link>
              </h2>
              <p className="mt-1 text-[0.8rem] font-semibold text-accent-foreground">Chair: {c.chair}</p>
              <p className="mt-3 text-[0.9rem] leading-relaxed text-muted-foreground">{c.remit}</p>
              <p className="mt-4 text-[0.8rem] text-muted-foreground">Meets {c.meetingCadence.toLowerCase()}</p>
            </li>
          ))}
        </ul>
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
  const committee = slug ? findCommittee(slug) : undefined

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
        title={committee.name}
        intro={committee.remit}
      />

      <Section>
        <div className="grid gap-12 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
          <div>
            <SectionHeading title="Focus areas" />
            <ul className="mt-6 divide-y divide-border border-y border-border">
              {committee.focusAreas.map((area) => (
                <li key={area} className="py-3.5 text-[0.95rem]">
                  {area}
                </li>
              ))}
            </ul>
          </div>

          <aside className="border border-border bg-card p-6">
            <h2 className="text-[1.05rem]">Committee details</h2>
            <dl className="mt-4 space-y-4 text-[0.88rem]">
              <div>
                <dt className="text-muted-foreground">Chair</dt>
                <dd className="mt-0.5 font-medium">{committee.chair}</dd>
              </div>
              <div>
                <dt className="text-muted-foreground">Meeting cadence</dt>
                <dd className="mt-0.5 font-medium">{committee.meetingCadence}</dd>
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
