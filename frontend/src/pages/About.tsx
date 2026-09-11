import { Link } from 'react-router-dom'
import { getCoreValues, getPartners } from '@/api/content'
import { PageHeader, Section, SectionHeading, Stat } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { useSettings } from '@/context/SettingsContext'
import { useApiData } from '@/hooks/useApiData'
import type { CoreValue, Partner } from '@/types'

export default function About() {
  const { settings, isLoading: loadingSettings } = useSettings()
  const { data: coreValues, isLoading: loadingCoreValues } = useApiData(getCoreValues, [] as CoreValue[])
  const { data: partners, isLoading: loadingPartners } = useApiData(getPartners, [] as Partner[])
  const chapterStats = settings?.chapterStats ?? []
  const aimsAndObjectives = settings?.aimsAndObjectives ?? []

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'About' }]}
        title="About the chapter"
        intro="The Society of Women Accountants of Nigeria is a national body dedicated to serving all female members of the Institute of Chartered Accountants of Nigeria. The Abuja Chapter carries that work across the Federal Capital Territory."
      />

      <Section>
        <div className="grid gap-12 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]">
          <div>
            <SectionHeading title="Who we are" />
            <div className="mt-6 max-w-[68ch] space-y-4 text-[0.98rem] leading-relaxed">
              <p>
                Membership of SWAN is not something you apply for in the usual sense. Every female
                member of ICAN is already a member of the Society. What the Abuja Chapter offers is
                the local structure that makes the membership worth something day to day: a
                calendar of accredited technical sessions, a mentorship programme, a welfare fund,
                and a body of women who will take your call.
              </p>
              <p>{settings?.aims}</p>
              <p>
                The chapter is run by an elected council of twelve, supported by eight standing
                committees. Accounts are presented to members annually, and outreach spending is
                published in full because members fund it directly.
              </p>
            </div>
          </div>

          <div className="space-y-8">
            {loadingSettings
              ? Array.from({ length: 4 }).map((_, i) => <Skeleton key={i} className="h-16" />)
              : chapterStats.map((s) => (
                  <Stat key={s.label} value={s.value} label={s.label} note={s.note} />
                ))}
          </div>
        </div>
      </Section>

      <Section tone="tinted">
        <div id="vision" className="grid gap-12 scroll-mt-24 lg:grid-cols-2">
          <div>
            <SectionHeading title="Vision and mission" />
            <div className="mt-6 space-y-6">
              <blockquote className="border-l-2 border-gold-500 pl-5 font-heading text-[1.2rem] leading-relaxed">
                {settings?.vision}
              </blockquote>
              <div>
                <h3 className="text-[1.05rem] text-plum-700 dark:text-primary">Our mission</h3>
                <ul className="mt-3 space-y-3">
                  {(settings?.mission ?? []).map((m) => (
                    <li key={m} className="text-[0.95rem] leading-relaxed text-muted-foreground">
                      {m}
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          </div>

          <div>
            <SectionHeading title="Aims and objectives" />
            <ol className="mt-6 divide-y divide-border border-y border-border">
              {aimsAndObjectives.map((aim) => (
                <li key={aim} className="py-3.5 text-[0.93rem] leading-relaxed">
                  {aim}
                </li>
              ))}
            </ol>
          </div>
        </div>
      </Section>

      <Section>
        <SectionHeading title="Core values" lede="Five commitments the chapter is measured against." className="mb-8" />
        {loadingCoreValues ? (
          <div className="grid gap-px bg-border sm:grid-cols-2 lg:grid-cols-3">
            {Array.from({ length: 5 }).map((_, i) => (
              <Skeleton key={i} className="h-24" />
            ))}
          </div>
        ) : (
          <dl className="grid gap-px bg-border sm:grid-cols-2 lg:grid-cols-3">
            {coreValues.map((v) => (
              <div key={v.title} className="bg-card p-6">
                <dt className="font-heading text-[1.15rem] text-accent-foreground">{v.title}</dt>
                <dd className="mt-2 text-[0.9rem] leading-relaxed text-muted-foreground">
                  {v.description}
                </dd>
              </div>
            ))}
          </dl>
        )}
      </Section>

      <Section tone="tinted">
        <div id="ican" className="grid gap-12 scroll-mt-24 lg:grid-cols-2">
          <div>
            <SectionHeading title="Our relationship with ICAN" />
            <div className="mt-6 max-w-[62ch] space-y-4 text-[0.96rem] leading-relaxed">
              <p>
                SWAN exists to support ICAN in safeguarding its Charter, the standing of the
                profession, and the interests of its female members. The chapter is not a separate
                qualification body and does not examine or admit members — that remains with the
                Institute.
              </p>
              <p>
                In practice the relationship runs two ways. Chapter technical sessions are
                accredited for ICAN Continuing Professional Development, and chapter positions on
                exposure drafts are submitted through the Technical and Education directorate.
              </p>
            </div>
            <Button variant="outline" asChild className="mt-6">
              <Link to="/membership">Become a SWAN member</Link>
            </Button>
          </div>

          <div id="partners" className="scroll-mt-24">
            <SectionHeading title="Affiliates and partners" />
            {loadingPartners ? (
              <div className="mt-6 space-y-3">
                {Array.from({ length: 4 }).map((_, i) => (
                  <Skeleton key={i} className="h-10" />
                ))}
              </div>
            ) : (
              <ul className="mt-6 divide-y divide-border border-y border-border">
                {partners.map((p) => (
                  <li key={p.id} className="py-3.5">
                    <a href={p.url} target="_blank" rel="noreferrer" className="group block">
                      <span className="text-[0.76rem] font-semibold text-accent-foreground">{p.scope}</span>
                      <span className="mt-0.5 block text-[0.93rem] group-hover:text-plum-700 dark:group-hover:text-primary">
                        {p.name}
                      </span>
                    </a>
                  </li>
                ))}
              </ul>
            )}
          </div>
        </div>
      </Section>

      <Section>
        <div className="flex flex-wrap items-center justify-between gap-6 border border-border bg-card p-8">
          <div>
            <h2 className="text-xl">Ready to become active in the chapter?</h2>
            <p className="mt-2 max-w-lg text-[0.93rem] text-muted-foreground">
              Pay your dues, get confirmed, and attend a meeting. That is the whole process.
            </p>
          </div>
          <Button size="lg" asChild>
            <Link to="/membership/register">Start your registration</Link>
          </Button>
        </div>
      </Section>
    </>
  )
}
