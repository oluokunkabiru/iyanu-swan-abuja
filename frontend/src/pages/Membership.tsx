import { Link } from 'react-router-dom'
import { getMembershipLevels } from '@/api/content'
import { PageHeader, Section, SectionHeading } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { useSettings } from '@/context/SettingsContext'
import { useApiData } from '@/hooks/useApiData'
import { formatNaira } from '@/lib/format'
import type { MembershipLevel } from '@/types'

export default function Membership() {
  const { settings, isLoading } = useSettings()
  const { data: levels } = useApiData(getMembershipLevels, [] as MembershipLevel[])
  const registrationSteps = settings?.registrationSteps ?? []
  const memberBenefits = settings?.memberBenefits ?? []

  const cheapestTotal = levels.length
    ? Math.min(...levels.map((l) => l.subscriptionAmount + l.welfareAmount))
    : null

  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'Membership' }]}
        title="Membership"
        intro="Every female member of ICAN is already a member of SWAN. Becoming active in the Abuja Chapter means paying your dues, getting confirmed, and turning up."
        aside={
          cheapestTotal !== null && (
            <div className="border border-gold-500/40 bg-plum-800/60 p-5 text-plum-200">
              <p className="text-[0.8rem]">Annual dues from</p>
              <p className="tnum mt-1 font-heading text-3xl text-gold-300">
                {formatNaira(cheapestTotal)}
              </p>
              <p className="mt-1 text-[0.78rem]">
                Subscription and welfare levy — the amount depends on the membership level you choose
              </p>
            </div>
          )
        }
      />

      <Section>
        <div className="grid gap-12 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
          <div>
            <SectionHeading title="Who is eligible" />
            <div className="mt-6 max-w-[68ch] space-y-4 text-[0.98rem] leading-relaxed">
              <p>
                Every female member of the Institute of Chartered Accountants of Nigeria is
                automatically a member of the Society of Women Accountants of Nigeria. There is no
                separate admission examination and no sponsorship requirement.
              </p>
              <p>
                What the chapter maintains is a roll of active members — those whose subscription
                and welfare levy are current for the year. Active status is what unlocks member
                event rates, eligibility for committee and executive office, and access to the
                welfare fund.
              </p>
              <p>
                Student accountants and members of the public are welcome at open lectures and
                community events. They register at the non-member rate.
              </p>
            </div>
          </div>

          <aside id="welfare" className="scroll-mt-24 border border-border bg-card p-6">
            <h2 className="text-[1.05rem]">The welfare fund</h2>
            <p className="mt-3 text-[0.9rem] leading-relaxed text-muted-foreground">
              The welfare levy included in every membership level is not an administrative charge.
              It funds the chapter response when a member faces bereavement, illness or another
              major life event, and it pays for the community outreach the chapter commits to each
              year.
            </p>
            <p className="mt-3 text-[0.9rem] leading-relaxed text-muted-foreground">
              The Welfare Officer administers claims and the fund is accounted for in the annual
              statements presented to members.
            </p>
            <Button variant="outline" asChild className="mt-5 w-full">
              <Link to="/resources">Read the welfare policy</Link>
            </Button>
          </aside>
        </div>
      </Section>

      <Section tone="tinted">
        <SectionHeading
          title="How to register"
          lede="Three steps. Most members complete the whole thing inside a fortnight."
          className="mb-10"
        />
        {isLoading ? (
          <div className="grid gap-px bg-border md:grid-cols-3">
            {Array.from({ length: 3 }).map((_, i) => (
              <Skeleton key={i} className="h-40" />
            ))}
          </div>
        ) : (
          <ol className="grid gap-px bg-border md:grid-cols-3">
            {registrationSteps.map((s) => (
              <li key={s.step} className="bg-card p-6">
                <span className="tnum font-heading text-3xl leading-none text-plum-200 dark:text-plum-500">
                  {String(s.step).padStart(2, '0')}
                </span>
                <h3 className="mt-4 text-[1.1rem]">{s.title}</h3>
                <p className="mt-2 text-[0.9rem] leading-relaxed text-muted-foreground">
                  {s.description}
                </p>
              </li>
            ))}
          </ol>
        )}
        <Button size="lg" asChild className="mt-8">
          <Link to="/membership/register">Start your registration</Link>
        </Button>
      </Section>

      <Section>
        <SectionHeading
          id="benefits"
          title="What active membership gets you"
          className="mb-8"
        />
        {isLoading ? (
          <div className="grid gap-px bg-border sm:grid-cols-2 lg:grid-cols-3">
            {Array.from({ length: 6 }).map((_, i) => (
              <Skeleton key={i} className="h-28" />
            ))}
          </div>
        ) : (
          <dl className="grid gap-px bg-border sm:grid-cols-2 lg:grid-cols-3">
            {memberBenefits.map((b) => (
              <div key={b.title} className="bg-card p-6">
                <dt className="text-[1.05rem] font-semibold">{b.title}</dt>
                <dd className="mt-2 text-[0.9rem] leading-relaxed text-muted-foreground">
                  {b.description}
                </dd>
              </div>
            ))}
          </dl>
        )}
      </Section>
    </>
  )
}
