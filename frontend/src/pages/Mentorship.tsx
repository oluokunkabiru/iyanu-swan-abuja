import { Link } from 'react-router-dom'
import { PageHeader, Section, SectionHeading, Stat } from '@/components/common/Primitives'
import { Button } from '@/components/ui/button'

const expectations = [
  {
    party: 'What a mentor commits to',
    items: [
      'Six documented sessions across the twelve months',
      'A first meeting within three weeks of matching',
      'Honest feedback on career decisions, including the ones you would rather not hear',
      'A midpoint note to the Membership Secretary confirming the pairing is working',
    ],
  },
  {
    party: 'What a mentee commits to',
    items: [
      'Setting the agenda for each session — the mentor is not there to improvise',
      'Coming with a specific question rather than a general request for advice',
      'Following through on what was agreed, or explaining why not',
      'Saying early if the pairing is not right, so it can be changed',
    ],
  },
]

export default function Mentorship() {
  return (
    <>
      <PageHeader
        breadcrumb={[{ label: 'Home', to: '/' }, { label: 'Mentorship' }]}
        title="Mentorship programme"
        intro="Forty pairings in the current cohort, matched on sector and specialisation rather than seniority alone. Applications for the next cohort open in January."
      />

      <Section>
        <div className="grid gap-8 sm:grid-cols-3">
          <Stat value="40" label="Active pairings" note="Cohort two, running to December" />
          <Stat value="6" label="Sessions per pairing" note="Documented across twelve months" />
          <Stat value="12" label="Months per cohort" note="With a midpoint review and rematch option" />
        </div>
      </Section>

      <Section tone="tinted">
        <div className="grid gap-12 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)]">
          <div>
            <SectionHeading title="How matching works" />
            <div className="mt-6 max-w-[68ch] space-y-4 text-[0.96rem] leading-relaxed">
              <p>
                Pairings are made on sector and specialisation first. A newly inducted member
                moving into forensic work is matched with someone doing that work now, not with
                whoever has the most years.
              </p>
              <p>
                Each pair sets its own rhythm inside a light structure the chapter provides. The
                Membership Secretary reviews progress at the midpoint and rematches where a
                pairing has not taken. Nobody is asked to explain why — some pairings simply do
                not fit.
              </p>
              <p>
                The programme is open to any member on the active roll. Mentors are drawn from
                members with at least eight years post-qualification experience.
              </p>
            </div>
            <div className="mt-8 flex flex-wrap gap-3">
              <Button asChild>
                <Link to="/contact">Apply to the next cohort</Link>
              </Button>
              <Button variant="outline" asChild>
                <Link to="/resources">Download the programme pack</Link>
              </Button>
            </div>
          </div>

          <div className="space-y-6">
            {expectations.map((block) => (
              <div key={block.party} className="border border-border bg-card p-6">
                <h2 className="text-[1.05rem]">{block.party}</h2>
                <ul className="mt-4 space-y-2.5">
                  {block.items.map((item) => (
                    <li
                      key={item}
                      className="border-l-2 border-gold-500 pl-4 text-[0.88rem] leading-relaxed text-muted-foreground"
                    >
                      {item}
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
        </div>
      </Section>
    </>
  )
}
