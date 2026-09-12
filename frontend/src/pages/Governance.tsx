import { getExecutives, getPastChairpersons } from '@/api/content'
import { LazyImage } from '@/components/common/LazyImage'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Skeleton } from '@/components/ui/skeleton'
import { useApiData } from '@/hooks/useApiData'
import type { ExecutiveMember } from '@/types'

export default function Governance() {
  const { data: executives, isLoading } = useApiData(getExecutives, [] as ExecutiveMember[])
  const { data: pastChairpersons, isLoading: loadingPastChairpersons } = useApiData(
    getPastChairpersons,
    [] as ExecutiveMember[],
  )
  const principals = executives.filter((e) => e.isPrincipal)
  const others = executives.filter((e) => !e.isPrincipal)

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'About', to: '/about' }, { label: 'Governance' }]}
        title="Governance and the chapter council"
        intro="The council is elected by financial members at the annual general meeting and serves a one-year session. Its duties, the election procedure and the standing rules are set out in the chapter constitution."
      />

      <Section>
        <SectionHeading
          title="How the chapter is governed"
          className="mb-8"
        />
        <div className="grid gap-8 md:grid-cols-3">
          <div className="border-l-2 border-gold-500 pl-5">
            <h3 className="text-[1.05rem]">The council</h3>
            <p className="mt-2 text-[0.9rem] leading-relaxed text-muted-foreground">
              Twelve elected officers, including five principal officers. The council sets the
              annual programme, approves the budget, and answers to members at the general meeting.
            </p>
          </div>
          <div className="border-l-2 border-gold-500 pl-5">
            <h3 className="text-[1.05rem]">Committees</h3>
            <p className="mt-2 text-[0.9rem] leading-relaxed text-muted-foreground">
              Eight standing committees carry the working load. Any financial member may serve;
              places are confirmed by council at the start of each session.
            </p>
          </div>
          <div className="border-l-2 border-gold-500 pl-5">
            <h3 className="text-[1.05rem]">Elections</h3>
            <p className="mt-2 text-[0.9rem] leading-relaxed text-muted-foreground">
              Nominations open eight weeks before the annual general meeting. Only members whose
              dues are current may nominate, stand or vote. The Immediate Past Chairperson chairs
              the elections committee.
            </p>
          </div>
        </div>
      </Section>

      <Section tone="tinted">
        <SectionHeading
          id="executives"
          title="Principal officers"
          lede="The offices with day-to-day responsibility for the chapter."
          className="mb-8"
        />
        <ul className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {isLoading
            ? Array.from({ length: 3 }).map((_, i) => <Skeleton key={i} className="h-32" />)
            : principals.map((exec) => (
            <li key={exec.id} className="flex gap-4 border border-border bg-card p-5">
              {exec.photoUrl && (
                <LazyImage
                  src={exec.photoUrl}
                  alt=""
                  className="h-20 w-20 shrink-0 rounded-sm object-cover object-top"
                />
              )}
              <div className="min-w-0">
                <h3 className="text-[1rem] leading-snug">
                  {exec.name}, {exec.credential}
                </h3>
                <p className="mt-1 text-[0.8rem] font-semibold text-accent-foreground">{exec.position}</p>
                {exec.bio && (
                  <div
                    className="prose prose-sm mt-2 text-[0.84rem] leading-relaxed text-muted-foreground prose-p:my-1"
                    dangerouslySetInnerHTML={{ __html: exec.bio }}
                  />
                )}
              </div>
            </li>
          ))}
        </ul>
      </Section>

      <Section>
        <SectionHeading title="Other members of council" className="mb-8" />
        <ul className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {!isLoading && others.map((exec) => (
            <li key={exec.id} className="flex gap-4 border border-border bg-card p-5">
              {exec.photoUrl && (
                <LazyImage
                  src={exec.photoUrl}
                  alt=""
                  className="h-16 w-16 shrink-0 rounded-sm object-cover object-top"
                />
              )}
              <div className="min-w-0">
                <h3 className="text-[0.98rem] leading-snug">
                  {exec.name}, {exec.credential}
                </h3>
                <p className="mt-1 text-[0.78rem] font-semibold text-accent-foreground">{exec.position}</p>
                <p className="mt-2 text-[0.82rem] leading-relaxed text-muted-foreground">
                  {exec.bio}
                </p>
              </div>
            </li>
          ))}
        </ul>
      </Section>

      {(loadingPastChairpersons || pastChairpersons.length > 0) && (
        <Section tone="tinted">
          <SectionHeading
            title="Past chairpersons"
            lede="Women who have led the chapter before."
            className="mb-8"
          />
          <ul className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {loadingPastChairpersons
              ? Array.from({ length: 2 }).map((_, i) => <Skeleton key={i} className="h-24" />)
              : pastChairpersons.map((exec) => (
                  <li key={exec.id} className="flex gap-4 border border-border bg-card p-5">
                    {exec.photoUrl && (
                      <LazyImage
                        src={exec.photoUrl}
                        alt=""
                        className="h-16 w-16 shrink-0 rounded-sm object-cover object-top"
                      />
                    )}
                    <div className="min-w-0">
                      <h3 className="text-[0.98rem] leading-snug">
                        {exec.name}, {exec.credential}
                      </h3>
                      <p className="mt-1 text-[0.78rem] font-semibold text-accent-foreground">
                        {exec.termStartYear && exec.termEndYear
                          ? `Chairperson, ${exec.termStartYear}–${exec.termEndYear}`
                          : 'Chairperson'}
                      </p>
                    </div>
                  </li>
                ))}
          </ul>
        </Section>
      )}
    </>
  )
}
